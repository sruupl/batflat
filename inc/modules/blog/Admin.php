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

namespace Inc\Modules\Blog;

use Inc\Core\AdminModule;

class Admin extends AdminModule
{
    private $assign = [];

    public function navigation()
    {
        return [
            $this->lang('manage', 'general')    => 'manage',
            $this->lang('add_new')              => 'add',
            $this->lang('settings')                => 'settings'
        ];
    }

    /**
    * list of posts
    */
    public function anyManage($page = 1)
    {
        if (isset($_POST['delete'])) {
            if (isset($_POST['post-list']) && !empty($_POST['post-list'])) {
                foreach ($_POST['post-list'] as $item) {
                    $row = $this->db('blog')->where('id', $item)->oneArray();
                    if ($this->db('blog')->delete($item) === 1) {
                        if (!empty($row['cover_photo']) && file_exists(UPLOADS."/blog/".$row['cover_photo'])) {
                            unlink(UPLOADS."/blog/".$row['cover_photo']);
                        }

                        $this->notify('success', $this->lang('delete_success'));
                    } else {
                        $this->notify('failure', $this->lang('delete_failure'));
                    }
                }

                redirect(url([ADMIN, 'blog', 'manage']));
            }
        }

        // lang
        if (!empty($_GET['lang'])) {
            $lang = $_GET['lang'];
            $_SESSION['blog']['last_lang'] = $lang;
        } elseif (!empty($_SESSION['blog']['last_lang'])) {
            $lang = $_SESSION['blog']['last_lang'];
        } else {
            $lang = $this->settings('settings.lang_site');
        }

        // pagination
        $totalRecords = count($this->db('blog')->where('lang', $lang)->toArray());
        $pagination = new \Inc\Core\Lib\Pagination($page, $totalRecords, 10, url([ADMIN, 'blog', 'manage', '%d']));
        $this->assign['pagination'] = $pagination->nav();

        // list
        $this->assign['newURL'] = url([ADMIN, 'blog', 'add']);
        $this->assign['postCount'] = 0;
        $rows = $this->db('blog')
                ->where('lang', $lang)
                ->limit($pagination->offset().', '.$pagination->getRecordsPerPage())
                ->desc('published_at')->desc('created_at')
                ->toArray();

        $this->assign['posts'] = [];
        if ($totalRecords) {
            $this->assign['postCount'] = $totalRecords;
            foreach ($rows as $row) {
                $row['editURL'] = url([ADMIN, 'blog', 'edit', $row['id']]);
                $row['delURL']  = url([ADMIN, 'blog', 'delete', $row['id']]);
                $row['viewURL'] = url(['blog', 'post', $row['slug']]);


                $fullname = $this->core->getUserInfo('fullname', $row['user_id'], true);
                $username = $this->core->getUserInfo('username', $row['user_id'], true);
                $row['user'] = !empty($fullname) ? $fullname.' ('.$username.')' : $username;

                $row['comments'] = $row['comments'] ? $this->lang('comments_on') : $this->lang('comments_off');

                switch ($row['status']) {
                    case 0:
                        $row['type'] = $this->lang('post_sketch');
                        break;
                    case 1:
                        $row['type'] = $this->lang('post_hidden');
                        break;
                    case 2:
                        $row['type'] = $this->lang('post_published');
                        break;
                    default:
                        case 0:
                        $row['type'] = "Unknown";
                }

                $row['created_at'] = date("d-m-Y", $row['created_at']);
                $row['published_at'] = date("d-m-Y", $row['published_at']);

                $row = htmlspecialchars_array($row);
                $this->assign['posts'][] = $row;
            }
        }

        $this->assign['langs'] = $this->_getLanguages($lang);

        return $this->draw('manage.html', ['blog' => $this->assign]);
    }

    /**
    * add new post
    */
    public function getAdd()
    {
        return $this->getEdit(null);
    }


