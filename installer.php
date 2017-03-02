<?php
function includeDDHooks()
{
    $oCache = Phpfox::getLib('cache');
    $oCache->remove();
    file_put_contents(PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'app.lock', '');
    new \Core\App(true);
    Phpfox_Plugin::set();
}

function installv1_0_0()
{

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_category') . "` (
      `category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      `name` varchar(255) NOT NULL,
      `name_url` varchar(255) DEFAULT NULL,
      `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
      `used` int(10) unsigned NOT NULL DEFAULT '0',
      `ordering` int(11) unsigned NOT NULL DEFAULT '0',
      `title` VARCHAR( 255 ) NULL ,
      `keywords` VARCHAR( 255 ) NULL ,
      `description` TEXT NULL ,
      PRIMARY KEY (`category_id`),
      KEY `parent_id` (`parent_id`,`is_active`),
      KEY `is_active` (`is_active`,`name_url`)
        );");


    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_fields') . "` (
      `field_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `type` varchar(255) DEFAULT 'string',
      `name` varchar(255) NOT NULL,
      `caption_phrase` varchar(255) DEFAULT NULL,
      `rules` varchar(255) DEFAULT NULL,
      `extra` text(5000) DEFAULT NULL,
      `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      `is_filter` tinyint(1) NOT NULL DEFAULT '0',
      `ordering` int(11) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`field_id`),
      KEY `is_active` (`is_active`)
        );");

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_category_fields') . "` (
      `field_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
      `category_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
      KEY `field_id` (`field_id`),
      KEY `category_id` (`category_id`)
        );");

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download') . "` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_id` int(11) NOT NULL,
      `privacy` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `images` TEXT NULL,
      `user_id` int( 11 ) NOT NULL DEFAULT  '0',
      `is_active` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `time_stamp` int( 10 ) NOT NULL,
      `expire_timestamp` int( 16 ) NULL,
      `featured` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `sponsored` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `highlighted` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `youtube_video` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `youtube_video_url` VARCHAR( 255 ) NULL,
      `total_comment` int(10) NOT NULL DEFAULT  '0',
      `total_like` int( 10 ) NOT NULL DEFAULT  '0',
      `total_view` INT( 11 )  NOT NULL DEFAULT  '0',
      `total_download` INT  NOT NULL DEFAULT  '0',
      `is_expired` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `rating` VARCHAR( 25 ) NOT NULL DEFAULT  '',
      `_title` VARCHAR(500) NOT NULL DEFAULT  '',
      PRIMARY KEY (`id`),
      KEY `category_id` (`category_id`),
      KEY `is_active` (`is_active`),
      KEY `user_id` (`user_id`),
      KEY `is_expired` (`is_expired`),
      KEY `featured` (`featured`));");

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_plans') . '` (
      `plan_id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(200) NOT NULL,
      `allowed_count_pictures` int(3) NOT NULL,
      `life_time` int(3) NOT NULL,
      `price` DECIMAL( 14, 2 ) NOT NULL DEFAULT  \'0.00\',
      `price_currency_id` CHAR( 3 ) NOT NULL,
      `featured` DECIMAL( 14, 2 ) NOT NULL DEFAULT \'0.00\',
      `featured_allowed` TINYINT( 1 ) NOT NULL DEFAULT  \'0\',
      `sponsored` DECIMAL( 14, 2 ) NOT NULL DEFAULT \'0.00\',
      `sponsored_allowed` TINYINT( 1 ) NOT NULL DEFAULT  \'0\',
      `highlighted` DECIMAL( 14, 2 ) NOT NULL DEFAULT \'0.00\',
      `highlighted_allowed` TINYINT( 1 ) NOT NULL DEFAULT  \'0\',
      `youtube_video` DECIMAL( 14, 2 ) NOT NULL DEFAULT \'0.00\',
      `youtube_video_allowed` TINYINT( 1 ) NOT NULL DEFAULT  \'0\',
      `user_groups` VARCHAR( 255 ) NULL,
       PRIMARY KEY (`plan_id`)
    )');

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_invoice') . '` (
      `invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `dd_id` int(10) unsigned NOT NULL,
      `user_id` int(10) unsigned NOT NULL,
      `currency_id` char(3) NOT NULL,
      `price` decimal(14,2) NOT NULL,
      `status` varchar(20) DEFAULT NULL,
      `time_stamp` int(10) unsigned NOT NULL,
      `time_stamp_paid` int(10) unsigned NOT NULL DEFAULT \'0\',
      `data` TEXT NOT NULL,
      `type` VARCHAR( 15 ) NOT NULL DEFAULT  \'options\',
      PRIMARY KEY (`invoice_id`),
      KEY `dd_id` (`dd_id`),
      KEY `user_id` (`user_id`),
      KEY `dd_id_2` (`dd_id`,`status`),
      KEY `dd_id_3` (`dd_id`,`user_id`,`status`)
    );');

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_dd_plan') . '` (
      `dd_plan_id` int(11) NOT NULL AUTO_INCREMENT,
      `dd_id` int(11) NOT NULL,
      `plan_id` int(11) NOT NULL,
      `info` text NOT NULL,
      PRIMARY KEY (`dd_plan_id`),
      KEY `dd_id` (`dd_id`,`plan_id`)
    );');

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_download') . '` (
        `download_id` int(11) NOT NULL AUTO_INCREMENT,
        `dd_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `field` varchar(255) NOT NULL,
        `limit` int(11) NOT NULL DEFAULT \'0\',
        PRIMARY KEY (`download_id`),
        KEY `dd_id` (`dd_id`,`user_id`)
    );');

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_invite') . '` (
        `invite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `dd_id` int(10) unsigned NOT NULL,
        `type_id` tinyint(1) NOT NULL DEFAULT \'0\',
        `visited_id` tinyint(1) NOT NULL DEFAULT \'0\',
        `user_id` int(10) unsigned NOT NULL DEFAULT \'0\',
        `invited_user_id` int(10) unsigned NOT NULL DEFAULT \'0\',
        `invited_email` varchar(100) DEFAULT NULL,
        `time_stamp` int(10) unsigned NOT NULL,
        PRIMARY KEY (`invite_id`),
        KEY `dd_id` (`dd_id`),
        KEY `dd_id_2` (`dd_id`,`invited_user_id`),
        KEY `invited_user_id` (`invited_user_id`),
        KEY `dd_id_3` (`dd_id`,`visited_id`),
        KEY `dd_id_4` (`dd_id`,`visited_id`,`invited_user_id`),
        KEY `visited_id` (`visited_id`,`invited_user_id`)
        ); ');

    db()->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('digital_download_rating') . '` (
      `dd_id` int(10) unsigned NOT NULL,
      `user_id` int(10) unsigned NOT NULL,
      `rating` varchar(2) DEFAULT NULL,
      `time_stamp` int(10) unsigned NOT NULL,
      `ip_address` varchar(15) DEFAULT NULL,
      KEY `dd_id` (`dd_id`,`user_id`)
    )');

    $aCrons = [
        [
            'module_id' => 'digitaldownload',
            'product_id' => 'phpfox',
            'next_run' => 0,
            'last_run' => 0,
            'type_id' => 2,
            'every' => 1,
            'is_active' => 1,
            'php_code' => '\Phpfox::getService(\'digitaldownload.cron\')->expireDD();'
        ],
    ];

    foreach($aCrons as $aCron) {
        db()->insert(Phpfox::getT('cron', $aCron));
    }

    if (!is_dir(Phpfox::getParam('core.dir_pic') . 'digitaldownload/')) {
        mkdir(Phpfox::getParam('core.dir_pic') . 'digitaldownload/', 0777, true);
    }

    $aPhrase  = [
        'digitaldownload_full_name_has_purchased_an_item_of_yours_on_site_name' => '{full_name} has purchased one of your items on {site_name}.

Item Name: {title}
Item Link: <a href="{link}">{link}</a>
Users Name: {full_name}
Users Profile: <a href="{user_link}">{user_link}</a>
Price: {price}',
        'digitaldownload_item_sold_title' => 'Item Sold: {title}',
        'digitaldownload_full_name_liked_your_dd_title' => '{full_name} liked your item "{title}"',
        'digitaldownload_full_name_liked_your_dd_message' => '{full_name} liked item "<a href="{link}">{title}</a>"
To view this dd follow the link below:
<a href="{link}">{link}</a>',

        'digitaldownload_user_name_liked_gender_own_dd_title' => '{user_name} liked {gender} own item "{title}"',
        'digitaldownload_user_names_liked_your_dd_title' => '{user_names} liked your item "{title}"',
        'digitaldownload_user_names_liked_span_class_drop_data_user_full_name_s_span_dd_title' => '{user_names} liked <span class="drop_data_user">{full_name}\'s</span> item "{title}"',

        'digitaldownload_user_names_commented_on_gender_dd_title' => '{user_names} commented on {gender} item "{title}"',
        'digitaldownload_user_names_commented_on_your_dd_title' => '{user_names} commented on your item "{title}"',
        'digitaldownload_user_names_commented_on_span_class_drop_data_user_full_name_s_span_dd_title' => '{user_names} commented on <span class="drop_data_user">{full_name}\'s</span> item "{title}"',

        'digitaldownload_item_expired_subject' => 'Items expiration report from {web_site}',
        'digitaldownload_item_expired_message' => 'This email contains information regarding your items expiration on {web_site}.</br><p>The following items have expired:</p><ul>{item_list}</ul>',

        'digitaldownload_full_name_invited_you_to_view_the_digitaldownload_item_title' => '{full_name} invited you to view the digital download item "{title}".

To check out this item, follow the link below:
<a href="{link}">{link}</a>"',
        'digitaldownload_full_name_added_the_following_personal_message' => '{full_name} added the following personal message',
        'digitaldownload_full_name_invited_you_to_view_the_item_title' => '{full_name} invited you to view the item "{title}".',


        'digitaldownload_view_mode_list' => 'List',
        'digitaldownload_view_mode_grid' => 'Grid',
    ];

    $aLanguages = \Language_Service_Language::instance()->getAll();

    foreach($aPhrase as $sVar => $sText ) {
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            $aText[$aLanguage['language_id']] = $sText;
        }
        $aVal = [
            'product_id' => 'phpfox',
            'module' => 'digitaldownload|digitaldownload',
            'var_name' => $sVar,
            'text' => $aText
        ];
        \Language_Service_Phrase_Process::instance()->add($aVal);
    }


    //sample data

    \Phpfox_Module::instance()
        ->addServiceNames([
            'digitaldownload.category' => '\Apps\CM_DigitalDownload\Service\Category',
            'digitaldownload.field' => '\Apps\CM_DigitalDownload\Service\Field',
            'digitaldownload.plan' => '\Apps\CM_DigitalDownload\Service\Plan',
            'digitaldownload.categoryField' => '\Apps\CM_DigitalDownload\Service\CategoryField',
            'digitaldownload.dd' => '\Apps\CM_DigitalDownload\Service\Digitaldownload\DigitalDownload',
        ]);

    $aCategories = [
        [
            'parent_id' => 0, //id 1
            'name' => 'Photo',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 0, //id 2
            'name' => 'Music',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 0,
            'name' => 'Video', //id 3
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 0, //id 4
            'name' => 'Software',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 0, //id 5
            'name' => 'Documents',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 0, //id 6
            'name' => 'CMS',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        //photo sub categories
        [
            'parent_id' => 1, //id 7
            'name' => 'Comedy',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 8
            'name' => 'Digital Art',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 9
            'name' => 'Photography',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 10
            'name' => 'Traditional Art',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 11
            'name' => 'Film & Animation',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 12
            'name' => 'Designs & Interfaces',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 13
            'name' => 'Game Development Art',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 14
            'name' => 'Artisan Crafts',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 15
            'name' => 'Fractal Art',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 16
            'name' => 'Cartoons & Comics',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 17
            'name' => 'Resources & Stock Images',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 18
            'name' => 'Literature',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 19
            'name' => 'Fan Art',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 20
            'name' => 'Anthro',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 21
            'name' => 'Community Projects',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 22
            'name' => 'People',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 23
            'name' => 'Pets & Animals',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 24
            'name' => 'Science & Technology',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 1,//id 25
            'name' => 'Sports',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],

        //music sub categories

        [
            'parent_id' => 2,//id 26
            'name' => 'Hip Hop',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 2,//id 27
            'name' => 'Rock',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ], [
            'parent_id' => 2,//id 28
            'name' => 'Pop',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 29
            'name' => 'Alternative',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 30
            'name' => 'Country',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 31
            'name' => 'Indie',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 32
            'name' => 'Rap',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 33
            'name' => 'R&B',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 34
            'name' => 'Metal',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 35
            'name' => 'Punk',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 36
            'name' => 'Hardcore',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 37
            'name' => 'House',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 38
            'name' => 'Electronica',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 39
            'name' => 'Techno',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 40
            'name' => 'Reggae',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 41
            'name' => 'Latin',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 42
            'name' => 'Jazz',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 43
            'name' => 'Classic Rock',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 44
            'name' => 'Blues',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 45
            'name' => 'Folk',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 2,//id 46
            'name' => 'Progressive',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        //video sub categories
        [
            'parent_id' => 3,//id 47
            'name' => 'Gaming',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 3,//id 48
            'name' => 'Film & Entertainment',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 3,//id 49
            'name' => 'Comedy',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 3,//id 50
            'name' => 'Music',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        //sub categories soft
        [
            'parent_id' => 4,//id 51
            'name' => 'Windows',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 4,//id 52
            'name' => '*nix',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 4,//id 53
            'name' => 'OS X(MAC)',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 4,//id 54
            'name' => 'Android',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],

        //CMS sub categories
        [
            'parent_id' => 6,//id 55
            'name' => 'Phpfox',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
        [
            'parent_id' => 6,//id 56
            'name' => 'Wordpress',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],

        //doc sub categ
        [
            'parent_id' => 5,//id 57
            'name' => 'Essay',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 5,//id 58
            'name' => 'Graduate work',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 5,//id 59
            'name' => 'Business plan',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],

        //phpfox sub cat
        [
            'parent_id' => 55,//id 60
            'name' => 'App',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 55,//id 61
            'name' => 'Theme',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],

        //wordpress sub categ
        [
            'parent_id' => 56,//id 62
            'name' => 'Plugin',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],[
            'parent_id' => 56,//id 63
            'name' => 'Theme',
            'title' => '$title',
            'keywords' => '$title, $description',
            'description' => '$description',
            'is_active' => '1',
        ],
    ];

    /**
     * @var $oForm  \Apps\CM_DigitalDownload\Lib\Form\DataBinding\Form
     */
    $oForm = Phpfox::getService('digitaldownload.category')->getForm();
    foreach($aCategories as $aCategory) {
        foreach($aCategory as $sCatField => $sCatValue){
            if ($sCatField == 'name') {
                foreach ($aLanguages as $aLanguage) {
                    Phpfox_Request::instance()->set('name_' . $aLanguage['language_id'], $sCatValue);
                }
            } else {
                $oForm->setFieldValue($sCatField, $sCatValue);
            }
        }
        $oForm->save();
    }

    $aFields = [
        [
            'type' => 'string',
            'name' => 'title',
            'rules' => 'required|255:maxLength',
            'is_filter' => '1',
            'is_active' => '1',
            'caption_phrase' => 'Title',
        ],
        [
            'type' => 'text',
            'name' => 'description',
            'rules' => '1000:maxLength',
            'is_filter' => '1',
            'is_active' => '1',
            'caption_phrase' => 'Description',
        ],
        [
            'type' => 'dd',
            'name' => 'file',
            'rules' => 'required',
            'is_filter' => '0',
            'is_active' => '1',
            'caption_phrase' => 'File',
        ],
    ];

    includeDDHooks();

    $oForm = Phpfox::getService('digitaldownload.field')->getForm();
    foreach($aFields as $aField) {
        foreach($aField as $sField => $sValue){
            if ($sField == 'caption_phrase') {
                foreach ($aLanguages as $aLanguage) {
                    Phpfox_Request::instance()->set('caption_phrase_' . $aLanguage['language_id'], $sValue);
                }
            } else {
                $oForm->setFieldValue($sField, $sValue);
            }
        }
        $oForm->save();
        Phpfox::getService('digitaldownload.field')->addField($oForm);
    }

    $aFields =  Phpfox::getService('digitaldownload.field')->all();
    $aFieldIds = [];

    foreach($aFields as  $aField) {
        $aFieldIds[] = $aField['field_id'];
    }

    $aCategories  = Phpfox::getService('digitaldownload.category')->getList();
    foreach($aCategories as $aCategory) {
        Phpfox::getService('digitaldownload.categoryField')->sync($aFieldIds, $aCategory['category_id']);
    }

    $aPlans = [
        [
            'name' => 'Free plan',
            'price_currency_id' => 'USD',
            'price' => '0.00',
            'allowed_count_pictures' => 3,
            'life_time' => 30,
            'featured' => [
                'allowed' => 1,
                'price' => '0.05',
            ],
            'sponsored' => [
                'allowed' => 1,
                'price' => '0.05',
            ],
            'highlighted' => [
                'allowed' => 1,
                'price' => '0.01',
            ],
            'youtube_video' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'user_groups' => [1, 2],
        ],
        [
            'name' => 'Demo Plan',
            'price_currency_id' => 'USD',
            'price' => '1.00',
            'allowed_count_pictures' => 5,
            'life_time' => 60,
            'featured' => [
                'allowed' => 1,
                'price' => '3.00',
            ],
            'sponsored' => [
                'allowed' => 1,
                'price' => '2.00',
            ],
            'highlighted' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'youtube_video' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'user_groups' => [1, 2],
        ],
        [
            'name' => 'Admin plan',
            'price_currency_id' => 'USD',
            'price' => '0.00',
            'allowed_count_pictures' => 3,
            'life_time' => 60,
            'featured' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'sponsored' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'highlighted' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'youtube_video' => [
                'allowed' => 1,
                'price' => '0.00',
            ],
            'user_groups' => [1],
        ],
    ];

    $oForm = Phpfox::getService('digitaldownload.plan')->getForm(['form_id' => 'dd-plan']);
    foreach($aPlans as $aPlan) {
        foreach($aPlan as $sField => $mValue) {
            if ($sField == 'name') {
                foreach ($aLanguages as $aLanguage) {
                    Phpfox_Request::instance()->set('name_' . $aLanguage['language_id'], $mValue);
                }
            }elseif($sField == 'price_currency_id'){
                Phpfox_Request::instance()->set($sField, $mValue);
            } elseif(is_array($mValue) && $sField != 'user_groups') { //plan option
                $oForm->getField($sField)->setMValue($aPlan);
            } else  {
                $oForm->setFieldValue($sField, $mValue);
            }
        }
        $oForm->save();
    }

    copy(PHPFOX_DIR_SITE_APPS . 'CM_DigitalDownload' . PHPFOX_DS . 'stubs' . PHPFOX_DS . 'dd_no_image.jpg',
        Phpfox::getParam('core.dir_pic') . 'digitaldownload' . PHPFOX_DS . 'dd_no_image.jpg');
}

installv1_0_0();