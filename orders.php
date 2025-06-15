<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

requireAuth();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $customer_name = sanitizeInput($_POST['customer_name']);
                $phone_number = sanitizeInput($_POST['phone_number']);
                $address = sanitizeInput($_POST['address']);
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                $status = sanitizeInput($_POST['status']);
                try {
                    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, phone_number, address, product_id, quantity, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $customer_name, $phone_number, $address, $product_id, $quantity, $status]);

                    // Update product stock (ensure product belongs to user)
                    $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$quantity, $product_id, $_SESSION['user_id']]);

                    setFlashMessage('success', 'Order added successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error adding order: ' . $e->getMessage());
                }
                break;

            case 'edit':
                $id = intval($_POST['id']);
                $customer_name = sanitizeInput($_POST['customer_name']);
                $phone_number = sanitizeInput($_POST['phone_number']);
                $address = sanitizeInput($_POST['address']);
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);
                $status = sanitizeInput($_POST['status']);
                try {
                    // Get current order to calculate stock changes (ensure user owns the order)
                    $stmt = $pdo->prepare("SELECT product_id, quantity FROM orders WHERE id = ? AND user_id = ?");
                    $stmt->execute([$id, $_SESSION['user_id']]);
                    $currentOrder = $stmt->fetch();

                    // Update order (ensure user owns the order)
                    $stmt = $pdo->prepare("UPDATE orders SET customer_name=?, phone_number=?, address=?, product_id=?, quantity=?, status=? WHERE id=? AND user_id=?");
                    $stmt->execute([$customer_name, $phone_number, $address, $product_id, $quantity, $status, $id, $_SESSION['user_id']]);

                    // Adjust stock if product or quantity changed
                    if ($currentOrder) {
                        // Restore stock for old product/quantity (ensure user owns the product)
                        $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$currentOrder['quantity'], $currentOrder['product_id'], $_SESSION['user_id']]);

                        // Reduce stock for new product/quantity (ensure user owns the product)
                        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$quantity, $product_id, $_SESSION['user_id']]);
                    }

                    setFlashMessage('success', 'Order updated successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error updating order: ' . $e->getMessage());
                }
                break;

            case 'delete':
                $id = intval($_POST['id']);
                try {
                    // Get order details to restore stock (ensure user owns the order)
                    $stmt = $pdo->prepare("SELECT product_id, quantity FROM orders WHERE id = ? AND user_id = ?");
                    $stmt->execute([$id, $_SESSION['user_id']]);
                    $order = $stmt->fetch();

                    // Delete order (ensure user owns the order)
                    $stmt = $pdo->prepare("DELETE FROM orders WHERE id=? AND user_id=?");
                    $stmt->execute([$id, $_SESSION['user_id']]);

                    // Restore stock (ensure user owns the product)
                    if ($order) {
                        $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$order['quantity'], $order['product_id'], $_SESSION['user_id']]);
                    }

                    setFlashMessage('success', 'Order deleted successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error deleting order: ' . $e->getMessage());
                }
                break;
        }
        header('Location: orders.php');
        exit();
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$product_filter = isset($_GET['product']) ? intval($_GET['product']) : '';
$customer_filter = isset($_GET['customer']) ? sanitizeInput($_GET['customer']) : '';
$date_from = isset($_GET['date_from']) ? sanitizeInput($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitizeInput($_GET['date_to']) : '';
$sort_by = isset($_GET['sort_by']) ? sanitizeInput($_GET['sort_by']) : 'order_date';
$sort_order = isset($_GET['sort_order']) ? sanitizeInput($_GET['sort_order']) : 'DESC';

// Build WHERE clause for filtering
$whereConditions = ["o.user_id = ?"];
$params = [$_SESSION['user_id']];

if (!empty($status_filter)) {
    $whereConditions[] = "o.status = ?";
    $params[] = $status_filter;
}

if (!empty($product_filter)) {
    $whereConditions[] = "o.product_id = ?";
    $params[] = $product_filter;
}

if (!empty($customer_filter)) {
    $whereConditions[] = "o.customer_name LIKE ?";
    $params[] = "%$customer_filter%";
}

if (!empty($date_from)) {
    $whereConditions[] = "DATE(o.order_date) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $whereConditions[] = "DATE(o.order_date) <= ?";
    $params[] = $date_to;
}

// Validate sort parameters
$allowed_sort_columns = ['order_date', 'customer_name', 'product_title', 'status', 'quantity'];
$allowed_sort_orders = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'order_date';
}

