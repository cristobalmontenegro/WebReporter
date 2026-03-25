<?php
/**
 * Centralized Authentication & Authorization
 */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.datatables.net; font-src \'self\' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src \'self\' data:; connect-src \'self\' https://cdn.datatables.net; frame-ancestors \'none\';');
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 1. Mandatory Login Check
if (!isset($_SESSION['user_id'])) {
    // Return JSON ONLY if it's a proper AJAX request
    $is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    
    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Sesión expirada.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // For any direct page access (including Print/Excel exports), redirect to login
    header('Location: login.php');
    exit;
}

// 2. Access variables for convenience
$is_admin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

// 3. CSRF Protection System
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

/**
 * Helper to restrict admin-only pages
 */
function restrict_to_admin() {
    global $is_admin;
    if (!$is_admin) {
        header('HTTP/1.1 403 Forbidden');
        echo "<h1>403 Acceso Denegado</h1><p>Solo los administradores pueden acceder a esta sección.</p>";
        exit;
    }
}

/**
 * Helper to validate CSRF tokens
 */
function validate_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        header('HTTP/1.1 403 Forbidden');
        echo "<h1>403 Error de Seguridad</h1><p>Solicitud no válida (CSRF).</p>";
        exit;
    }
}
?>
