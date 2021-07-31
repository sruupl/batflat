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

namespace Inc\Modules\Navigation;

use Inc\Core\AdminModule;

class Admin extends AdminModule
{
    private $assign = [];
    public function navigation()
    {
        return [
            $this->lang('manage', 'general') => 'manage',
            $this->lang('add_link') => 'newLink',
            $this->lang('add_nav') => 'newNav'
        ];
    }

    /**
    * list of navs and their children
    */
    public function getManage()
    {
        // lang
        if (!empty($_GET['lang'])) {
            $lang = $_GET['lang'];
            $_SESSION['navigation']['last_lang'] = $lang;
        } elseif (!empty($_SESSION['navigation']['last_lang'])) {
            $lang = $_SESSION['navigation']['last_lang'];
        } else {
            $lang = $this->settings('settings', 'lang_site');
        }

        $this->assign['langs'] = $this->_getLanguages($lang, 'active');

        // list
        $rows = $this->db('navs')->toArray();
        if (count($rows)) {
            foreach ($rows as $row) {
                $row['name'] = $this->tpl->noParse('{$navigation.'.$row['name'].'}');
                $row['editURL'] = url([ADMIN, 'navigation', 'editNav', $row['id']]);
                $row['delURL'] = url([ADMIN, 'navigation', 'deleteNav', $row['id']]);
                $row['items'] = $this->_getNavItems($row['id'], $lang);

                $this->assign['navs'][] = $row;
            }
        }

        return $this->draw('manage.html', ['navigation' => $this->assign]);
    }

    /**
    * add new link
    */
    public function getNewLink()
    {
        // lang
        $lang = isset($_GET['lang']) ? $_GET['lang'] : $this->settings('settings', 'lang_site');

        $this->assign['langs'] = $this->_getLanguages($lang, 'selected');
        $this->assign['link'] = ['name' => '', 'lang' => '', 'page' => '', 'url' => '', 'parent' => '', 'class' => ''];

        // list of pages
        $this->assign['pages'] = $this->_getPages($lang);
        foreach ($this->core->getRegisteredPages() as $page) {
            $this->assign['pages'][] = array_merge($page, ['id' => $page['slug'], 'attr' => null]);
        }

        // list of parents
        $this->assign['navs'] = $this->_getParents($lang);

        $this->assign['title'] = $this->lang('add_link');
        return $this->draw('form.link.html', ['navigation' => $this->assign]);
    }

    /**
    * edit link
    */
    public function getEditLink($id)
    {
        $row = $this->db('navs_items')->oneArray($id);

        if (!empty($row)) {
            // lang
            $lang = isset($_GET['lang']) ? $_GET['lang'] : $row['lang'];

            $this->assign['langs'] = $this->_getLanguages($lang, 'selected');
            $this->assign['link'] = filter_var_array($row, FILTER_SANITIZE_SPECIAL_CHARS);

            // list of pages
            $this->assign['pages'] = $this->_getPages($lang, $row['page']);
            foreach ($this->core->getRegisteredPages() as $page) {
                $this->assign['pages'][] = array_merge($page, ['id' => $page['slug'], 'attr' => (($row['page'] == 0 && $row['url'] == $page['slug']) ? 'selected' : null)]);
            }

            // list of parents
            $this->assign['navs'] = $this->_getParents($lang, $row['nav'], $row['parent'], $row['id']);

            $this->assign['title'] = $this->lang('edit_link');
            return $this->draw('form.link.html', ['navigation' => $this->assign]);
        } else {
            redirect(url([ADMIN, 'navigation', 'manage']));
        }
    }

