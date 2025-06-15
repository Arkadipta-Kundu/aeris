<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'config/database.php';

$error = '';
$success = '';
$step = 1;
$form_username = '';
$user_data = null;

if ($_POST) {
    if (isset($_POST['step']) && $_POST['step'] == '1') {
        // Step 1: Verify username
        $form_username = trim($_POST['username'] ?? '');

        if (empty($form_username)) {
            $error = 'Please enter your username.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, username, name, security_question_1, security_question_2 FROM users WHERE username = ?");
                $stmt->execute([$form_username]);
                $user_data = $stmt->fetch();

                if ($user_data) {
                    $step = 2;
                } else {
                    $error = 'Username not found.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again.';
            }
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == '2') {
        // Step 2: Verify security questions
        $user_id = $_POST['user_id'] ?? '';
        $answer1 = trim($_POST['security_answer_1'] ?? '');
        $answer2 = trim($_POST['security_answer_2'] ?? '');

        if (empty($answer1) || empty($answer2)) {
            $error = 'Please answer both security questions.';
            $step = 2;
            // Re-fetch user data for step 2
            $stmt = $pdo->prepare("SELECT id, username, name, security_question_1, security_question_2 FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch();
        } else {
            try {
                $stmt = $pdo->prepare("SELECT security_answer_1, security_answer_2 FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $stored_answers = $stmt->fetch();

                if (
                    $stored_answers &&
                    password_verify(strtolower($answer1), $stored_answers['security_answer_1']) &&
                    password_verify(strtolower($answer2), $stored_answers['security_answer_2'])
                ) {
                    $step = 3;
                    $user_data = ['id' => $user_id];
                } else {
                    $error = 'Security answers are incorrect.';
                    $step = 2;
                    // Re-fetch user data for step 2
                    $stmt = $pdo->prepare("SELECT id, username, name, security_question_1, security_question_2 FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user_data = $stmt->fetch();
                }
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again.';
                $step = 2;
            }
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == '3') {
        // Step 3: Reset password
        $user_id = $_POST['user_id'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || empty($confirm_password)) {
            $error = 'Please fill in both password fields.';
            $step = 3;
            $user_data = ['id' => $user_id];
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters long.';
            $step = 3;
            $user_data = ['id' => $user_id];
        } elseif ($new_password !== $confirm_password) {
            $error = 'Passwords do not match.';
            $step = 3;
            $user_data = ['id' => $user_id];
        } else {
            try {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");

                if ($stmt->execute([$password_hash, $user_id])) {
                    // Clear all remember tokens for security
                    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
                    $stmt->execute([$user_id]);

                    $success = 'Password reset successfully! You can now sign in with your new password.';
                    $step = 4;
                } else {
                    $error = 'Failed to reset password. Please try again.';
                    $step = 3;
                    $user_data = ['id' => $user_id];
                }
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again.';
                $step = 3;
                $user_data = ['id' => $user_id];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Password Recovery</title>
    <link rel="icon" type="image/x-icon" href="asset/logo_new.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <img src="asset/aerislogoandtext.png" alt="Aeris" class="mx-auto h-16 mb-6">
            <p class="text-white text-lg">Reset Your Password</p>
        </div>

        <div class="bg-white py-8 px-6 shadow-xl rounded-lg border border-gray-200">
            <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 mr-3"></i>
                        <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <i data-feather="check-circle" class="w-5 h-5 text-green-500 mt-0.5 mr-3"></i>
                        <p class="text-green-700 text-sm"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Step indicator -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                <?php echo ($step >= $i) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'; ?>">
                                <?php echo $i; ?>
                            </div>
                            <?php if ($i < 3): ?>
                                <div class="w-12 h-1 mx-2 <?php echo ($step > $i) ? 'bg-blue-600' : 'bg-gray-200'; ?>"></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>Username</span>
                    <span>Verify</span>
                    <span>Reset</span>
                </div>
            </div>

            <?php if ($step == 1): ?>
                <!-- Step 1: Enter Username -->
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="step" value="1">

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-feather="user" class="w-4 h-4 inline mr-1"></i>
                            Username
                        </label>
                        <input type="text" id="username" name="username" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your username" value="<?php echo htmlspecialchars($form_username); ?>">
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                        <i data-feather="arrow-right" class="w-5 h-5 mr-2"></i>
                        Continue
                    </button>
                </form>

            <?php elseif ($step == 2 && $user_data): ?>
                <!-- Step 2: Security Questions -->
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Hello <strong><?php echo htmlspecialchars($user_data['name']); ?></strong>, please answer your security questions to verify your identity.
                    </p>
                </div>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="user_id" value="<?php echo $user_data['id']; ?>">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-feather="help-circle" class="w-4 h-4 inline mr-1"></i>
                            Security Question 1
                        </label>
                        <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($user_data['security_question_1']); ?></p>
                        <input type="text" name="security_answer_1" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your answer">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-feather="help-circle" class="w-4 h-4 inline mr-1"></i>
                            Security Question 2
                        </label>
                        <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($user_data['security_question_2']); ?></p>
                        <input type="text" name="security_answer_2" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your answer">
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                        <i data-feather="arrow-right" class="w-5 h-5 mr-2"></i>
                        Verify Answers
                    </button>
                </form>

            <?php elseif ($step == 3 && $user_data): ?>
                <!-- Step 3: Reset Password -->
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        <i data-feather="check-circle" class="w-4 h-4 inline mr-1 text-green-500"></i>
                        Identity verified! Now you can set a new password.
                    </p>
                </div>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="user_id" value="<?php echo $user_data['id']; ?>">

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                            New Password
                        </label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your new password">
                        <p class="text-xs text-gray-500 mt-1">At least 6 characters</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                            Confirm New Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Confirm your new password">
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                        <i data-feather="check" class="w-5 h-5 mr-2"></i>
                        Reset Password
                    </button>
                </form>

            <?php elseif ($step == 4): ?>
                <!-- Step 4: Success -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="check" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Password Reset Complete!</h3>
                    <p class="text-gray-600 mb-6">Your password has been successfully reset. You can now sign in with your new password.</p>

                    <a href="login.php"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                        <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                        Sign In
                    </a>
                </div>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Remember your password?
                    <a href="login.php" class="text-blue-600 hover:text-blue-700 font-medium">Sign in here</a>
                </p>
            </div>
        </div>

        <div class="text-center">
            <a href="landing.php" class="text-white hover:text-blue-200 text-sm transition duration-300">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-1"></i>
                Back to home
            </a>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>