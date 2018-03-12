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

namespace Inc\Modules\Settings;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function init()
    {
        $this->_importSettings();
    }

    private function _importSettings()
    {
        $tmp = $this->core->settings->all();
        $tmp = array_merge($tmp, $tmp['settings']);
        unset($tmp['settings']);
        $this->tpl->set('settings', $tmp);
    }
}