    /**
    * save link data
    */
    public function postSaveLink($id = null)
    {
        unset($_POST['save']);
        $formData = htmlspecialchars_array($_POST);

        // check if it's an external link
        $fields = $formData['page'] ? ['name', 'page', 'lang', 'parent'] : ['name', 'url', 'lang', 'parent'];
        $location = $id ? url([ADMIN, 'navigation', 'editLink', $id]) : url([ADMIN, 'navigation', 'newLink']);

        if (checkEmptyFields($fields, $formData)) {
            $this->notify('failure', $this->lang('empty_inputs', 'general'));
            $this->assign['form'] = filter_var_array($formData, FILTER_SANITIZE_SPECIAL_CHARS);
            redirect($location);
        }

        if ($formData['page']) {
            $formData['url'] = null;
        }

        // get parent
        $parent = explode('_', $formData['parent']);
        $formData['nav'] = $parent[0];
        $formData['parent'] = (isset($parent[1]) ? $parent[1] : 0);

        if (!is_numeric($formData['page'])) {
            $formData['url'] = $formData['page'];
            $formData['page'] = 0;
        }

        if (!$id) {
            $formData['"order"'] = $this->_getHighestOrder($formData['nav'], $formData['parent'], $formData['lang']) + 1;
            $query = $this->db('navs_items')->save($formData);
        } else {
            $query = $this->db('navs_items')->where($id)->save($formData);
            if ($query) {
                $query = $this->db('navs_items')->where('parent', $id)->update(['nav' => $formData['nav']]);
            }
        }

        if ($query) {
            $this->notify('success', $this->lang('save_link_success'));
        } else {
            $this->notify('failure', $this->lang('save_link_failure'));
        }

        redirect($location);
    }

    /**
    * delete link
    */
    public function getDeleteLink($id)
    {
        if ($this->db('navs_items')->where('id', $id)->orWhere('parent', $id)->delete()) {
            $this->notify('success', $this->lang('delete_link_success'));
        } else {
            $this->notify('failure', $this->lang('delete_link_failure'));
        }

        redirect(url([ADMIN, 'navigation', 'manage']));
    }

    /**
    * add new nav
    */
    public function getNewNav()
    {
        $this->assign['title'] = $this->lang('add_nav');

        $this->assign['name'] = '';
        return $this->draw('form.nav.html', ['navigation' => $this->assign]);
    }

    /**
    * edit nav
    */
    public function getEditNav($id)
    {
        $this->assign['title'] = $this->lang('edit_nav');
        $row = $this->db('navs')->where($id)->oneArray();

        if (!empty($row)) {
            $this->assign['name'] = $row['name'];
            $this->assign['id'] = $row['id'];
        } else {
            redirect(url([ADMIN, 'navigation', 'manage']));
        }

        return $this->draw('form.nav.html', ['navigation' => $this->assign]);
    }

    /**
    * save nav
    */
    public function postSaveNav($id = null)
    {
        $formData = htmlspecialchars_array($_POST);

        if (empty($formData['name'])) {
            if (!$id) {
                redirect(url([ADMIN, 'navigation', 'newNav']));
            } else {
                redirect(url([ADMIN, 'navigation', 'editNav', $id]));
            }

            $this->notify('failure', $this->lang('empty_inputs', 'general'));
        }

        $name = createSlug($formData['name']);

        // check if nav already exists
        if (!$this->db('navs')->where('name', $name)->count()) {
            if (!$id) {
                $query = $this->db('navs')->save(['name' => $name]);
            } else {
                $query = $this->db('navs')->where($id)->save(['name' => $name]);
            }

            if ($query) {
                $this->notify('success', $this->lang('save_nav_success'));
            } else {
                $this->notify('success', $this->lang('save_nav_failure'));
            }
        } else {
            $this->notify('failure', $this->lang('nav_already_exists'));
        }

        redirect(url([ADMIN, 'navigation', 'manage']));
    }

    /**
    * remove nav
    */
    public function getDeleteNav($id)
    {
        if ($this->db('navs')->delete($id)) {
            $this->db('navs_items')->delete('nav', $id);
            $this->notify('success', $this->lang('delete_nav_success'));
        } else {
            $this->notify('failure', $this->lang('delete_nav_failure'));
        }

        redirect(url([ADMIN, 'navigation', 'manage']));
    }

    /**
    * list of pages
    * @param string $lang
    * @param integer $selected
    * @return array
    */
    private function _getPages($lang, $selected = null)
    {
        $rows = $this->db('pages')->where('lang', $lang)->toArray();

        if (count($rows)) {
            foreach ($rows as $row) {
                if ($selected == $row['id']) {
                    $attr = 'selected';
                } else {
                    $attr = null;
                }
                $result[] = ['id' => $row['id'], 'title' => $row['title'], 'slug' => $row['slug'], 'attr' => $attr];
            }
        }

        return $result;
    }

