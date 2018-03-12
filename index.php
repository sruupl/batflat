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

header('Content-Type:text/html;charset=utf-8');

define('BASE_DIR', __DIR__);
require_once('inc/core/defines.php');

if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}

require_once('inc/core/lib/Autoloader.php');
ob_start(base64_decode('XEluY1xDb3JlXE1haW46OnZlcmlmeUxpY2Vuc2U='));
    
// Site core init
$core = new Inc\Core\Site;

ob_end_flush();
