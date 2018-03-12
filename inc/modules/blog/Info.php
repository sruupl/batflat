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

return [
    'name'          =>  $core->lang['blog']['module_name'],
    'description'   =>  $core->lang['blog']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.3',
    'compatibility'    =>    '1.3.*',
    'icon'          =>  'pencil-square',

    'pages'            =>  ['Blog' => 'blog'],

    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `blog` (
                        `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                        `title`	TEXT NOT NULL,
                        `slug`	TEXT NOT NULL,
                        `user_id`	INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                        `content`	TEXT NOT NULL,
                        `intro`	TEXT DEFAULT NULL,
                        `cover_photo`	TEXT DEFAULT NULL,
                        `status`	INTEGER NOT NULL,
                        `lang`	TEXT NOT NULL,
                        `comments`	INTEGER DEFAULT 1,
                        `markdown`	INTEGER DEFAULT 0,
                        `published_at`	INTEGER DEFAULT 0,
                        `updated_at`	INTEGER NOT NULL,
                        `created_at`	INTEGER NOT NULL
                    );");
        $core->db()->pdo()->exec('CREATE TABLE "blog_tags" (
                        `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                        `name`	TEXT,
                        `slug`	TEXT
                    );');
        $core->db()->pdo()->exec('CREATE TABLE `blog_tags_relationship` (
                        `blog_id`	INTEGER NOT NULL REFERENCES blog(id) ON DELETE CASCADE,
                        `tag_id`	INTEGER NOT NULL REFERENCES blog_tags(id) ON DELETE CASCADE
                    );');
        
        $core->db()->pdo()->exec("INSERT INTO `blog` VALUES (1,'Let’s put a smile on that face','lets-put-a-smile-on-that-face',1,'<p>Every man who has lotted here over the centuries, has looked up to the light and imagined climbing to freedom. So easy, so simple! And like shipwrecked men turning to seawater foregoing uncontrollable thirst, many have died trying. And then here there can be no true despair without hope. So as I terrorize Gotham, I will feed its people hope to poison their souls. I will let them believe that they can survive so that you can watch them climbing over each other to stay in the sun. You can watch me torture an entire city. And then when you’ve truly understood the depth of your failure, we will fulfill Ra’s Al Ghul’s destiny. We will destroy Gotham. And then, when that is done, and Gotham is... ashes Then you have my permission to die.</p>','<p>You wanna know how I got these scars? My father was… a drinker, and a fiend. And one night, he goes off crazier than usual. Mommy gets the kitchen knife to defend herself. He doesn’t like that, not one bit. So, me watching he takes the knife to her, laughing while he does it.</p>','default.jpg',2,'en_english',1,0,".time().",".time().",".time().")");
        $core->db()->pdo()->exec("INSERT INTO `blog` VALUES (2,'Początek traktatu czasu panowania Fryderyka Wielkiego','poczatek-traktatu-czasu-panowania-fryderyka-wielkiego',1,'<p>Władzę poznawczą musiemy mu jego czyny z pobudki mogł co ująć od Dobra musiemy mu w przeciwnym razie niebyłoby najwyższego dobra był człowiek trwać ma: więc ma naturalna ustawa moralna wiara niejest wiedzą czyli Wendów. Więc ja niejest biernym. Nieskończoność Boską można było spodziewać zawdzięczającej nagrody, niż od tego pokazuje żem ja substancyą Każda kompozycya może np. niebo jako człowiek walczyć musi być wzruszona. Ale wszystkie te rzeczy naturalnych utworzeniem istoty jest przeciw sprawiedliwości Dobraj, którąby przestrzeń ograniczała. Przez wszechmocność Boską można przedstawić lepszy plan względem innych takim razie podług biegu rzeczy możliwe, więc w sobie warunki sprawowania się nie kunsztu. Dyogenes miał nic wydarzyć niemoże, ani więcej nad tą lub zupełne poznanie niebędzie czasem w piosence: Marusieńka po naszym pojęciom o przedmiotach, mają być wzniecone, ażeby Subjekt przez podzielenie realności, albo drugim przypadku bez różnicy w Dobru: że często chwalebna poczciwość upadła, gdyby nasza własna wina. Tak też takie postępowanie niebyłoby najwyższego dobra był ideał świętości Dobraj. Kiedy więc nie było powszechne, tedyćby go czas ograniczał.</p>','<p>Władzę poznawczą musiemy mu jego czyny z pobudki mogł co ująć od Dobra musiemy mu w przeciwnym razie niebyłoby najwyższego dobra był człowiek trwać ma: więc ma naturalna ustawa moralna wiara niejest wiedzą czyli Wendów. Więc ja niejest biernym.','default2.jpg',2,'pl_polski',1,0,".time().",".time().",".time().")");
        $core->db()->pdo()->exec("INSERT INTO `blog_tags` VALUES (1, 'hello world', 'hello-world'), (2, 'batflat', 'batflat'), (3, 'witaj świecie', 'witaj-swiecie')");
        $core->db()->pdo()->exec("INSERT INTO `blog_tags_relationship` VALUES (1, 1), (1, 2), (2, 3), (2, 2)");
        $core->db()->pdo()->exec("INSERT INTO `settings` 
                    (`module`, `field`, `value`)
                    VALUES
                    ('blog', 'perpage', '5'),
                    ('blog', 'disqus', ''),
                    ('blog', 'dateformat', 'M d, Y'),
                    ('blog', 'title', 'Blog'),
                    ('blog', 'desc', '... Why so serious? ...'),
                    ('blog', 'latestPostsCount', '5')
        ");

        if (!is_dir(UPLOADS."/blog")) {
            mkdir(UPLOADS."/blog", 0777);
        }

        copy(MODULES.'/blog/img/default.jpg', UPLOADS.'/blog/default.jpg');
        copy(MODULES.'/blog/img/default.jpg', UPLOADS.'/blog/default2.jpg');
    },
    'uninstall'     =>  function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `blog`");
        $core->db()->pdo()->exec("DROP TABLE `blog_tags`");
        $core->db()->pdo()->exec("DROP TABLE `blog_tags_relationship`");
        $core->db()->pdo()->exec("DELETE FROM `settings` WHERE `module` = 'blog'");
        
        deleteDir(UPLOADS."/blog");
    }
];
