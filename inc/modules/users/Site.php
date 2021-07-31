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

namespace Inc\Modules\Users;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function init()
    {
        $this->tpl->set('users', function () {
            $result = [];
            $users = $this->db('users')->select(['id', 'username', 'fullname', 'description', 'avatar', 'email'])->toArray();

            foreach ($users as $key => $value) {
                $result[$value['id']] = $users[$key];
                $result[$value['id']]['avatar'] = url('uploads/users/' . $value['avatar']);
            }

            return $result;
        });
    }
}
