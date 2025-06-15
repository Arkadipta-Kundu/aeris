<nav class="bg-white shadow-sm border-b border-gray-200 fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="landing.php" class="flex items-center">
                        <img src="asset/aerislogoandtext.png" alt="Aeris" class="h-8">
                    </a>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition duration-150 ease-in-out
                    <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-blue-600 border-b-2 border-blue-600' : ''; ?>">
                    <i data-feather="home" class="w-4 h-4 mr-2"></i>
                    Dashboard
                </a>

                <a href="products.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition duration-150 ease-in-out
                    <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'text-blue-600 border-b-2 border-blue-600' : ''; ?>">
                    <i data-feather="package" class="w-4 h-4 mr-2"></i>
                    Products
                </a>

                <a href="orders.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition duration-150 ease-in-out
                    <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'text-blue-600 border-b-2 border-blue-600' : ''; ?>">
                    <i data-feather="shopping-cart" class="w-4 h-4 mr-2"></i>
                    Orders
                </a>

                <a href="suppliers.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition duration-150 ease-in-out
                    <?php echo (basename($_SERVER['PHP_SELF']) == 'suppliers.php') ? 'text-blue-600 border-b-2 border-blue-600' : ''; ?>">
                    <i data-feather="truck" class="w-4 h-4 mr-2"></i>
                    Suppliers
                </a>
            </div> <!-- User Menu -->
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 truncate max-w-32">
                            Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']); ?>
                        </span>
                        <a href="logout.php"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-red-600 transition duration-150 ease-in-out">
                            <i data-feather="log-out" class="w-4 h-4 mr-2"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="md:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
            <!-- Mobile User Info -->
            <div class="px-3 py-2 border-b border-gray-200 mb-2">
                <span class="text-sm font-medium text-gray-700">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
            </div>

            <a href="index.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-md">
                <i data-feather="home" class="w-4 h-4 mr-2 inline"></i>
                Dashboard
            </a>
            <a href="products.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-md">
                <i data-feather="package" class="w-4 h-4 mr-2 inline"></i>
                Products
            </a>
            <a href="orders.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-md">
                <i data-feather="shopping-cart" class="w-4 h-4 mr-2 inline"></i>
                Orders
            </a>
            <a href="suppliers.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-md">
                <i data-feather="truck" class="w-4 h-4 mr-2 inline"></i>
                Suppliers
            </a>
            <div class="border-t border-gray-200 pt-2">
                <a href="logout.php" class="block px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md">
                    <i data-feather="log-out" class="w-4 h-4 mr-2 inline"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>