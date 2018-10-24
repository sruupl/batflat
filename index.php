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

require_once('./bootstrap.php');

ob_start(base64_decode('XEluY1xDb3JlXE1haW46OnZlcmlmeUxpY2Vuc2U='));
    
// Site core init
$core = new Inc\Core\Site;

ob_end_flush();