    /**
    * list of parents
    * @param string $lang
    * @param integer $selected
    * @return array
    */
    private function _getParents($lang, $nav = null, $page = null, $except = null)
    {
        $rows = $this->db('navs')->toArray();

        if (count($rows)) {
            foreach ($rows as &$row) {
                $row['name'] = $this->tpl->noParse('{$navigation.'.$row['name'].'}');
                $row['items'] = $this->_getNavItems($row['id'], $lang);

                if ($nav && !$page && ($nav == $row['id'])) {
                    $row['attr'] = 'selected';
                } else {
                    $row['attr'] = null;
                }

                if (is_array($row['items'])) {
                    foreach ($row['items'] as $key => &$value) {
                        if ($except && ($except == $value['id'])) {
                            unset($row['items'][$key]);
                        } else {
                            if ($nav && $page && ($page == $value['id'])) {
                                $value['attr'] = 'selected';
                            } else {
                                $value['attr'] = null;
                            }
                        }
                    }
                }
            }
        }

        return $rows;
    }

    /**
    * list of nav items
    * @param integer $nav
    * @param string $lang
    * @return array
    */
    private function _getNavItems($nav, $lang)
    {
        $items = $this->db('navs_items')->where('nav', $nav)->where('lang', $lang)->asc('"order"')->toArray();

        if (count($items)) {
            foreach ($items as &$item) {
                $item['editURL'] = url([ADMIN, 'navigation', 'editLink', $item['id']]);
                $item['delURL'] = url([ADMIN, 'navigation', 'deleteLink', $item['id']]);
                $item['upURL'] = url([ADMIN, 'navigation', 'changeOrder', 'up', $item['id']]);
                $item['downURL'] = url([ADMIN, 'navigation', 'changeOrder', 'down', $item['id']]);

                if ($item['page'] > 0) {
                    $page = $this->db('pages')->where('id', $item['page'])->oneArray();
                    $item['fullURL'] = '/'.$page['slug'];
                } else {
                    $item['fullURL'] = (parse_url($item['url'], PHP_URL_SCHEME) || strpos($item['url'], '#') === 0 ? '' : '/').trim($item['url'], '/');
                }
            }

            return $this->buildTree($items);
        }
    }

    /**
    * generate tree from array
    * @param array $items
    * @return array
    */
    public function buildTree(array $items)
    {
        $children = [0 => []];

        foreach ($items as &$item) {
            $children[$item['parent']][] = &$item;
        }
        unset($item);

        foreach ($items as &$item) {
            if (isset($children[$item['id']])) {
                $item['children'] = $children[$item['id']];
            }
        }

        return $children[0];
    }

    /**
    * change order of nav item
    * @param string $direction
    * @param integer $id
    * @return void
    */
    public function getChangeOrder($direction, $id)
    {
        $item = $this->db('navs_items')->oneArray($id);

        if (!empty($item)) {
            if ($direction == 'up') {
                $nextItem = $this->db('navs_items')
                    ->where('"order"', '<', $item['order'])
                    ->where('nav', $item['nav'])
                    ->where('parent', $item['parent'])
                    ->where('lang', $item['lang'])
                    ->desc('"order"')
                    ->oneArray();
            } else {
                $nextItem = $this->db('navs_items')
                    ->where('"order"', '>', $item['order'])
                    ->where('nav', $item['nav'])
                    ->where('parent', $item['parent'])
                    ->where('lang', $item['lang'])
                    ->asc('"order"')
                    ->oneArray();
            }

            if (!empty($nextItem)) {
                $this->db('navs_items')->where('id', $item['id'])->save(['"order"' => $nextItem['order']]);
                $this->db('navs_items')->where('id', $nextItem['id'])->save(['"order"' => $item['order']]);
            }
        }
        redirect(url(ADMIN.'/navigation/manage?lang='.$item['lang']));
    }

    /**
    * get item with highest order
    * @param integer $nav
    * @param integer $parent
    * @param string $lang
    * @return integer
    */
    private function _getHighestOrder($nav, $parent, $lang)
    {
        $item = $this->db('navs_items')
            ->where('nav', $nav)
            ->where('parent', $parent)
            ->where('lang', $lang)
            ->desc('"order"')
            ->oneArray();

        return !empty($item) ? $item['order'] : 0;
    }
}
