<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Dropship Business Management</title>
    <link rel="icon" type="image/x-icon" href="asset/logo_new.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-pattern {
            background-image: radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.1) 2px, transparent 0);
            background-size: 50px 50px;
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="overflow-x-hidden">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16"> <!-- Logo -->
                <div class="flex items-center">
                    <img src="asset/aerislogoandtext.png" alt="Aeris" class="h-8">
                </div>
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-blue-600 transition duration-300">Features</a>
                    <a href="#benefits" class="text-gray-600 hover:text-blue-600 transition duration-300">Benefits</a> <a href="#how-it-works" class="text-gray-600 hover:text-blue-600 transition duration-300">How It Works</a>
                    <a href="signup.php" class="text-blue-600 hover:text-blue-700 px-4 py-2 rounded-lg border border-blue-600 hover:bg-blue-50 transition duration-300 flex items-center">
                        <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                        Sign Up
                    </a>
                    <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                        <i data-feather="log-in" class="w-4 h-4 mr-2"></i>
                        Sign In
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="px-4 py-2 space-y-2">
                <a href="#features" class="block py-2 text-gray-600 hover:text-blue-600">Features</a>
                <a href="#benefits" class="block py-2 text-gray-600 hover:text-blue-600">Benefits</a> <a href="#how-it-works" class="block py-2 text-gray-600 hover:text-blue-600">How It Works</a>
                <a href="signup.php" class="block py-2 text-blue-600 hover:text-blue-700">Sign Up</a>
                <a href="login.php" class="block py-2 text-blue-600 font-medium">Sign In</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg hero-pattern min-h-screen flex items-center relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full floating-animation"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full floating-animation" style="animation-delay: -3s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"> <!-- Content -->
                <div class="text-white fade-in">
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                        <span class="text-white drop-shadow-lg">Streamline Your</span>
                        <span class="text-yellow-300 drop-shadow-lg">Dropshipping</span>
                        <span class="text-white drop-shadow-lg">Business</span>
                    </h1>
                    <p class="text-xl text-blue-200 mb-8 leading-relaxed">
                        Manage products, track orders, and coordinate with suppliers effortlessly.
                        Built for entrepreneurs who need powerful tools with complete data privacy.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="signup.php" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition duration-300 flex items-center justify-center">
                            <i data-feather="user-plus" class="w-5 h-5 mr-2"></i>
                            Sign Up Free
                        </a>
                        <a href="login.php" class="glass-effect text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition duration-300 flex items-center justify-center">
                            <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                            Sign In
                        </a>
                        <a href="#features" class="glass-effect text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition duration-300 flex items-center justify-center">
                            <i data-feather="info" class="w-5 h-5 mr-2"></i>
                            Learn More
                        </a>
                    </div><!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-white/20">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-300">100%</div>
                            <div class="text-sm text-blue-200">Free to Use</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-300">Solo</div>
                            <div class="text-sm text-blue-200">Entrepreneur Focus</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-300">Simple</div>
                            <div class="text-sm text-blue-200">Clean Interface</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image/Dashboard Preview -->
                <div class="lg:flex justify-center items-center fade-in" style="animation-delay: 0.3s;">
                    <div class="glass-effect rounded-2xl p-8 max-w-md mx-auto">
                        <div class="bg-white rounded-xl p-6 mb-4">
                            <div class="flex items-center mb-4">
                                <img src="asset/logo.png" alt="Aeris" class="h-8 w-8 mr-3">
                                <span class="font-bold text-gray-900">Dashboard</span>
                            </div>

                            <!-- Mock Dashboard Cards -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-full mr-3">
                                            <i data-feather="shopping-cart" class="w-4 h-4 text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600">Today</div>
                                            <div class="font-bold text-gray-900">12</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-green-100 rounded-full mr-3">
                                            <i data-feather="package" class="w-4 h-4 text-green-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600">Products</div>
                                            <div class="font-bold text-gray-900">48</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mock Order List -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div class="text-sm font-medium">John Doe</div>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Delivered</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div class="text-sm font-medium">Jane Smith</div>
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center text-white">
                            <div class="text-sm font-medium">Clean, Intuitive Interface</div>
                            <div class="text-xs text-blue-100 mt-1">Everything you need, nothing you don't</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Everything You Need</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Powerful features designed specifically for dropship businesses,
                    without the complexity of enterprise solutions.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-8 rounded-2xl border border-gray-200">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="package" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Product Management</h3>
                    <p class="text-gray-600 mb-4">
                        Organize your product catalog with SKUs, pricing, supplier information, and automatic stock tracking.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> SKU Management</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Price Tracking</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Stock Levels</li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white p-8 rounded-2xl border border-gray-200">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="shopping-cart" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Order Tracking</h3>
                    <p class="text-gray-600 mb-4">
                        Track customer orders from placement to delivery with status updates and customer management.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Status Updates</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Customer Info</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Order History</li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white p-8 rounded-2xl border border-gray-200">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="truck" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Supplier Coordination</h3>
                    <p class="text-gray-600 mb-4">
                        Manage supplier orders, track deliveries, and maintain clear communication with your suppliers.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Order Tracking</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Delivery Dates</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Status Updates</li>
                    </ul>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white p-8 rounded-2xl border border-gray-200">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="bar-chart-3" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Analytics Dashboard</h3>
                    <p class="text-gray-600 mb-4">
                        Get insights into your business with clear metrics, top products, and order trends.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Daily/Monthly Stats</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Top Products</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Order Trends</li>
                    </ul>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white p-8 rounded-2xl border border-gray-200">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="shield" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Secure & Private</h3>
                    <p class="text-gray-600 mb-4">
                        Your business data is secure with proper authentication and no email requirements.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Secure Login</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> No Email Required</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Private Data</li>
                    </ul>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white p-8 rounded-2xl border border-gray-200">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <i data-feather="smartphone" class="w-6 h-6 text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Mobile Friendly</h3>
                    <p class="text-gray-600 mb-4">
                        Access your business data from anywhere with a fully responsive, mobile-optimized interface.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Responsive Design</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Touch Optimized</li>
                        <li class="flex items-center"><i data-feather="check" class="w-4 h-4 mr-2 text-green-500"></i> Fast Loading</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Built for Solo Entrepreneurs</h2>
                    <p class="text-xl text-gray-600 mb-8">
                        Stop juggling spreadsheets and sticky notes. Aeris gives you the professional tools
                        you need without the complexity of enterprise software.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i data-feather="zap" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Lightning Fast Setup</h3>
                                <p class="text-gray-600">Get started in minutes, not hours. No complex configuration required.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i data-feather="dollar-sign" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Cost Effective</h3>
                                <p class="text-gray-600">No monthly subscriptions or per-user fees. Own your business tools.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i data-feather="trending-up" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Grow Your Business</h3>
                                <p class="text-gray-600">Make better decisions with clear insights and organized data.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:pl-12">
                    <div class="bg-white rounded-2xl p-8 shadow-xl">
                        <div class="text-center mb-6">
                            <img src="asset/logo.png" alt="Aeris" class="h-16 w-16 mx-auto mb-4">
                            <h3 class="text-xl font-bold text-gray-900">Ready to Start?</h3>
                            <p class="text-gray-600">Join entrepreneurs who've simplified their dropship business</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">Setup Time</span>
                                <span class="text-green-600 font-semibold">
                                    < 5 minutes</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">Monthly Cost</span>
                                <span class="text-green-600 font-semibold">$0</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">Learning Curve</span>
                                <span class="text-green-600 font-semibold">Minimal</span>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <a href="signup.php" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-300">
                                Sign Up Free
                            </a>
                            <a href="login.php" class="flex-1 border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-3 px-4 rounded-lg text-center transition duration-300">
                                Sign In
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Simple workflow designed for dropship businesses. Get organized and stay in control.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Add Your Products</h3>
                    <p class="text-gray-600">
                        Import your product catalog with SKUs, pricing, and supplier information.
                        Track stock levels automatically.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-green-600">2</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Process Orders</h3>
                    <p class="text-gray-600">
                        Record customer orders, track their status from pending to delivered,
                        and manage customer information.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Coordinate Suppliers</h3>
                    <p class="text-gray-600">
                        Track orders placed with suppliers, monitor delivery schedules,
                        and maintain clear communication.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Streamline Your Business?</h2>
            <p class="text-xl text-blue-100 mb-8">
                Join the solo entrepreneurs who've simplified their dropship operations with Aeris.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="signup.php" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition duration-300 flex items-center justify-center">
                    <i data-feather="user-plus" class="w-5 h-5 mr-2"></i>
                    Sign Up Free
                </a> <a href="login.php" class="glass-effect text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition duration-300 flex items-center justify-center">
                    <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                    Sign In
                </a>
                <a href="#features" class="glass-effect text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition duration-300 flex items-center justify-center">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i>
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="asset/aerislogoandtext.png" alt="Aeris" class="h-8">
                    </div>
                    <p class="text-gray-400">
                        Streamlined dropship business management for solo entrepreneurs.
                        Simple, powerful, and built for growth.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold mb-4">Features</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>Product Management</li>
                        <li>Order Tracking</li>
                        <li>Supplier Coordination</li>
                        <li>Analytics Dashboard</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Getting Started</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="system-check.php" class="hover:text-white transition duration-300">System Check</a></li>
                        <li><a href="signup.php" class="hover:text-white transition duration-300">Sign Up</a></li>
                        <li><a href="login.php" class="hover:text-white transition duration-300">Sign In</a></li>
                        <li><a href="#features" class="hover:text-white transition duration-300">View Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-white transition duration-300">How It Works</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Aeris. Built for dropship entrepreneurs.</p>
            </div>
        </div>
    </footer>

    <script>
        feather.replace();

        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/95');
                nav.classList.remove('bg-white/90');
            } else {
                nav.classList.add('bg-white/90');
                nav.classList.remove('bg-white/95');
            }
        });
    </script>
</body>

</html>