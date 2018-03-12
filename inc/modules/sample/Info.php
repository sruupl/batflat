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

return [
    'name'          =>  $core->lang['sample']['module_name'],
    'description'   =>  $core->lang['sample']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.0',
    'compatibility'    =>    '1.3.*',                                // Compatibility with Batflat version
    'icon'          =>  'code',                                 // Icon from http://fontawesome.io/icons/

    // Registering page for possible use as a homepage
    'pages'            =>  ['Sample Page' => 'sample'],

    'install'       =>  function () use ($core) {
    },
    'uninstall'     =>  function () use ($core) {
    }
];
