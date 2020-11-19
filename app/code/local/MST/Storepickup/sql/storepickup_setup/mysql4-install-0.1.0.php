<?php

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('storepickup/storepickup')} (
  `storepickup_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `position` smallint(6) NOT NULL DEFAULT '0',
  `phone_number` varchar(255) NOT NULL,
  `fax_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mapicon` text NOT NULL,
  `zoom_level` int(11) NOT NULL DEFAULT '0',
  `latitude` varchar(35) NOT NULL,
  `longitude` varchar(35) NOT NULL,
  `storepickup_status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `pickup_manager` varchar(255) NOT NULL,
  `pickup_phone` varchar(255) NOT NULL,
  `pickup_email` varchar(255) NOT NULL,
  `pickup_fax` varchar(255) NOT NULL,
  `pickup_receive_email` smallint(6) NOT NULL,
  `pickup_image` varchar(255) NOT NULL,
  `pickup_monday_open` time NOT NULL,
  `pickup_monday_close` time NOT NULL,
  `pickup_tuesday_open` time NOT NULL,
  `pickup_tuesday_close` time NOT NULL,
  `pickup_wednesday_open` time NOT NULL,
  `pickup_wednesday_close` time NOT NULL,
  `pickup_thursday_open` time NOT NULL,
  `pickup_thursday_close` time NOT NULL,
  `pickup_friday_open` time NOT NULL,
  `pickup_friday_close` time NOT NULL,
  `pickup_saturday_open` time NOT NULL,
  `pickup_saturday_close` time NOT NULL,
  `pickup_sunday_open` time NOT NULL,
  `pickup_sunday_close` time NOT NULL,
  PRIMARY KEY (`storepickup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

 CREATE TABLE IF NOT EXISTS {$this->getTable('storepickup/image')} (
 `image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image_delete` int(11) DEFAULT NULL,
  `options` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `storepickup_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

 CREATE TABLE IF NOT EXISTS {$this->getTable('storepickup/messages')} (
 `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `pickup_contact_name` varchar(255) NOT NULL,
  `pickup_contact_email` varchar(255) NOT NULL,
  `pickup_contact_message` text NOT NULL,
  `pickup_contact_at` datetime NOT NULL,
  `pickup_id` int(11) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

 CREATE TABLE IF NOT EXISTS {$this->getTable('storepickup/pickuporder')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pickup_order_id` int(11) NOT NULL,
  `pickup_id` int(11) NOT NULL,
  `time_pickup` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;CREATE TABLE IF NOT EXISTS {$this->getTable('mst_license')} (	license_id int(11) NOT NULL AUTO_INCREMENT,	domain_count varchar(255) NOT NULL,	domain_list varchar(255) NOT NULL,	path varchar(255) NOT NULL,	extension_code varchar(255) NOT NULL,	license_key varchar(255) NOT NULL,	created_time date NOT NULL,	domains varchar(255) NOT NULL,	is_valid tinyint(1) NOT NULL DEFAULT '0',	PRIMARY KEY (license_id)) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 