    /**
    * edit post
    */
    public function getEdit($id = null)
    {
        $this->assign['manageURL'] = url([ADMIN, 'blog', 'manage']);
        $this->assign['coverDeleteURL'] = url([ADMIN, 'blog', 'deleteCover', $id]);
        $this->assign['editor'] = $this->settings('settings.editor');
        $this->_addHeaderFiles();

        if ($id === null) {
            $blog = [
                'id' => null,
                'title' => '',
                'content' => '',
                'slug' => '',
                'intro' => '',
                'lang' => $this->settings('settings.lang_site'),
                'user_id' => $this->core->getUserInfo('id'),
                'comments' => 1,
                'cover_photo' => null,
                'status' => 0,
                'markdown' => 0,
                'tags' => '',
                'published_at' => time(),
            ];
        } else {
            $blog = $this->db('blog')->where('id', $id)->oneArray();
        }

        if (!empty($blog)) {
            $this->assign['langs'] = $this->_getLanguages($blog['lang'], 'selected');
            $this->assign['form'] = htmlspecialchars_array($blog);
            $this->assign['form']['content'] =  $this->tpl->noParse($this->assign['form']['content']);
            $this->assign['form']['date'] = date("Y-m-d\TH:i", $blog['published_at']);

            $tags_array = $this->db('blog_tags')->leftJoin('blog_tags_relationship', 'blog_tags.id = blog_tags_relationship.tag_id')->where('blog_tags_relationship.blog_id', $blog['id'])->select(['blog_tags.name'])->toArray();

            $this->assign['form']['tags'] = $tags_array;
            $this->assign['users'] = $this->db('users')->toArray();
            $this->assign['author'] = $this->core->getUserInfo('id', $blog['user_id'], true);

            $this->assign['title'] = ($blog['id'] === null) ? $this->lang('new_post') : $this->lang('edit_post');

            return $this->draw('form.html', ['blog' => $this->assign]);
        } else {
            redirect(url([ADMIN, 'blog', 'manage']));
        }
    }

    /**
     * Save post
     *
     * @param int $id
     * @return void
     */
    public function postSave($id = null)
    {
        unset($_POST['save'], $_POST['files']);

        if (!empty($_POST['tags'])) {
            $tags = array_unique($_POST['tags']);
        } else {
            $tags = [];
        }

        unset($_POST['tags']);

        // redirect location
        if (!$id) {
            $location = url([ADMIN, 'blog', 'add']);
        } else {
            $location = url([ADMIN, 'blog', 'edit', $id]);
        }

        if (checkEmptyFields(['title', 'content'], $_POST)) {
            $this->notify('failure', $this->lang('empty_inputs', 'general'));
            $this->assign['form'] = htmlspecialchars_array($_POST);
            $this->assign['form']['content'] = $this->tpl->noParse($this->assign['form']['content']);
            redirect($location);
        }

        // slug
        if (empty($_POST['slug'])) {
            $_POST['slug'] = createSlug($_POST['title']);
        } else {
            $_POST['slug'] = createSlug($_POST['slug']);
        }

        // check slug and append with iterator
        $oldSlug = $_POST['slug'];
        $i = 2;

        if ($id === null) {
            $id = 0;
        }

        while ($this->db('blog')->where('slug', $_POST['slug'])->where('id', '!=', $id)->oneArray()) {
            $_POST['slug'] = $oldSlug.'-'.($i++);
        }

        // format conversion date
        $_POST['updated_at'] = strtotime(date('Y-m-d H:i:s'));
        $_POST['published_at'] = strtotime($_POST['published_at']);
        if (!isset($_POST['comments'])) {
            $_POST['comments'] = 0;
        }
        if (!isset($_POST['markdown'])) {
            $_POST['markdown'] = 0;
        }

        if (isset($_FILES['cover_photo']['tmp_name'])) {
            $img = new \Inc\Core\Lib\Image;

            if ($img->load($_FILES['cover_photo']['tmp_name'])) {
                if ($img->getInfos('width') > 1000) {
                    $img->resize(1000);
                } elseif ($img->getInfos('width') < 600) {
                    $img->resize(600);
                }

                $_POST['cover_photo'] = $_POST['slug'].".".$img->getInfos('type');
            }
        }

        if (!$id) { // new
            $_POST['created_at'] = strtotime(date('Y-m-d H:i:s'));

            $query = $this->db('blog')->save($_POST);
            $location = url([ADMIN, 'blog', 'edit', $this->db()->pdo()->lastInsertId()]);
        } else {    // edit
            $query = $this->db('blog')->where('id', $id)->save($_POST);
        }

        // detach tags from post
        if ($id) {
            $this->db('blog_tags_relationship')->delete('blog_id', $id);
            $blogId = $id;
        } else {
            $blogId = $id ? $id : $this->db()->pdo()->lastInsertId();
        }


        // Attach or create new tag
        foreach ($tags as $tag) {
            if (preg_match("/[`~!@#$%^&*()_|+\-=?;:\'\",.<>\{\}\[\]\\\/]+/", $tag)) {
                continue;
            }

            $slug = createSlug($tag);
            if ($e = $this->db('blog_tags')->like('slug', $slug)->oneArray()) {
                $this->db('blog_tags_relationship')->save(['blog_id' => $blogId, 'tag_id' => $e['id']]);
            } else {
                $tagId = $this->db('blog_tags')->save(['name' => $tag, 'slug' => $slug]);
                $this->db('blog_tags_relationship')->save(['blog_id' => $blogId, 'tag_id' => $tagId]);
            }
        }

        if ($query) {
            if (!file_exists(UPLOADS."/blog")) {
                mkdir(UPLOADS."/blog", 0777, true);
            }

            if ($p = $img->getInfos('width')) {
                $img->save(UPLOADS."/blog/".$_POST['cover_photo']);
            }

            $this->notify('success', $this->lang('save_success'));
        } else {
            $this->notify('failure', $this->lang('save_failure'));
        }

        redirect($location);
    }

