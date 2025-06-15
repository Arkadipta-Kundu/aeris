<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'config/database.php';
require_once 'includes/functions.php';

// Check remember me token if not logged in
if (!isset($_SESSION['user_id'])) {
    if (checkRememberToken($pdo)) {
        header('Location: index.php');
        exit();
    }
}

$error = '';

if ($_POST) {
    $form_username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if (empty($form_username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, name, password_hash FROM users WHERE username = ?");
            $stmt->execute([$form_username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];

                // Handle remember me
                if ($remember_me) {
                    $token = generateRememberToken();
                    setRememberMeCookie($user['id'], $token, $pdo);
                }

                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Login</title>
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

<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <img src="asset/aerislogoandtext.png" alt="Aeris" class="mx-auto h-16 mb-6">
            <p class="text-gray-600">Sign in to your dropship dashboard</p>
        </div>

        <div class="bg-white py-8 px-6 shadow-sm rounded-lg border border-gray-200">
            <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 mr-3"></i>
                        <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="username"
                            name="username"
                            required
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter your username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password"
                            id="password"
                            name="password"
                            required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter your password">
                    </div>
                </div> <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Remember me for 30 days
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="forgot-password.php" class="text-blue-600 hover:text-blue-700 font-medium">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <i data-feather="log-in" class="w-4 h-4 mr-2"></i>
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="signup.php" class="text-blue-600 hover:text-blue-700 font-medium">Sign up here</a>
                </p>
            </div>
        </div>
        <div class="text-center">
            <p class="text-xs text-gray-500 mb-2">
                Secure dropship business management
            </p>
            <a href="landing.php" class="text-xs text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out">
                ← Back to Home
            </a>
        </div>
    </div>

    <script>
        feather.replace();

        // Focus on username field
        document.getElementById('username').focus();
    </script>
</body>

</html>