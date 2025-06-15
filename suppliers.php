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
                $supplier_name = sanitizeInput($_POST['supplier_name']);
                $order_reference = sanitizeInput($_POST['order_reference']);
                $products = sanitizeInput($_POST['products']);
                $expected_delivery_date = $_POST['expected_delivery_date'];
                $status = sanitizeInput($_POST['status']);
                try {
                    $stmt = $pdo->prepare("INSERT INTO supplier_orders (user_id, supplier_name, order_reference, products, expected_delivery_date, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_SESSION['user_id'], $supplier_name, $order_reference, $products, $expected_delivery_date, $status]);
                    setFlashMessage('success', 'Supplier order added successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error adding supplier order: ' . $e->getMessage());
                }
                break;

            case 'edit':
                $id = intval($_POST['id']);
                $supplier_name = sanitizeInput($_POST['supplier_name']);
                $order_reference = sanitizeInput($_POST['order_reference']);
                $products = sanitizeInput($_POST['products']);
                $expected_delivery_date = $_POST['expected_delivery_date'];
                $status = sanitizeInput($_POST['status']);
                try {
                    $stmt = $pdo->prepare("UPDATE supplier_orders SET supplier_name=?, order_reference=?, products=?, expected_delivery_date=?, status=? WHERE id=? AND user_id=?");
                    $stmt->execute([$supplier_name, $order_reference, $products, $expected_delivery_date, $status, $id, $_SESSION['user_id']]);
                    setFlashMessage('success', 'Supplier order updated successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error updating supplier order: ' . $e->getMessage());
                }
                break;

            case 'delete':
                $id = intval($_POST['id']);
                try {
                    $stmt = $pdo->prepare("DELETE FROM supplier_orders WHERE id=? AND user_id=?");
                    $stmt->execute([$id, $_SESSION['user_id']]);
                    setFlashMessage('success', 'Supplier order deleted successfully!');
                } catch (PDOException $e) {
                    setFlashMessage('error', 'Error deleting supplier order: ' . $e->getMessage());
                }
                break;
        }
        header('Location: suppliers.php');
        exit();
    }
}

// Get all supplier orders for current user
$stmt = $pdo->prepare("SELECT * FROM supplier_orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$supplierOrders = $stmt->fetchAll();

// Get products for reference (only user's products)
$stmt = $pdo->prepare("SELECT id, title, sku FROM products WHERE user_id = ? ORDER BY title");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Supplier Orders</title>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Supplier Orders</h1>
                    <p class="text-gray-600 mt-2">Track orders placed with your suppliers</p>
                </div>
                <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition duration-150 ease-in-out">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                    New Supplier Order
                </button>
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
            <?php endif; ?>

            <!-- Supplier Orders Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">All Supplier Orders</h2>
                </div>

                <?php if (empty($supplierOrders)): ?>
                    <div class="text-center py-12">
                        <i data-feather="truck" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500 text-lg">No supplier orders yet</p>
                        <p class="text-gray-400 mt-2">Create your first supplier order to track purchases</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Delivery</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($supplierOrders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['supplier_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($order['order_reference'] ?: 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                                <?php echo htmlspecialchars($order['products']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $order['expected_delivery_date'] ? formatDate($order['expected_delivery_date']) : 'N/A'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                <?php
                                                switch ($order['status']) {
                                                    case 'Pending':
                                                        echo 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    case 'Confirmed':
                                                        echo 'bg-blue-100 text-blue-800';
                                                        break;
                                                    case 'Shipped':
                                                        echo 'bg-purple-100 text-purple-800';
                                                        break;
                                                    case 'Delivered':
                                                        echo 'bg-green-100 text-green-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-100 text-gray-800';
                                                }
                                                ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatDate($order['created_at']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="editSupplierOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)"
                                                    class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </button>
                                                <button onclick="deleteSupplierOrder(<?php echo $order['id']; ?>)"
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

    <!-- Add/Edit Supplier Order Modal -->
    <div id="supplierOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">New Supplier Order</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form id="supplierOrderForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="supplierOrderId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                        <input type="text" name="supplier_name" id="supplierName" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Reference</label>
                        <input type="text" name="order_reference" id="orderReference"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Optional order ID">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Products</label>
                    <textarea name="products" id="supplierProducts" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="List the products included in this order"></textarea>
                    <div class="mt-2">
                        <p class="text-xs text-gray-500 mb-2">Available products:</p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($products as $product): ?>
                                <button type="button"
                                    onclick="addProductToList('<?php echo htmlspecialchars($product['title']); ?> (<?php echo htmlspecialchars($product['sku']); ?>)')"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 transition duration-150 ease-in-out">
                                    <?php echo htmlspecialchars($product['title']); ?> (<?php echo htmlspecialchars($product['sku']); ?>)
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" id="expectedDeliveryDate"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="supplierOrderStatus" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                        </select>
                    </div>
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Supplier Order</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this supplier order? This action cannot be undone.</p>

                <form id="deleteForm" method="POST" class="flex justify-center space-x-3">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteSupplierOrderId">

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
            document.getElementById('modalTitle').textContent = 'New Supplier Order';
            document.getElementById('formAction').value = 'add';
            document.getElementById('supplierOrderForm').reset();
            document.getElementById('supplierOrderId').value = '';
            document.getElementById('supplierOrderModal').classList.remove('hidden');
        }

        function editSupplierOrder(order) {
            document.getElementById('modalTitle').textContent = 'Edit Supplier Order';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('supplierOrderId').value = order.id;
            document.getElementById('supplierName').value = order.supplier_name;
            document.getElementById('orderReference').value = order.order_reference || '';
            document.getElementById('supplierProducts').value = order.products;
            document.getElementById('expectedDeliveryDate').value = order.expected_delivery_date || '';
            document.getElementById('supplierOrderStatus').value = order.status;

            document.getElementById('supplierOrderModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('supplierOrderModal').classList.add('hidden');
        }

        function deleteSupplierOrder(orderId) {
            document.getElementById('deleteSupplierOrderId').value = orderId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function addProductToList(productName) {
            const productsTextarea = document.getElementById('supplierProducts');
            const currentValue = productsTextarea.value;

            if (currentValue) {
                productsTextarea.value = currentValue + ', ' + productName;
            } else {
                productsTextarea.value = productName;
            }
        }

        // Close modals when clicking outside
        document.getElementById('supplierOrderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>