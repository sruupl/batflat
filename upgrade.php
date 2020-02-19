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

if (!defined("UPGRADABLE")) {
    exit();
}

function rrmdir($dir)
{
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        if (is_dir("$dir/$file")) {
            rrmdir("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
    return rmdir($dir);
}

switch ($version) {
    case '1.0.0':
        /*
            Change homepage id to slug
            */
        $homepage = $this->core->getSettings('settings', 'homepage');
        $homepage = $this->core->db('pages')->where('id', $homepage)->oneArray();
        $this->core->db('settings')->where('field', 'homepage')->save(['value' => $homepage['slug']]);

        /*
            Add 404 pages if does not exist
            */
        if (!$this->core->db('pages')->where('slug', '404')->where('lang', 'en_english')->oneArray()) {
            // 404 - EN
            $this->core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
                VALUES ('404', '404', 'Not found', 'en_english', 'index.html', datetime('now'),
                '<p>Sorry, page does not exist.</p>')
            ");
        }
        if (!$this->core->db('pages')->where('slug', '404')->where('lang', 'pl_polski')->oneArray()) {
            // 404 -PL
            $this->core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
                VALUES ('404', '404', 'Not found', 'pl_polski', 'index.html', datetime('now'),
                '<p>Niestety taka strona nie istnieje.</p>')
            ");
        }

        /*
            Remove LESS directory
            */
        deleteDir('inc/less');

        // Upgrade version
        $return = '1.0.1';

    case '1.0.1':
        $return = "1.0.2";

    case '1.0.2':
        $return = "1.0.3";

    case '1.0.3':
        // Add columns for markdown flag - blog and pages
        $this->core->db()->pdo()->exec("ALTER TABLE blog ADD COLUMN markdown INTEGER DEFAULT 0");
        $this->core->db()->pdo()->exec("ALTER TABLE pages ADD COLUMN markdown INTEGER DEFAULT 0");
        $this->core->db()->pdo()->exec("CREATE TABLE `login_attempts` (
            `ip`	TEXT NOT NULL,
            `attempts`	INTEGER NOT NULL,
            `expires`	INTEGER NOT NULL DEFAULT 0
        )");
        $this->rcopy(BASE_DIR.'/tmp/update/admin', BASE_DIR.'/admin');
        $return = "1.0.4";

    case '1.0.4':
        $return = '1.0.4a';

    case '1.0.4a':
        $this->core->db()->pdo()->exec("ALTER TABLE modules ADD COLUMN sequence INTEGER DEFAULT 0");
        $this->rcopy(BASE_DIR.'/tmp/update/admin', BASE_DIR.'/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/.htaccess', BASE_DIR.'/.htaccess');
        $this->rcopy(BASE_DIR.'/tmp/update/inc/fonts', BASE_DIR.'/inc/fonts');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        $return = '1.0.5';

    case '1.0.5':
        if (file_exists(BASE_DIR.'/themes/default')) {
            $this->rcopy(BASE_DIR.'/tmp/update/themes/default/preview.png', BASE_DIR.'/themes/default/preview.png');
            $this->rcopy(BASE_DIR.'/tmp/update/themes/default/manifest.json', BASE_DIR.'/themes/default/manifest.json');
            $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        }
        $return = '1.1.0';

    case '1.1.0':
        $this->core->db()->pdo()->exec('CREATE TABLE "blog_tags" (
                        `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                        `name`	TEXT,
                        `slug`  TEXT
                    );');
        $this->core->db()->pdo()->exec('CREATE TABLE `blog_tags_relationship` (
                        `blog_id`	INTEGER NOT NULL,
                        `tag_id`	INTEGER NOT NULL
                    );');
        $this->core->db()->pdo()->exec("INSERT INTO `settings`
                    (`module`, `field`, `value`)
                    VALUES
                    ('contact', 'email', 1),
                    ('contact', 'driver', 'mail'),
                    ('contact', 'phpmailer.server', 'smtp.example.com'),
                    ('contact', 'phpmailer.port', '587'),
                    ('contact', 'phpmailer.username', 'login@example.com'),
                    ('contact', 'phpmailer.name', 'Batflat contact'),
                    ('contact', 'phpmailer.password', 'yourpassword')");

        $this->rcopy(BASE_DIR.'/tmp/update/inc/core', BASE_DIR.'/inc/core');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/admin', BASE_DIR.'/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/index.php', BASE_DIR.'/index.php');
        $return = '1.2.0';

    case '1.2.0':
        $return = '1.2.1';

    case '1.2.1':
        register_shutdown_function(function () {
            sleep(2);
            redirect(url([ADMIN, 'settings', 'updates']));
        });

        $lang = $this->core->getSettings('settings', 'lang_site');

        $this->rcopy(BASE_DIR.'/tmp/update/admin', BASE_DIR.'/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/index.php', BASE_DIR.'/index.php');
        $this->rcopy(BASE_DIR.'/tmp/update/LICENSE.txt', BASE_DIR.'/LICENSE.txt');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/batblog', BASE_DIR.'/themes/batblog');

        // Settings
        $this->core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'timezone', '".date_default_timezone_get()."')");
        $this->core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'license', '')");
        $this->core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('blog', 'latestPostsCount', '5')");

        // Users
        $this->core->db()->pdo()->exec("ALTER TABLE users ADD COLUMN description TEXT NULL");
        $this->core->db()->pdo()->exec("ALTER TABLE users ADD COLUMN avatar TEXT NULL");
        $this->core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `remember_me` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `token` text NOT NULL,
            `user_id` integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            `expiry` integer NOT NULL
        )");
        if (!is_dir(UPLOADS."/users")) {
            mkdir(UPLOADS."/users", 0777);
        }

        $users = $this->core->db('users')->toArray();
        foreach ($users as $user) {
            $avatar = uniqid('avatar').'.png';
            copy(MODULES.'/users/img/default.png', UPLOADS.'/users/'.$avatar);
            $this->core->db('users')->where('id', $user['id'])->save(['avatar' => $avatar]);
        }

        // Blog
        $this->core->db()->pdo()->exec("ALTER TABLE blog ADD COLUMN lang TEXT NULL");
        $this->core->db()->pdo()->exec("UPDATE blog SET lang = '".$lang."'");

        // Snippets
        $snippets = $this->core->db('snippets')->toArray();
        foreach ($snippets as $snippet) {
            $this->core->db('snippets')->where('id', $snippet['id'])->save(['content' => '{lang: '.$lang.'}'.$snippet['content'].'{/lang}']);
        }
        $return = '1.3.0';

    case '1.3.0':
        $this->core->db()->pdo()->exec("ALTER TABLE navs_items ADD COLUMN class TEXT NULL");
        $return = '1.3.1';

    case '1.3.1':
        $this->rcopy(BASE_DIR.'/backup/'.$backup_date.'/inc/core/defines.php', BASE_DIR.'/inc/core/defines.php');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        $return = '1.3.1a';

    case '1.3.1a':
        $return = '1.3.1b';

    case '1.3.1b':
        $return = '1.3.2';

    case '1.3.2':
        $this->rcopy(BASE_DIR.'/tmp/update/admin', BASE_DIR.'/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        $this->core->db()->pdo()->exec("INSERT INTO modules (`dir`) VALUES ('devbar')");
        $return = '1.3.3';

    case '1.3.3':
        $this->rcopy(BASE_DIR.'/tmp/update/admin', BASE_DIR.'/admin');
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin', BASE_DIR.'/themes/admin');
        $return = '1.3.4';

    case '1.3.4':
        $this->rcopy(BASE_DIR.'/tmp/update/themes/admin/css', BASE_DIR.'/themes/admin/css');
        $this->core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('contact', 'checkbox.switch', '0')");
        $this->core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('contact', 'checkbox.content', 'I agree to the processing of personal data...')");
        $return = '1.3.5';

    case '1.3.5':
        $return = '1.3.6';
}

return $return;