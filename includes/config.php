<?php
// includes/config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'grantgates');

/*
|--------------------------------------------------------------------------
| Site Configuration
|--------------------------------------------------------------------------
*/
define('SITE_NAME', 'GrantGates');
define('BASE_URL', '/grantgates/');
define('SITE_URL', 'http://localhost/grantgates/');

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

/*
|--------------------------------------------------------------------------
| Auth Helpers
|--------------------------------------------------------------------------
*/
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/*
|--------------------------------------------------------------------------
| URL / Redirect Helpers
|--------------------------------------------------------------------------
*/
function redirect($url)
{
    header('Location: ' . SITE_URL . ltrim($url, '/'));
    exit();
}

function url($path = '')
{
    return SITE_URL . ltrim($path, '/');
}

function asset($path = '')
{
    return SITE_URL . ltrim($path, '/');
}
?>
