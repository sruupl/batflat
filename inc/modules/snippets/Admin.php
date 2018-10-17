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

namespace Inc\Modules\Snippets;

use Inc\Core\AdminModule;

class Admin extends AdminModule
{
    public function navigation()
    {
        return [
            $this->lang('manage', 'general') => 'manage',
            $this->lang('add') => 'add',
        ];
    }

    /**
     * list of snippets
     */
    public function getManage()
    {
        $rows = $this->db('snippets')->toArray();
        if (count($rows)) {
            foreach ($rows as &$row) {
                $row['tag'] = $this->tpl->noParse('{$snippet.' . $row['slug'] . '}');
                $row['editURL'] = url([ADMIN, 'snippets', 'edit', $row['id']]);
                $row['delURL'] = url([ADMIN, 'snippets', 'delete', $row['id']]);
            }
        }

        return $this->draw('manage.html', ['snippets' => $rows]);
    }

    /**
     * add new snippet
     */
    public function getAdd()
    {
        return $this->getEdit();
    }

    /**
     * edit snippet
     */
    public function getEdit($id = null)
    {
        $this->add2header();

        if (!empty($redirectData = getRedirectData())) {
            $assign = $redirectData;
        }

        if ($id === null) {
            $row = ['name' => isset_or($assign['name'], null), 'content' => isset_or($assign['content'], null)];

            $assign['title'] = $this->lang('add');
        } elseif (!empty($row = $this->db('snippets')->oneArray($id))) {
            $assign['title'] = $this->lang('edit');
        } else {
            redirect(url([ADMIN, 'snippets', 'manage']));
        }

        $assign = array_merge($assign, htmlspecialchars_array($row));
        $assign['languages'] = $this->getLanguages($this->settings('settings', 'lang_site'));

        $assign['content'] = [];
        preg_match_all("/{lang: ([a-z]{2}_[a-z]+)}(.*?){\/lang}/ms", $row['content'], $matches);
        foreach ($matches[1] as $key => $value) {
            $assign['content'][trim($value)] = $this->tpl->noParse(trim($matches[2][$key]));
        }

        $assign['editor'] = $this->settings('settings', 'editor');

        return $this->draw('form.html', ['snippets' => $assign]);
    }

    /**
     * remove snippet
     */
    public function getDelete($id)
    {
        if ($this->db('snippets')->delete($id)) {
            $this->notify('success', $this->lang('delete_success'));
        } else {
            $this->notify('failure', $this->lang('delete_failure'));
        }

        redirect(url([ADMIN, 'snippets', 'manage']));
    }

    /**
     * save snippet
     */
    public function postSave($id = null)
    {
        unset($_POST['save']);

        if (checkEmptyFields(['name'], $_POST)) {
            $this->notify('failure', $this->lang('empty_inputs', 'general'));

            if (!$id) {
                redirect(url([ADMIN, 'snippets', 'add']));
            } else {
                redirect(url([ADMIN, 'snippets', 'edit', $id]));
            }
        }

        $_POST['name'] = trim($_POST['name']);
        $_POST['slug'] = createSlug($_POST['name']);

        $tmp = null;
        foreach ($_POST['content'] as $lang => $content) {
            $tmp .= "{lang: $lang}" . $content . "{/lang}";
        }

        $_POST['content'] = $tmp;

        if ($id === null) { // new
            $location = url([ADMIN, 'snippets', 'add']);
            if (!$this->db('snippets')->where('slug', $_POST['slug'])->count()) {
                if ($this->db('snippets')->save($_POST)) {
                    $location = url([ADMIN, 'snippets', 'edit', $this->db()->lastInsertId()]);
                    $this->notify('success', $this->lang('save_success'));
                } else {
                    $this->notify('failure', $this->lang('save_failure'));
                }
            } else {
                $this->notify('failure', $this->lang('already_exists'));
            }
        } else {    // edit
            if (!$this->db('snippets')->where('slug', $_POST['slug'])->where('id', '<>', $id)->count()) {
                if ($this->db('snippets')->where($id)->save($_POST)) {
                    $this->notify('success', $this->lang('save_success'));
                } else {
                    $this->notify('failure', $this->lang('save_failure'));
                }
            } else {
                $this->notify('failure', $this->lang('already_exists'));
            }

            $location = url([ADMIN, 'snippets', 'edit', $id]);
        }

        redirect($location, $_POST);
    }

    /**
     * module JavaScript
     */
    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES . '/snippets/js/admin/snippets.js');
        exit();
    }

    private function add2header()
    {
        // WYSIWYG
        $this->core->addCSS(url('inc/jscripts/wysiwyg/summernote.min.css'));
        $this->core->addJS(url('inc/jscripts/wysiwyg/summernote.min.js'));
        if ($this->settings('settings', 'lang_admin') != 'en_english') {
            $this->core->addJS(url('inc/jscripts/wysiwyg/lang/' . $this->settings('settings', 'lang_admin') . '.js'));
        }

        // HTML EDITOR
        $this->core->addCSS(url('/inc/jscripts/editor/markitup.min.css'));
        $this->core->addCSS(url('/inc/jscripts/editor/markitup.highlight.min.css'));
        $this->core->addCSS(url('/inc/jscripts/editor/sets/html/set.min.css'));
        $this->core->addJS(url('/inc/jscripts/editor/highlight.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/markitup.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/markitup.highlight.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/sets/html/set.min.js'));

        // ARE YOU SURE?
        $this->core->addJS(url('inc/jscripts/are-you-sure.min.js'));

        // MODULE SCRIPTS
        $this->core->addJS(url([ADMIN, 'snippets', 'javascript']));
    }
}
