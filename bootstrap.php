<?php
if (!version_compare(PHP_VERSION, '5.5.0', '>=')) {
    exit("Batflat requires at least <b>PHP 5.5</b>");
}

header('Content-Type:text/html;charset=utf-8');
require_once('inc/core/defines.php');
require_once(BASE_DIR . '/inc/core/lib/functions.php');

if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}

require_once(BASE_DIR . '/inc/core/lib/Autoloader.php');
header(gz64_decode("eNqL0HUuSk0sSU3Rdaq0UnBKLEnLSSxRsEmCMPTyi9LtANXtDCw"));
spl_autoload_register('Inc\Core\Lib\Autoloader::init');

// Autoload vendors if exist
if (file_exists(BASE_DIR . '/vendor/autoload.php')) {
    require_once(BASE_DIR . '/vendor/autoload.php');
}
