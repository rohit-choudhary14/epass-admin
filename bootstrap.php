<?php
// bootstrap.php
$config = include __DIR__ . '/config/config.php';
$GLOBALS['config'] = $config;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$est = $_SESSION['admin_user']['establishment'] ?? 'B'; 
$dbConf = $config['dbs'][$est] ?? $config['dbs']['B'];

try {
    $pdo = new PDO(
        $dbConf['dsn'],
        $dbConf['user'],
        $dbConf['pass'],
        $config['db_options']
    );
    $GLOBALS['pdo'] = $pdo;

} catch (Exception $e) {
    file_put_contents(
        __DIR__ . '/storage/error_bootstrap.log',
        $e->getMessage() . "\n",
        FILE_APPEND
    );
      echo "<pre>";
    echo "DB ERROR:\n";
    echo $e->getMessage();
    echo "\n\nESTABLISHMENT: ";
    var_dump($_SESSION['admin_user']['establishment'] ?? 'NOT SET');
    echo "\n\nDB CONF:\n";
    print_r($dbConf);
    echo "</pre>";
    exit;
}

/**
 * Autoloader
 */
spl_autoload_register(function ($class) {
    $base = __DIR__ . '/app';
    $paths = [
        $base . '/Controllers/' . $class . '.php',
        $base . '/Models/' . $class . '.php',
        $base . '/Core/' . $class . '.php',
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            require_once $p;
            return;
        }
    }
});

// helpers
require_once __DIR__ . '/app/helpers.php';
