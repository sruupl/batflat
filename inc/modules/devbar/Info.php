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
        'name'          =>  $core->lang['devbar']['module_name'],
        'description'   =>  $core->lang['devbar']['module_desc'],
        'author'        =>  'Sruu.pl',
        'version'       =>  '1.0',
		'compatibility'	=> 	'1.3.*',
        'icon'          =>  'bug',

        'install'       =>  function() use($core)
        {
            
        },
        'uninstall'     =>  function() use($core)
        {
            
        }
    ];