    /**
     * Remove post
     *
     * @param int $id
     * @return void
     */
    public function getDelete($id)
    {
        if ($post = $this->db('blog')->where('id', $id)->oneArray() && $this->db('blog')->delete($id)) {
            if ($post['cover_photo']) {
                unlink(UPLOADS."/blog/".$post['cover_photo']);
            }
            $this->notify('success', $this->lang('delete_success'));
        } else {
            $this->notify('failure', $this->lang('delete_failure'));
        }

        redirect(url([ADMIN, 'blog', 'manage']));
    }

    /**
    * remove post cover
    */
    public function getDeleteCover($id)
    {
        if ($post = $this->db('blog')->where('id', $id)->oneArray()) {
            unlink(UPLOADS."/blog/".$post['cover_photo']);
            $this->db('blog')->where('id', $id)->save(['cover_photo' => null]);
            $this->notify('success', $this->lang('cover_deleted'));

            redirect(url([ADMIN, 'blog', 'edit', $id]));
        }
    }

    public function getSettings()
    {
        $assign = htmlspecialchars_array($this->settings('blog'));
        $assign['dateformats'] = [
            [
                'value' => 'd-m-Y',
                'name'  => '01-01-2016'
            ],
            [
                'value' => 'd/m/Y',
                'name'  => '01/01/2016'
            ],
            [
                'value' => 'd Mx Y',
                'name'  => '01 '.$this->lang('janx').' 2016'
            ],
            [
                'value' => 'M d, Y',
                'name'  => $this->lang('jan').' 01, 2016'
            ],
            [
                'value' => 'd-m-Y H:i',
                'name'  => '01-01-2016 12:00'
            ],
            [
                'value' => 'd/m/Y H:i',
                'name'  => '01/01/2016 12:00'
            ],
            [
                'value' => 'd Mx Y, H:i',
                'name'  => '01 '.$this->lang('janx').' 2016, 12:00'
            ],
        ];
        return $this->draw('settings.html', ['settings' => $assign]);
    }

    public function postSaveSettings()
    {
        foreach ($_POST['blog'] as $key => $val) {
            $this->settings('blog', $key, $val);
        }
        $this->notify('success', $this->lang('settings_saved'));
        redirect(url([ADMIN, 'blog', 'settings']));
    }

    /**
    * image upload from WYSIWYG
    */
    public function postEditorUpload()
    {
        header('Content-type: application/json');
        $dir    = UPLOADS.'/blog';
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
        echo $this->draw(MODULES.'/blog/js/admin/blog.js');
        exit();
    }

    public function getJsonTags($query = null)
    {
        header('Content-type: application/json');

        if (!$query) {
            exit(json_encode([]));
        }

        $query = urldecode($query);
        $tags = $this->db('blog_tags')->like('name', $query.'%')->toArray();

        if (array_search($query, array_column($tags, 'name')) === false) {
            $tags[] = ['id' => 0, 'slug' => createSlug($query), 'name' => $query];
        }

        exit(json_encode($tags));
    }

    private function _addHeaderFiles()
    {
        // WYSIWYG
        $this->core->addCSS(url('inc/jscripts/wysiwyg/summernote.min.css'));
        $this->core->addJS(url('inc/jscripts/wysiwyg/summernote.min.js'));

        if ($this->settings('settings.lang_admin') != 'en_english') {
            $this->core->addJS(url('inc/jscripts/wysiwyg/lang/'.$this->settings('settings.lang_admin').'.js'));
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
        $this->core->addJS(url([ADMIN, 'blog', 'javascript']));

        // MODULE CSS
        $this->core->addCSS(url(MODULES.'/blog/css/admin/blog.css'));
    }
}
