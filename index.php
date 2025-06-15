<?php
session_start();

require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in or has valid remember token
if (!isset($_SESSION['user_id'])) {
    if (!checkRememberToken($pdo)) {
        header('Location: landing.php');
        exit();
    }
}

// Get dashboard data
$stats = getDashboardStats($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="asset/logo_new.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="min-h-screen pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"> <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']); ?>! Here's what's happening with your business.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Orders -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <i data-feather="shopping-cart" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Today's Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['orders_today']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- This Month's Orders -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <i data-feather="calendar" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['orders_month']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_orders']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Products -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <i data-feather="package" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_products']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders & Top Products -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($stats['recent_orders'])): ?>
                            <p class="text-gray-500 text-center py-8">No recent orders</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($stats['recent_orders'] as $order): ?>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['product_title']); ?></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            <?php echo getStatusColor($order['status']); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Top Products</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($stats['top_products'])): ?>
                            <p class="text-gray-500 text-center py-8">No products data</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($stats['top_products'] as $product): ?>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($product['title']); ?></p>
                                            <p class="text-sm text-gray-600">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900"><?php echo $product['order_count']; ?> orders</p>
                                            <p class="text-sm text-gray-600">Stock: <?php echo $product['stock']; ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>