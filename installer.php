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
      `digital_download` VARCHAR( 255 ) NOT NULL,
      `privacy` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `price` DECIMAL( 14, 2 ) NOT NULL DEFAULT  '0.00',
      `price_currency_id` CHAR( 3 ) NOT NULL,
      `images` TEXT NOT NULL DEFAULT  '';
      `user_id` INT( 11 ) NOT NULL DEFAULT  '0',
      `is_active` TINYINT( 1 ) NOT NULL DEFAULT  '0',
      `time_stamp` INT( 10 ) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `category_id` (`category_id`),
      KEY `is_active` (`is_active`),
      KEY `user_id` (`user_id`),
      );");

    if (!is_dir(Phpfox::getParam('core.dir_pic') . 'digitaldownload/')) {
        mkdir(Phpfox::getParam('core.dir_pic') . 'digitaldownload/', 0777, true);
    }

}

installv1_0_0();