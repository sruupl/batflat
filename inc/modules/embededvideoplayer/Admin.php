<?php
/**
* BatFlat - EmbededVideoPlayer Module by pplaczek.
* Allows you to upload a video clip and play it using the html5 player.
*
* @author       Piotr Płaczek <piotr@pplaczek.pl>
* @copyright    2018 Piotr Płaczek <p.dev>
* @license      MIT https://github.com/piotr-placzek/BatFlat-EmbededVideoPlayer/blob/master/LICENSE.md
* @link         https://github.com/piotr-placzek/BatFlat-EmbededVideoPlayer
*/


namespace Inc\Modules\EmbededVideoPlayer;

use Inc\Core\AdminModule;

/**
 * EmbededVideoPlayer admin class
 */
class Admin extends AdminModule
{
    /**
     * Initialize module. Add fontawesome css.
     */
    public function init(){
    }

    /**
     * Module navigation
     * Items of the returned array will be displayed in the administration sidebar
     *
     * @return array
     */
    public function navigation()
    {
        return [
            $this->lang('index') => 'index',
        ];
    }

    /**
     * GET: /admin/embededvideoplayer/index
     * Subpage method of the module
     *
     * @return string
     */
    public function getIndex()
    {
        $entries = $this->core->db('pdev_evp')->toArray();
        return $this->draw('index.html', ['entries' => $entries]);
    }

    /**
     * Upload file into ~/uploads/pdev_evp directory and add data into db
     */
    public function postSaveFile()
    {
        dump($_FILES);
        if(is_uploaded_file($_FILES['file_path']['tmp_name'])) {
            $dir = UPLOADS.'/pdev_evp';
            move_uploaded_file($_FILES['file_path']['tmp_name'], $dir."/".$_FILES['file_path']['name']);

            $row = array(
                'name' => $_POST['file_name'],
                'slug' => $_POST['file_slug'],
                'file' => $_FILES['file_path']['name'],
                'path' => url(UPLOADS.'/pdev_evp/'.$_FILES['file_path']['name'])
            );

            if($query = $this->core->db('pdev_evp')->save($row)){
                $this->notify('success', $this->core->lang['embededvideoplayer']['db_save_ok'].' '.$_POST['file_name'].' ('.$_FILES['file_path']['size'].'B)');
            }
            else{
                $this->notify('failure', $this->core->lang['embededvideoplayer']['no_files']);
            }
        }
        else{
            $this->notify('failure', $this->core->lang['embededvideoplayer']['no_files']);
        }

        redirect(url([ADMIN, 'embededvideoplayer', 'index']));
    }

    /**
     * GET: /admin/embededvideoplayer/modify
     * Subpage method of the module
     * @param id
     * @return string
     */
    public function getModify($id){
        $row = $this->core->db('pdev_evp')->oneArray($id);
        return $this->draw('modify.html', ['element' => $row]);
    }

    /**
     * Modify row in db
     */
    public function postModifyFile(){
        if($this->core->db('pdev_evp')->where('id', $_POST['id'])->update(
            [
                'name' => $_POST['file_name'],
                'slug' => $_POST['file_slug']
            ]
        )){
            $this->notify('success', $this->core->lang['embededvideoplayer']['modify_file_success']);
        }
        else{
            $this->notify('failure', $this->core->lang['embededvideoplayer']['modify_file_failure']);
        }

        redirect(url([ADMIN, 'embededvideoplayer', 'index']));
    }

    /**
     * Remove row from db
     * @param $id
     */
    public function getRemove($id){
        $row = $this->core->db('pdev_evp')->oneArray($id);
        $file = UPLOADS.'/pdev_evp/'.$row['file'];

        dump($file);

        if (file_exists($file)) {
            if (!unlink($file)) {
                $this->notify('failure', $this->core->lang['embededvideoplayer']['delete_file_failure']);
            } else {
                $this->notify('success', $this->core->lang['embededvideoplayer']['delete_file_success']);
                $this->core->db('pdev_evp')->delete($id);
            }
        }
        redirect(url([ADMIN, 'embededvideoplayer', 'index']));
    }
}
