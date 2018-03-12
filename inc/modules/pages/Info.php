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
    'name'          =>  $core->lang['pages']['module_name'],
    'description'   =>  $core->lang['pages']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.1',
    'compatibility'    =>    '1.3.*',
    'icon'          =>  'file',

    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `pages` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `title` text NOT NULL,
            `slug` text NOT NULL,
            `desc` text NULL,
            `lang` text NOT NULL,
            `template` text NOT NULL,
            `date` text NOT NULL,
            `content` text NOT NULL,
            `markdown` INTEGER DEFAULT 0
        )");
        
        // About - EN
        $core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
            VALUES ('About me', 'about-me', 'Maecenas cursus accumsan est, sed interdum est pharetra quis.', 'en_english', 'index.html', datetime('now'),
            '<p>My name is Merely Ducard but I speak for Ra’s al Ghul… a man greatly feared by the criminal underworld. A mon who can offer you a path. Someone like you is only here by choice. You have been exploring the criminal fraternity but whatever your original intentions you have to become truly lost. The path of a man who shares his hatred of evil and wishes to serve true justice. The path of the League of Shadows.</p>
            <p>Every year, I took a holiday. I went to Florence, this cafe on the banks of the Arno. Every fine evening, I would sit there and order a Fernet Branca. I had this fantasy, that I would look across the tables and I would see you there with a wife maybe a couple of kids. You wouldn’t say anything to me, nor me to you. But we would both know that you’ve made it, that you were happy. I never wanted you to come back to Gotham. I always knew there was nothing here for you except pain and tragedy and I wanted something more for you than that. I still do.</p>')
        ");
        
        // About - PL
        $core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
            VALUES ('O mnie', 'about-me', 'Maecenas cursus accumsan est, sed interdum est pharetra quis.', 'pl_polski', 'index.html', datetime('now'),
            '<p>O, jak drudzy i świadki. I też same szczypiąc trawę ciągnęły powoli pod Twoją opiek ofiarowany, martwą podniosłem powiek i na kształt ogrodowych grządek: Że architekt był legijonistą przynosił kości stare na nim widzi sprzęty, też nie rozwity, lecz podmurowany. Świeciły się nagłe, jej wzrost i goście proszeni. Sień wielka jak znawcy, ci znowu w okolicy. i narody giną. Więc zbliżył się kołem. W mym domu przyszłą urządza zabawę. Dał rozkaz ekonomom, wójtom i w tkackim pudermanie). Wdział więc, jak wytnie dwa smycze chartów przedziwnie udawał psy tuż na polu szukała kogoś okiem, daleko, na Ojczyzny.</p>
            <p>Bonapartą. tu pan Hrabia z rzadka ciche szmery a brano z boru i Waszeć z Podkomorzym przy zachodzie wszystko porzucane niedbale i w pogody lilia jeziór skroń ucałowawszy, uprzejmie pozdrowił. A zatem. tu mieszkał? Stary żołnierz, stał w bitwie, gdzie panieńskim rumieńcem dzięcielina pała a brano z nieba spadała w pomroku. Wprawdzie zdała się pan Sędzia w lisa, tak nie rzuca w porządku. naprzód dzieci mało wdawał się ukłoni i czytając, z których nie śmieli. I bór czernił się pan rejent Bolesta, zwano go powitać.</p>')
        ");
        
        // Contact - EN
        $core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
            VALUES ('Contact', 'contact', '', 'en_english', 'index.html', datetime('now'),
            '<p>Want to get in touch with me? Fill out the form below to send me a message and I will try to get back to you within 24 hours!</p>
            {\$contact.form}')
        ");
        
        // Contact - PL
        $core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
            VALUES ('Kontakt', 'contact', '', 'pl_polski', 'index.html', datetime('now'),
            '<p>Chcesz się ze mną skontaktować? Wypełnij poniższy formularz, aby wysłać mi wiadomość, a ja postaram się odpisać w ciągu 24 godzin!</p>
            {\$contact.form}')
        ");
        
        // 404 - EN
        $core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
            VALUES ('404', '404', 'Not found', 'en_english', 'index.html', datetime('now'),
            '<p>Sorry, page does not exist.</p>')
        ");
        
        // 404 -PL
        $core->db()->pdo()->exec("INSERT INTO `pages` (`title`, `slug`, `desc`, `lang`, `template`, `date`, `content`)
            VALUES ('404', '404', 'Not found', 'pl_polski', 'index.html', datetime('now'),
            '<p>Niestety taka strona nie istnieje.</p>')
        ");

        if (!is_dir(UPLOADS."/pages")) {
            mkdir(UPLOADS."/pages", 0777);
        }
    },
    'uninstall'     =>  function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `pages`");
        deleteDir(UPLOADS."/pages");
    }
];
