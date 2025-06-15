<?php
// Helper functions

function getDashboardStats($pdo)
{
    $stats = [];
    $user_id = $_SESSION['user_id'];

    // Orders today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND DATE(order_date) = CURDATE()");
    $stmt->execute([$user_id]);
    $stats['orders_today'] = $stmt->fetchColumn();

    // Orders this month
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())");
    $stmt->execute([$user_id]);
    $stats['orders_month'] = $stmt->fetchColumn();

    // Pending orders
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'Pending'");
    $stmt->execute([$user_id]);
    $stats['pending_orders'] = $stmt->fetchColumn();

    // Total products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $stats['total_products'] = $stmt->fetchColumn();

    // Recent orders
    $stmt = $pdo->prepare("
        SELECT o.*, p.title as product_title 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $stats['recent_orders'] = $stmt->fetchAll();

    // Top products
    $stmt = $pdo->prepare("
        SELECT p.*, COUNT(o.id) as order_count 
        FROM products p 
        LEFT JOIN orders o ON p.id = o.product_id 
        WHERE p.user_id = ?
        GROUP BY p.id 
        ORDER BY order_count DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $stats['top_products'] = $stmt->fetchAll();

    return $stats;
}

function getStatusColor($status)
{
    switch ($status) {
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'Ordered from supplier':
            return 'bg-blue-100 text-blue-800';
        case 'Shipped':
            return 'bg-purple-100 text-purple-800';
        case 'Delivered':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatPrice($price)
{
    return '₹' . number_format($price, 2);
}

function formatDate($date)
{
    return date('M j, Y', strtotime($date));
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateSKU()
{
    return 'SKU' . strtoupper(substr(uniqid(), -6));
}

// Check if user is authenticated
function requireAuth()
{
    global $pdo;

    if (!isset($_SESSION['user_id'])) {
        // Check remember me token
        if (!checkRememberToken($pdo)) {
            header('Location: landing.php');
            exit();
        }
    }
}

// Flash message functions
function setFlashMessage($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Remember Me functionality
function generateRememberToken()
{
    return bin2hex(random_bytes(32));
}

function setRememberMeCookie($user_id, $token, $pdo)
{
    // Token expires in 30 days
    $expires_at = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));

    // Hash the token for database storage
    $token_hash = hash('sha256', $token);

    // Store in database
    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $token_hash, $expires_at]);

    // Set cookie (30 days)
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

function checkRememberToken($pdo)
{
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }

    $token = $_COOKIE['remember_token'];
    $token_hash = hash('sha256', $token);    // Check if token exists and hasn't expired
    $stmt = $pdo->prepare("
        SELECT rt.user_id, u.username, u.name 
        FROM remember_tokens rt 
        JOIN users u ON rt.user_id = u.id 
        WHERE rt.token_hash = ? AND rt.expires_at > NOW()
    ");
    $stmt->execute([$token_hash]);
    $result = $stmt->fetch();

    if ($result) {
        // Clean up expired tokens
        cleanupExpiredTokens($pdo);

        // Log the user in
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['name'] = $result['name'];

        // Optionally refresh the token (rolling expiration)
        refreshRememberToken($result['user_id'], $token, $pdo);

        return true;
    }

    // Invalid token, clean it up
    clearRememberToken($token_hash, $pdo);
    return false;
}

function refreshRememberToken($user_id, $old_token, $pdo)
{
    $old_token_hash = hash('sha256', $old_token);

    // Delete old token
    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ? AND token_hash = ?");
    $stmt->execute([$user_id, $old_token_hash]);

    // Create new token
    $new_token = generateRememberToken();
    setRememberMeCookie($user_id, $new_token, $pdo);
}

function clearRememberToken($token_hash = null, $pdo = null)
{
    // Clear cookie
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);

    // Clear from database if token_hash provided
    if ($token_hash && $pdo) {
        $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
        $stmt->execute([$token_hash]);
    }
}

function clearAllRememberTokens($user_id, $pdo)
{
    // Clear all remember tokens for a user (useful for logout from all devices)
    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Clear cookie
    clearRememberToken();
}

function cleanupExpiredTokens($pdo)
{
    // Clean up expired tokens (run periodically)
    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE expires_at < NOW()");
    $stmt->execute();
}
