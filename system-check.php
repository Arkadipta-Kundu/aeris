<?php

/**
 * Aeris System Requirements Checker
 * This script helps verify that your server meets the requirements to run Aeris
 */

// Suppress warnings for cleaner output
error_reporting(E_ERROR | E_PARSE);

$requirements = [
    'php_version' => '7.4.0',
    'mysql_available' => true,
    'pdo_available' => true,
    'write_permissions' => true
];

// Check results
$checks = [];

// PHP Version
$php_version = phpversion();
$checks['php_version'] = [
    'name' => 'PHP Version',
    'required' => $requirements['php_version'] . '+',
    'current' => $php_version,
    'status' => version_compare($php_version, $requirements['php_version'], '>='),
    'message' => version_compare($php_version, $requirements['php_version'], '>=')
        ? 'Your PHP version is compatible'
        : 'Please upgrade to PHP ' . $requirements['php_version'] . ' or higher'
];

// MySQL/PDO MySQL
$checks['mysql'] = [
    'name' => 'MySQL Support',
    'required' => 'Available',
    'current' => extension_loaded('pdo_mysql') ? 'Available' : 'Not Available',
    'status' => extension_loaded('pdo_mysql'),
    'message' => extension_loaded('pdo_mysql')
        ? 'MySQL support is available'
        : 'PDO MySQL extension is required'
];

// PDO
$checks['pdo'] = [
    'name' => 'PDO Support',
    'required' => 'Available',
    'current' => extension_loaded('pdo') ? 'Available' : 'Not Available',
    'status' => extension_loaded('pdo'),
    'message' => extension_loaded('pdo')
        ? 'PDO is available'
        : 'PDO extension is required'
];

// File permissions (config directory)
$config_writable = is_writable('config/');
$checks['permissions'] = [
    'name' => 'File Permissions',
    'required' => 'Writable',
    'current' => $config_writable ? 'Writable' : 'Not Writable',
    'status' => $config_writable,
    'message' => $config_writable
        ? 'File permissions are correct'
        : 'Please ensure the config directory is writable'
];

// Database connection test (if config exists)
$db_status = false;
$db_message = 'Not tested - configure database first';
if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        if (isset($pdo)) {
            $db_status = true;
            $db_message = 'Database connection successful';
        }
    } catch (Exception $e) {
        $db_message = 'Database connection failed: ' . $e->getMessage();
    }
}

$checks['database'] = [
    'name' => 'Database Connection',
    'required' => 'Connected',
    'current' => $db_status ? 'Connected' : 'Failed',
    'status' => $db_status,
    'message' => $db_message
];

// Overall status
$all_passed = true;
foreach ($checks as $check) {
    if (!$check['status']) {
        $all_passed = false;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - System Check</title>
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

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="flex items-center justify-center mb-6">
                <img src="asset/aerislogoandtext.png" alt="Aeris System Check" class="h-16 mr-4">
            </div>
            <p class="text-xl text-gray-600">Verify your server is ready to run Aeris</p>
        </div>

        <!-- Overall Status -->
        <div class="mb-8">
            <?php if ($all_passed): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <i data-feather="check-circle" class="w-8 h-8 text-green-500 mr-4"></i>
                        <div>
                            <h2 class="text-xl font-semibold text-green-900">System Ready!</h2>
                            <p class="text-green-700">Your server meets all requirements to run Aeris.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <i data-feather="alert-circle" class="w-8 h-8 text-red-500 mr-4"></i>
                        <div>
                            <h2 class="text-xl font-semibold text-red-900">Requirements Not Met</h2>
                            <p class="text-red-700">Please address the issues below before installing Aeris.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Detailed Checks -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Detailed Requirements</h2>
            </div>

            <div class="divide-y divide-gray-200">
                <?php foreach ($checks as $key => $check): ?>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <?php if ($check['status']): ?>
                                    <i data-feather="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                                <?php else: ?>
                                    <i data-feather="x-circle" class="w-5 h-5 text-red-500 mr-3"></i>
                                <?php endif; ?>
                                <div>
                                    <h3 class="font-medium text-gray-900"><?php echo $check['name']; ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo $check['message']; ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">Required: <?php echo $check['required']; ?></div>
                                <div class="text-sm <?php echo $check['status'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    Current: <?php echo $check['current']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Server Information -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Server Information</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">PHP Information</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li><strong>Version:</strong> <?php echo phpversion(); ?></li>
                            <li><strong>Server API:</strong> <?php echo php_sapi_name(); ?></li>
                            <li><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></li>
                            <li><strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Extensions</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li><strong>PDO:</strong> <?php echo extension_loaded('pdo') ? '✓ Loaded' : '✗ Not Loaded'; ?></li>
                            <li><strong>PDO MySQL:</strong> <?php echo extension_loaded('pdo_mysql') ? '✓ Loaded' : '✗ Not Loaded'; ?></li>
                            <li><strong>Session:</strong> <?php echo extension_loaded('session') ? '✓ Loaded' : '✗ Not Loaded'; ?></li>
                            <li><strong>JSON:</strong> <?php echo extension_loaded('json') ? '✓ Loaded' : '✗ Not Loaded'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="mt-8 text-center">
            <?php if ($all_passed): ?>
                <div class="space-y-4">
                    <p class="text-gray-600">Your system is ready! Choose your next step:</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg flex items-center justify-center transition duration-150 ease-in-out">
                            <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                            Sign Up
                        </a>
                        <a href="login.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg flex items-center justify-center transition duration-150 ease-in-out">
                            <i data-feather="log-in" class="w-4 h-4 mr-2"></i>
                            Sign In
                        </a>
                        <a href="landing.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg flex items-center justify-center transition duration-150 ease-in-out">
                            <i data-feather="home" class="w-4 h-4 mr-2"></i>
                            View Landing Page
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <p class="text-gray-600">Please resolve the issues above, then refresh this page.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg flex items-center justify-center transition duration-150 ease-in-out">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                            Recheck Requirements
                        </button>
                        <a href="README.md" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg flex items-center justify-center transition duration-150 ease-in-out">
                            <i data-feather="book" class="w-4 h-4 mr-2"></i>
                            Read Documentation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-gray-500">
            <p class="text-sm">Aeris - Dropship Business Management</p>
            <p class="text-xs mt-1">Check completed at <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>