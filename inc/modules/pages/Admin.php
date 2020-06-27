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

namespace Inc\Modules\Pages;

use Inc\Core\AdminModule;

class Admin extends AdminModule
{
    private $assign = [];

    public function navigation()
    {
        return [
            $this->lang('manage', 'general')    => 'manage',
            $this->lang('add_new')                => 'add'
        ];
    }

    /**
    * list of pages
    */
    public function getManage($page = 1)
    {
        // lang
        if (!empty($_GET['lang'])) {
            $lang = $_GET['lang'];
            $_SESSION['pages']['last_lang'] = $lang;
        } elseif (!empty($_SESSION['pages']['last_lang'])) {
            $lang = $_SESSION['pages']['last_lang'];
        } else {
            $lang = $this->settings('settings', 'lang_site');
        }

        // pagination
        $totalRecords = $this->db('pages')->where('lang', $lang)->toArray();
        $pagination = new \Inc\Core\Lib\Pagination($page, count($totalRecords), 10, url([ADMIN, 'pages', 'manage', '%d']));
        $this->assign['pagination'] = $pagination->nav();
        // list
        $rows = $this->db('pages')->where('lang', $lang)
                ->limit($pagination->offset().', '.$pagination->getRecordsPerPage())
                ->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['editURL'] = url([ADMIN, 'pages', 'edit', $row['id']]);
                $row['delURL']  = url([ADMIN, 'pages', 'delete', $row['id']]);
                $row['viewURL'] = url(explode('_', $lang)[0].'/'.$row['slug']);
                $row['desc'] = str_limit($row['desc'], 48);

                $this->assign['list'][] = $row;
            }
        }

        $this->assign['langs'] = $this->_getLanguages($lang);
        return $this->draw('manage.html', ['pages' => $this->assign]);
    }

    /**
    * add new page
    */
    public function getAdd()
    {
        $this->assign['editor'] = $this->settings('settings', 'editor');
        $this->_addHeaderFiles();

        // Unsaved data with failure
        if (!empty($e = getRedirectData())) {
            $this->assign['form'] = ['title' => isset_or($e['title'], ''), 'desc' => isset_or($e['desc'], ''), 'content' => isset_or($e['content'], ''), 'slug' => isset_or($e['slug'], '')];
        } else {
            $this->assign['form'] = ['title' => '', 'desc' => '', 'content' => '', 'slug' => '', 'markdown' => 0];
        }

        $this->assign['title'] = $this->lang('new_page');
        $this->assign['langs'] = $this->_getLanguages($this->settings('settings.lang_site'), 'selected');
        $this->assign['templates'] = $this->_getTemplates(isset_or($e['template'], 'index.html'));
        $this->assign['manageURL'] = url([ADMIN, 'pages', 'manage']);

        return $this->draw('form.html', ['pages' => $this->assign]);
    }


    /**
    * edit page
    */
    public function getEdit($id)
    {
        $this->assign['editor'] = $this->settings('settings', 'editor');
        $this->_addHeaderFiles();

        $page = $this->db('pages')->where('id', $id)->oneArray();

        if (!empty($page)) {
            // Unsaved data with failure
            if (!empty($e = getRedirectData())) {
                $page = array_merge($page, ['title' => isset_or($e['title'], ''), 'desc' => isset_or($e['desc'], ''), 'content' => isset_or($e['content'], ''), 'slug' => isset_or($e['slug'], '')]);
            }

            $this->assign['form'] = htmlspecialchars_array($page);
            $this->assign['form']['content'] =  $this->tpl->noParse($this->assign['form']['content']);

            $this->assign['title'] = $this->lang('edit_page');
            $this->assign['langs'] = $this->_getLanguages($page['lang'], 'selected');
            $this->assign['templates'] = $this->_getTemplates($page['template']);
            $this->assign['manageURL'] = url([ADMIN, 'pages', 'manage']);

            return $this->draw('form.html', ['pages' => $this->assign]);
        } else {
            redirect(url([ADMIN, 'pages', 'manage']));
        }
    }

    /**
    * save data
    */
    public function postSave($id = null)
    {
        unset($_POST['save'], $_POST['files']);

        if (!$id) {
            $location = url([ADMIN, 'pages', 'add']);
        } else {
            $location = url([ADMIN, 'pages', 'edit', $id]);
        }

        if (checkEmptyFields(['title', 'lang', 'template'], $_POST)) {
            $this->notify('failure', $this->lang('empty_inputs', 'general'));
            redirect($location, $_POST);
        }

        $_POST['title'] = trim($_POST['title']);
        if (!isset($_POST['markdown'])) {
            $_POST['markdown'] = 0;
        }

        if (empty($_POST['slug'])) {
            $_POST['slug'] = createSlug($_POST['title']);
        } else {
            $_POST['slug'] = createSlug($_POST['slug']);
        }

        if ($id != null && $this->db('pages')->where('slug', $_POST['slug'])->where('lang', $_POST['lang'])->where('id', '!=', $id)->oneArray()) {
            $this->notify('failure', $this->lang('page_exists'));
            redirect(url([ADMIN, 'pages', 'edit', $id]), $_POST);
        } elseif ($id == null && $this->db('pages')->where('slug', $_POST['slug'])->where('lang', $_POST['lang'])->oneArray()) {
            $this->notify('failure', $this->lang('page_exists'));
            redirect(url([ADMIN, 'pages', 'add']), $_POST);
        }

        if (!$id) {
            $_POST['date'] = date('Y-m-d H:i:s');
            $query = $this->db('pages')->save($_POST);
            $location = url([ADMIN, 'pages', 'edit', $this->db()->pdo()->lastInsertId()]);
        } else {
            $query = $this->db('pages')->where('id', $id)->save($_POST);
        }

        if ($query) {
            $this->notify('success', $this->lang('save_success'));
        } else {
            $this->notify('failure', $this->lang('save_failure'));
        }

        redirect($location);
    }

    /**
    * remove page
    */
    public function getDelete($id)
    {
        if ($this->db('pages')->delete($id)) {
            $this->notify('success', $this->lang('delete_success'));
        } else {
            $this->notify('failure', $this->lang('delete_failure'));
        }

        redirect(url([ADMIN, 'pages', 'manage']));
    }


    /**
    * image upload from WYSIWYG
    */
    public function postEditorUpload()
    {
        header('Content-type: application/json');
        $dir    = UPLOADS.'/pages';
        $error    = null;

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if (isset($_FILES['file']['tmp_name'])) {
            $img = new \Inc\Core\Lib\Image;

            if ($img->load($_FILES['file']['tmp_name'])) {
                $imgPath = $dir.'/'.time().'.'.$img->getInfos('type');
                $img->save($imgPath);
                echo json_encode(['status' => 'success', 'result' => url($imgPath)]);
            } else {
                $error = $this->lang('editor_upload_fail');
            }

            if ($error) {
                echo json_encode(['status' => 'failure', 'result' => $error]);
            }
        }
        exit();
    }

    /**
    * module JavaScript
    */
    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES.'/pages/js/admin/pages.js');
        exit();
    }

    /**
    * list of theme's templates
    * @param string $selected
    * @return array
    */
    private function _getTemplates($selected = null)
    {
        $theme = $this->settings('settings', 'theme');
        $tpls = glob(THEMES.'/'.$theme.'/*.html');

        $result = [];
        foreach ($tpls as $tpl) {
            if ($selected == basename($tpl)) {
                $attr = 'selected';
            } else {
                $attr = null;
            }
            $result[] = ['name' => basename($tpl), 'attr' => $attr];
        }
        return $result;
    }

    private function _addHeaderFiles()
    {
        // WYSIWYG
        $this->core->addCSS(url('inc/jscripts/wysiwyg/summernote.min.css'));
        $this->core->addJS(url('inc/jscripts/wysiwyg/summernote.min.js'));
        if ($this->settings('settings', 'lang_admin') != 'en_english') {
            $this->core->addJS(url('inc/jscripts/wysiwyg/lang/'.$this->settings('settings', 'lang_admin').'.js'));
        }

        // HTML & MARKDOWN EDITOR
        $this->core->addCSS(url('/inc/jscripts/editor/markitup.min.css'));
        $this->core->addCSS(url('/inc/jscripts/editor/markitup.highlight.min.css'));
        $this->core->addCSS(url('/inc/jscripts/editor/sets/html/set.min.css'));
        $this->core->addCSS(url('/inc/jscripts/editor/sets/markdown/set.min.css'));
        $this->core->addJS(url('/inc/jscripts/editor/highlight.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/markitup.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/markitup.highlight.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/sets/html/set.min.js'));
        $this->core->addJS(url('/inc/jscripts/editor/sets/markdown/set.min.js'));

        // ARE YOU SURE?
        $this->core->addJS(url('inc/jscripts/are-you-sure.min.js'));

        // MODULE SCRIPTS
        $this->core->addJS(url([ADMIN, 'pages', 'javascript']));
    }
}
