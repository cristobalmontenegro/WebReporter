<?php
/**
 * Main Configuration File for WebReporter PHP
 */

// 1. Initial Defaults & Localization
if (!defined('APP_NAME')) define('APP_NAME', 'WebReporter PHP');
if (!defined('APP_LANG')) define('APP_LANG', 'en'); // Default language: 'en' or 'es'

// Load Localization
$lang_file = __DIR__ . '/lang/' . APP_LANG . '.php';
$translations = file_exists($lang_file) ? require $lang_file : [];

/**
 * Translation helper
 */
function __($key, $default = null) {
    global $translations;
    return $translations[$key] ?? ($default ?: $key);
}

// 2. Load local configuration if available
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
} else {
    // If no config.local.php, show a helpful setup guide instead of half-loading
    if (basename($_SERVER['PHP_SELF']) !== 'login.php' || !empty($_POST)) {
        die("<!DOCTYPE html><html><head><title>" . __('setup_required') . "</title><link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css'><link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'></head><body class='hold-transition login-page'>
            <div class='login-box' style='width: 500px;'>
                <div class='card card-outline card-danger'>
                    <div class='card-header text-center'><h3><b>Open</b>Reporter PHP</h3></div>
                    <div class='card-body text-center'>
                        <h1 class='display-4'><i class='fas fa-exclamation-triangle text-warning'></i></h1>
                        <h4 class='text-danger mb-4'><b>" . __('setup_error') . "</b></h4>
                        <div class='text-left d-inline-block'>
                            <h5>" . __('setup_steps') . "</h5>
                            <ol>
                                <li>" . __('setup_step1') . "</li>
                                <li>" . __('setup_step2') . "</li>
                                <li>" . __('setup_step3') . "</li>
                            </ol>
                        </div>
                        <p class='mt-4 text-muted'><i class='fas fa-sync-alt fa-spin mr-1'></i> " . __('setup_refresh') . "</p>
                        <hr>
                        <p class='text-muted small'><i class='fas fa-info-circle mr-1'></i> " . __('setup_git_hint') . "</p>
                    </div>
                </div>
            </div></body></html>");
    }
}

// 3. Database Defaults (if not in config.local.php)
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', 3306);
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'webreporter');

// If $REPORTS or $CHARTS aren't defined in config.local.php, provide empty defaults
if (!isset($REPORTS)) $REPORTS = [];
if (!isset($CHARTS)) $CHARTS = [];

/**
 * Get database connection
 */
function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        die("Connection to Database Failed: (" . $conn->connect_errno . ") " . $conn->connect_error . 
            "<br><br>💡 Please ensure your 'config.local.php' is correctly configured.");
    }
    $conn->set_charset("utf8");
    return $conn;
}

/**
 * Check if the necessary tables exist
 */
function check_system_readiness($conn) {
    $tables = ['user_table'];
    $missing = [];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            $missing[] = $table;
        }
    }
    return $missing;
}
?>
