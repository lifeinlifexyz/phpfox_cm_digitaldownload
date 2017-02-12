<?php

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
      `expire_timestamp` int( 10 ) NULL,
      `featured` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `sponsored` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `highlighted` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `total_comment` int(10) NOT NULL DEFAULT  '0',
      `total_like` int( 10 ) NOT NULL DEFAULT  '0',
      `total_view` INT( 11 ) NULL,
      `total_download` INT NULL,
      `is_expired` TINYINT( 1 ) NOT NULL DEFAULT  '0'
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

    $aCrons[] = [
        'module_id' => 'digitaldownload',
        'product_id' => 'phpfox',
        'next_run' => 0,
        'last_run' => 0,
        'type_id' => 2,
        'every' => 1,
        'is_active' => 1,
        'php_code' => '\Phpfox::getService(\'digitaldownload.cron\')->expireDD();'
    ];
    foreach($aCrons as &$aCron) {
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
        'digitaldownload_full_name_liked_your_listing_title' => '{full_name} liked your item "{title}"',
        'digitaldownload_full_name_liked_your_listing_message' => '{full_name} liked item "<a href="{link}">{title}</a>"
To view this listing follow the link below:
<a href="{link}">{link}</a>',

        'digitaldownload_user_name_liked_gender_own_listing_title' => '{user_name} liked {gender} own item "{title}"',
        'digitaldownload_user_names_liked_your_listing_title' => '{user_names} liked your item "{title}"',
        'digitaldownload_user_names_liked_span_class_drop_data_user_full_name_s_span_listing_title' => '{user_names} liked <span class="drop_data_user">{full_name}\'s</span> item "{title}"',

        'digitaldownload_user_names_commented_on_gender_listing_title' => '{user_names} commented on {gender} item "{title}"',
        'digitaldownload_user_names_commented_on_your_listing_title' => '{user_names} commented on your item "{title}"',
        'digitaldownload_user_names_commented_on_span_class_drop_data_user_full_name_s_span_listing_title' => '{user_names} commented on <span class="drop_data_user">{full_name}\'s</span> item "{title}"',

        'digitaldownload_item_expired_subject' => 'Items expiration report from {web_site}',
        'digitaldownload_item_expired_message' => 'This email contains information regarding your items expiration on {web_site}.</br><p>The following items have expired:</p><ul>{item_list}</ul>',
    ];

    foreach($aPhrase as $sVar => $sText ) {
        $aVal = [
            'product_id' => 'phpfox',
            'module' => 'digitaldownload|digitaldownload',
            'var_name' => $sVar,
            'text' => $sText
        ];
        \Language_Service_Phrase_Process::instance()->add($aVal);
    }
}

installv1_0_0();