if (!in_array($sort_order, $allowed_sort_orders)) {
    $sort_order = 'DESC';
}

// Get filtered orders with product details
$whereClause = implode(' AND ', $whereConditions);
$stmt = $pdo->prepare("
    SELECT o.*, p.title as product_title, p.sku, p.selling_price 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    WHERE $whereClause
    ORDER BY $sort_by $sort_order
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Get order statistics for dashboard
$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_orders,
        COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_orders,
        COUNT(CASE WHEN status = 'Ordered from supplier' THEN 1 END) as supplier_orders,
        COUNT(CASE WHEN status = 'Shipped' THEN 1 END) as shipped_orders,
        COUNT(CASE WHEN status = 'Delivered' THEN 1 END) as delivered_orders,
        SUM(o.quantity * p.selling_price) as total_revenue
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    WHERE $whereClause
");
$statsStmt->execute($params);
$orderStats = $statsStmt->fetch();

// Get all products for the dropdown (only user's products)
$stmt = $pdo->prepare("SELECT id, title, sku, stock FROM products WHERE user_id = ? ORDER BY title");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Get order for editing if edit ID is provided
$editOrder = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$editId, $_SESSION['user_id']]);
    $editOrder = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Orders</title>
    <link rel="icon" type="image/x-icon" href="asset/logo_new.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'includes/nav.php'; ?>

    <div class="min-h-screen pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"> <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
                    <p class="text-gray-600 mt-2">Manage customer orders and track fulfillment</p>
                </div>
                <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition duration-150 ease-in-out">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                    New Order
                </button>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i data-feather="filter" class="w-5 h-5 mr-2"></i>
                            Filters
                        </h3>
                        <button onclick="toggleFilters()" id="filter-toggle" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <span id="filter-toggle-text">Show Filters</span>
                            <i data-feather="chevron-down" class="w-4 h-4 inline ml-1" id="filter-chevron"></i>
                        </button>
                    </div>
                </div>

                <div id="filter-panel" class="px-6 py-4 border-b border-gray-200 hidden">
                    <form method="GET" action="orders.php" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Status</option>
                                    <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Ordered from supplier" <?php echo $status_filter === 'Ordered from supplier' ? 'selected' : ''; ?>>Ordered from supplier</option>
                                    <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </div>

                            <!-- Product Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                <select name="product" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Products</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" <?php echo $product_filter == $product['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product['title']); ?> (<?php echo htmlspecialchars($product['sku']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Customer Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                                <input type="text" name="customer" value="<?php echo htmlspecialchars($customer_filter); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Search customer...">
                            </div>

                            <!-- Sort By -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                                <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="order_date" <?php echo $sort_by === 'order_date' ? 'selected' : ''; ?>>Order Date</option>
                                    <option value="customer_name" <?php echo $sort_by === 'customer_name' ? 'selected' : ''; ?>>Customer Name</option>
                                    <option value="product_title" <?php echo $sort_by === 'product_title' ? 'selected' : ''; ?>>Product</option>
                                    <option value="status" <?php echo $sort_by === 'status' ? 'selected' : ''; ?>>Status</option>
                                    <option value="quantity" <?php echo $sort_by === 'quantity' ? 'selected' : ''; ?>>Quantity</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Date From -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                                <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                                <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <select name="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="DESC" <?php echo $sort_order === 'DESC' ? 'selected' : ''; ?>>Newest First</option>
                                    <option value="ASC" <?php echo $sort_order === 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pt-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition duration-150 ease-in-out">
                                <i data-feather="search" class="w-4 h-4 mr-2"></i>
                                Apply Filters
                            </button>
                            <a href="orders.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg flex items-center transition duration-150 ease-in-out">
                                <i data-feather="x" class="w-4 h-4 mr-2"></i>
                                Clear All
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Order Statistics -->
                <?php if ($orderStats && $orderStats['total_orders'] > 0): ?>
                    <div class="px-6 py-4 bg-gray-50">
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-gray-900"><?php echo number_format($orderStats['total_orders']); ?></div>
                                <div class="text-xs text-gray-500">Total Orders</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-yellow-600"><?php echo number_format($orderStats['pending_orders']); ?></div>
                                <div class="text-xs text-gray-500">Pending</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-600"><?php echo number_format($orderStats['supplier_orders']); ?></div>
                                <div class="text-xs text-gray-500">With Supplier</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600"><?php echo number_format($orderStats['shipped_orders']); ?></div>
                                <div class="text-xs text-gray-500">Shipped</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600"><?php echo number_format($orderStats['delivered_orders']); ?></div>
                                <div class="text-xs text-gray-500">Delivered</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-emerald-600">$<?php echo number_format($orderStats['total_revenue'], 2); ?></div>
                                <div class="text-xs text-gray-500">Total Revenue</div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Flash Messages -->
            <?php $flash = getFlashMessage(); ?>
            <?php if ($flash): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $flash['type'] == 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'; ?>">
                    <div class="flex">
                        <i data-feather="<?php echo $flash['type'] == 'success' ? 'check-circle' : 'alert-circle'; ?>"
                            class="w-5 h-5 <?php echo $flash['type'] == 'success' ? 'text-green-500' : 'text-red-500'; ?> mt-0.5 mr-3"></i>
                        <p class="<?php echo $flash['type'] == 'success' ? 'text-green-700' : 'text-red-700'; ?> text-sm">
                            <?php echo htmlspecialchars($flash['message']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?> <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                Orders
                                <?php if (!empty(array_filter([$status_filter, $product_filter, $customer_filter, $date_from, $date_to]))): ?>
                                    <span class="text-sm font-normal text-gray-500 ml-2">
                                        (<?php echo count($orders); ?> filtered results)
                                    </span>
                                <?php else: ?>
                                    <span class="text-sm font-normal text-gray-500 ml-2">
                                        (<?php echo count($orders); ?> total)
                                    </span>
                                <?php endif; ?>
                            </h2>
                        </div>

                        <!-- Quick Filter Buttons -->
                        <div class="flex items-center space-x-2">
                            <div class="flex space-x-1">
                                <button onclick="setStatusFilter('Pending')"
                                    class="px-3 py-1 text-xs font-medium rounded-full border transition-colors
                                    <?php echo $status_filter === 'Pending' ? 'bg-yellow-100 border-yellow-300 text-yellow-800' : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200'; ?>">
                                    Pending
                                </button>
                                <button onclick="setStatusFilter('Shipped')"
                                    class="px-3 py-1 text-xs font-medium rounded-full border transition-colors
                                    <?php echo $status_filter === 'Shipped' ? 'bg-purple-100 border-purple-300 text-purple-800' : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200'; ?>">
                                    Shipped
                                </button>
                                <button onclick="setDateFilter(7)"
                                    class="px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200">
                                    Last 7 Days
                                </button>
                                <button onclick="setDateFilter(30)"
                                    class="px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200">
                                    Last 30 Days
                                </button>
                            </div>
                        </div>
                    </div>
                </div> <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <i data-feather="shopping-cart" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <?php if (!empty(array_filter([$status_filter, $product_filter, $customer_filter, $date_from, $date_to]))): ?>
                            <p class="text-gray-500 text-lg">No orders match your filters</p>
                            <p class="text-gray-400 mt-2">Try adjusting your search criteria or clear all filters</p>
                            <div class="mt-4">
                                <a href="orders.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition duration-150 ease-in-out">
                                    <i data-feather="x" class="w-4 h-4 mr-2"></i>
                                    Clear Filters
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-lg">No orders yet</p>
                            <p class="text-gray-400 mt-2">Create your first order to get started</p>
                            <div class="mt-4">
                                <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition duration-150 ease-in-out">
                                    <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                    Add Order
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['phone_number']); ?></div>
                                                <div class="text-xs text-gray-400 mt-1"><?php echo htmlspecialchars(substr($order['address'], 0, 40)); ?>...</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['product_title']); ?></div>
                                                <div class="text-sm text-gray-500">SKU: <?php echo htmlspecialchars($order['sku']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $order['quantity']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo formatPrice($order['selling_price'] * $order['quantity']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo getStatusColor($order['status']); ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatDate($order['order_date']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)"
                                                    class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </button>
                                                <button onclick="deleteOrder(<?php echo $order['id']; ?>)"
                                                    class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out">
                                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Order Modal -->
    <div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">New Order</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form id="orderForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="orderId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                        <input type="text" name="customer_name" id="customerName" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone_number" id="phoneNumber" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" id="orderAddress" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <select name="product_id" id="productId" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>" data-stock="<?php echo $product['stock']; ?>">
                                    <?php echo htmlspecialchars($product['title']); ?> (<?php echo htmlspecialchars($product['sku']); ?>) - Stock: <?php echo $product['stock']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" id="orderQuantity" min="1" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="orderStatus" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Ordered from supplier">Ordered from supplier</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-150 ease-in-out">
                        <i data-feather="save" class="w-4 h-4 mr-2 inline"></i>
                        Save Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="text-center">
                <i data-feather="alert-triangle" class="w-16 h-16 text-red-500 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Order</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this order? This action cannot be undone.</p>

                <form id="deleteForm" method="POST" class="flex justify-center space-x-3">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteOrderId">

                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition duration-150 ease-in-out">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'New Order';
            document.getElementById('formAction').value = 'add';
            document.getElementById('orderForm').reset();
            document.getElementById('orderId').value = '';
            document.getElementById('orderQuantity').value = '1';
            document.getElementById('orderModal').classList.remove('hidden');
        }

        function editOrder(order) {
            document.getElementById('modalTitle').textContent = 'Edit Order';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('orderId').value = order.id;
            document.getElementById('customerName').value = order.customer_name;
            document.getElementById('phoneNumber').value = order.phone_number;
            document.getElementById('orderAddress').value = order.address;
            document.getElementById('productId').value = order.product_id;
            document.getElementById('orderQuantity').value = order.quantity;
            document.getElementById('orderStatus').value = order.status;

            document.getElementById('orderModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        function deleteOrder(orderId) {
            document.getElementById('deleteOrderId').value = orderId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Validate quantity against stock
        document.getElementById('productId').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.dataset.stock || 0;
            const quantityInput = document.getElementById('orderQuantity');
            quantityInput.max = stock;

            if (quantityInput.value > stock) {
                quantityInput.value = stock;
            }
        });

        // Close modals when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Filter functionality
        function toggleFilters() {
            const panel = document.getElementById('filter-panel');
            const toggleText = document.getElementById('filter-toggle-text');
            const chevron = document.getElementById('filter-chevron');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                toggleText.textContent = 'Hide Filters';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                panel.classList.add('hidden');
                toggleText.textContent = 'Show Filters';
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Auto-show filters if any filters are active
        function checkActiveFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('status') || urlParams.has('product') ||
                urlParams.has('customer') || urlParams.has('date_from') ||
                urlParams.has('date_to') || urlParams.get('sort_by') !== 'order_date' ||
                urlParams.get('sort_order') !== 'DESC';

            if (hasFilters) {
                const panel = document.getElementById('filter-panel');
                const toggleText = document.getElementById('filter-toggle-text');
                const chevron = document.getElementById('filter-chevron');

                panel.classList.remove('hidden');
                toggleText.textContent = 'Hide Filters';
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        // Quick filter buttons
        function setStatusFilter(status) {
            const url = new URL(window.location);
            url.searchParams.set('status', status);
            window.location.href = url.toString();
        }

        function setDateFilter(days) {
            const url = new URL(window.location);
            const today = new Date();
            const fromDate = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

            url.searchParams.set('date_from', fromDate.toISOString().split('T')[0]);
            url.searchParams.set('date_to', today.toISOString().split('T')[0]);
            window.location.href = url.toString();
        }

        // Initialize filters on page load
        // checkActiveFilters(); // Removed to keep filters hidden by default
    </script>
</body>

</html>