<?php
// bootstrap: load config, create PDO, autoload classes, start session
$config = include __DIR__ . '/config/config.php';

$GLOBALS['config'] = $config;

try {
    $db = $config['db'];
    $pdo = new PDO($db['dsn'], $db['user'], $db['pass'], $db['options']);
    $GLOBALS['pdo'] = $pdo;
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/storage/error_bootstrap.log', $e->getMessage()."\n", FILE_APPEND);
    die("Database connection error. Check logs.");
}

// PSR-0-like autoloader for app classes
spl_autoload_register(function($class) {
    $base = __DIR__ . '/app';
    $paths = [
        $base . '/Controllers/' . $class . '.php',
        $base . '/Models/' . $class . '.php'
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            require_once $p;
            return;
        }
    }
});
// load helper file
require_once __DIR__ . '/app/helpers.php';
if (session_status() === PHP_SESSION_NONE) session_start();
