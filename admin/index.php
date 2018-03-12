<?php
/**
* This file is part of Batflat ~ the lightweight, fast and easy CMS
*
* @author       Paweł Klockiewicz <klockiewicz@sruu.pl>
* @author       Wojciech Król <krol@sruu.pl>
* @copyright    2017 Paweł Klockiewicz, Wojciech Król <Sruu.pl>
* @license      https://batflat.org/license
* @link         https://batflat.org
*/

ob_start();
header('Content-Type:text/html;charset=utf-8');
define('BASE_DIR', __DIR__.'/..');
require_once('../inc/core/defines.php');

if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}

require_once('../inc/core/lib/Autoloader.php');

// Admin core init
$core = new Inc\Core\Admin;

if ($core->loginCheck()) {
    $core->loadModules();
    
    // Modules routing
    $core->router->set('(:str)/(:str)(:any)', function ($module, $method, $params) use ($core) {
        $core->createNav($module, $method);
        if ($params) {
            $core->loadModule($module, $method, explode('/', trim($params, '/')));
        } else {
            $core->loadModule($module, $method);
        }
    });

    $core->router->execute();
    $core->drawTheme('index.html');
    $core->module->finishLoop();
} else {
    if (isset($_POST['login'])) {
        if ($core->login($_POST['username'], $_POST['password'], isset($_POST['remember_me']))) {
            if (count($arrayURL = parseURL()) > 1) {
                $url = array_merge([ADMIN], $arrayURL);
                redirect(url($url));
            }
            redirect(url([ADMIN, 'dashboard', 'main']));
        }
    }
    $core->drawTheme('login.html');
}

ob_end_flush();
