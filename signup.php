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
$form_username = ''; // Store form username separately to avoid conflict with DB config
$form_name = '';
$form_security_question_1 = '';
$form_security_question_2 = '';

// Predefined security questions
$security_questions = [
    "What is your mother's maiden name?",
    "What was the name of your first pet?",
    "What city were you born in?",
    "What is your favorite color?",
    "What was the name of your first school?",
    "What is your favorite food?",
    "What was your childhood nickname?",
    "What is the name of your best friend?",
    "What was your first car model?",
    "What is your favorite movie?"
];

if ($_POST) {
    $form_username = trim($_POST['username'] ?? '');
    $form_name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $form_security_question_1 = $_POST['security_question_1'] ?? '';
    $security_answer_1 = trim($_POST['security_answer_1'] ?? '');
    $form_security_question_2 = $_POST['security_question_2'] ?? '';
    $security_answer_2 = trim($_POST['security_answer_2'] ?? '');

    if (
        empty($form_username) || empty($form_name) || empty($password) || empty($confirm_password) ||
        empty($form_security_question_1) || empty($security_answer_1) ||
        empty($form_security_question_2) || empty($security_answer_2)
    ) {
        $error = 'Please fill in all fields.';
    } elseif ($form_security_question_1 === $form_security_question_2) {
        $error = 'Please choose different security questions.';
    } elseif (strlen($form_username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (strlen($form_name) < 2) {
        $error = 'Name must be at least 2 characters long.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$form_username]);

            if ($stmt->fetch()) {
                $error = 'Username already exists. Please choose a different username.';
            } else {
                // Create new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $security_answer_1_hash = password_hash(strtolower($security_answer_1), PASSWORD_DEFAULT);
                $security_answer_2_hash = password_hash(strtolower($security_answer_2), PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (username, name, password_hash, security_question_1, security_answer_1, security_question_2, security_answer_2) VALUES (?, ?, ?, ?, ?, ?, ?)");

                if ($stmt->execute([$form_username, $form_name, $password_hash, $form_security_question_1, $security_answer_1_hash, $form_security_question_2, $security_answer_2_hash])) {
                    $success = 'Account created successfully! You can now sign in.';
                    $form_username = ''; // Clear form after successful registration
                    $form_name = '';
                    $form_security_question_1 = '';
                    $form_security_question_2 = '';
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeris - Sign Up</title>
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
    <div class="max-w-lg w-full space-y-8 p-8">
        <div class="text-center">
            <img src="asset/aerislogoandtext.png" alt="Aeris" class="mx-auto h-16 mb-6">
            <p class="text-white text-lg">Create your Aeris account</p>
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
            <?php endif; ?> <form method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-feather="user" class="w-4 h-4 inline mr-1"></i>
                        Full Name
                    </label>
                    <input type="text" id="name" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your full name" value="<?php echo htmlspecialchars($form_name); ?>">
                    <p class="text-xs text-gray-500 mt-1">At least 2 characters</p>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-feather="at-sign" class="w-4 h-4 inline mr-1"></i>
                        Username
                    </label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your username" value="<?php echo htmlspecialchars($form_username); ?>">
                    <p class="text-xs text-gray-500 mt-1">At least 3 characters</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                        Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your password">
                    <p class="text-xs text-gray-500 mt-1">At least 6 characters</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-feather="lock" class="w-4 h-4 inline mr-1"></i>
                        Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Confirm your password">
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                        <i data-feather="shield" class="w-4 h-4 inline mr-1"></i>
                        Security Questions (for password recovery)
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="security_question_1" class="block text-sm font-medium text-gray-700 mb-2">
                                Security Question 1
                            </label>
                            <select id="security_question_1" name="security_question_1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a question...</option>
                                <?php foreach ($security_questions as $question): ?>
                                    <option value="<?php echo htmlspecialchars($question); ?>"
                                        <?php echo ($form_security_question_1 === $question) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($question); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="security_answer_1" class="block text-sm font-medium text-gray-700 mb-2">
                                Answer 1
                            </label>
                            <input type="text" id="security_answer_1" name="security_answer_1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter your answer">
                        </div>

                        <div>
                            <label for="security_question_2" class="block text-sm font-medium text-gray-700 mb-2">
                                Security Question 2
                            </label>
                            <select id="security_question_2" name="security_question_2" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a question...</option>
                                <?php foreach ($security_questions as $question): ?>
                                    <option value="<?php echo htmlspecialchars($question); ?>"
                                        <?php echo ($form_security_question_2 === $question) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($question); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="security_answer_2" class="block text-sm font-medium text-gray-700 mb-2">
                                Answer 2
                            </label>
                            <input type="text" id="security_answer_2" name="security_answer_2" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter your answer">
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <i data-feather="user-plus" class="w-5 h-5 mr-2"></i>
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="login.php" class="text-blue-600 hover:text-blue-700 font-medium">Sign in here</a>
                </p>
            </div>
        </div>

        <div class="text-center">
            <a href="landing.php" class="text-white hover:text-blue-200 text-sm flex items-center justify-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1"></i>
                Back to Home
            </a>
        </div>
    </div>

    <script>
        feather.replace();

        // Show password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');

            if (password.length === 0) {
                if (strengthDiv) strengthDiv.remove();
                return;
            }

            let strength = 0;
            let strengthText = '';
            let strengthColor = '';

            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthText = 'Weak';
                    strengthColor = 'text-red-500';
                    break;
                case 2:
                case 3:
                    strengthText = 'Medium';
                    strengthColor = 'text-yellow-500';
                    break;
                case 4:
                case 5:
                    strengthText = 'Strong';
                    strengthColor = 'text-green-500';
                    break;
            }

            let existingStrength = document.getElementById('password-strength');
            if (!existingStrength) {
                existingStrength = document.createElement('p');
                existingStrength.id = 'password-strength';
                existingStrength.className = 'text-xs mt-1';
                this.parentElement.appendChild(existingStrength);
            }

            existingStrength.className = `text-xs mt-1 ${strengthColor}`;
            existingStrength.textContent = `Password strength: ${strengthText}`;
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            let existingMatch = document.getElementById('password-match');
            if (!existingMatch && confirmPassword.length > 0) {
                existingMatch = document.createElement('p');
                existingMatch.id = 'password-match';
                existingMatch.className = 'text-xs mt-1';
                this.parentElement.appendChild(existingMatch);
            }

            if (confirmPassword.length === 0) {
                if (existingMatch) existingMatch.remove();
                return;
            }

            if (password === confirmPassword) {
                existingMatch.className = 'text-xs mt-1 text-green-500';
                existingMatch.textContent = 'Passwords match';
            } else {
                existingMatch.className = 'text-xs mt-1 text-red-500';
                existingMatch.textContent = 'Passwords do not match';
            }
        });
    </script>
</body>

</html>