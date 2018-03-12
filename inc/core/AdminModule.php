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

namespace Inc\Core;

/**
 * Admin class for administration panel
 */
abstract class AdminModule extends BaseModule
{
    /**
     * Module navigation
     *
     * @return array
     */
    public function navigation()
    {
        return [];
    }
}
