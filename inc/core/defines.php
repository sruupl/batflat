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

if (!version_compare(PHP_VERSION, '5.5.0', '>=')) {
    exit("Batflat requires at least <b>PHP 5.5</b>");
}

// Admin cat name
define('ADMIN', 'admin');

// Themes path
define('THEMES', BASE_DIR . '/themes');

// Modules path
define('MODULES', BASE_DIR . '/inc/modules');

// Uploads path
define('UPLOADS', BASE_DIR . '/uploads');

// Lock files
define('FILE_LOCK', false);

// Basic modules
define('BASIC_MODULES', serialize([
    8 => 'settings',
    0 => 'dashboard',
    2 => 'pages',
    3 => 'navigation',
    7 => 'users',
    1 => 'blog',
    4 => 'galleries',
    5 => 'snippets',
    6 => 'modules',
    9 => 'contact',
    10 => 'langswitcher',
    11 => 'devbar',
]));

// HTML beautifier
define('HTML_BEAUTY', false);

// Developer mode
define('DEV_MODE', false);