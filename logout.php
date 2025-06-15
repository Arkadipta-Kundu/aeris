<?php
session_start();

require_once 'config/database.php';
require_once 'includes/functions.php';

// Clear remember me tokens if user is logged in
if (isset($_SESSION['user_id'])) {
    clearAllRememberTokens($_SESSION['user_id'], $pdo);
}

session_destroy();
header('Location: landing.php');
exit();
