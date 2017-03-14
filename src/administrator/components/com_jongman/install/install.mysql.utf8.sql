--
--
-- Table structure for table `#__jongman_blackouts`
--

DROP TABLE IF EXISTS `#__jongman_blackouts`;
CREATE TABLE IF NOT EXISTS `#__jongman_blackouts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `alias` varchar(50) NOT NULL,
  `repeat_type` varchar(10) NOT NULL,
  `repeat_options` text NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `created_by` int(11) unsigned NOT NULL COMMENT 'reserved by (user id)',
  `created` datetime NOT NULL COMMENT 'change from created',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `state` smallint(6) NOT NULL DEFAULT '1' COMMENT 'changed from is_pending;1=approved, -1=pending',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL DEFAULT '0',
  `note` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `res_created` (`created`),
  KEY `res_modified` (`modified`),
  KEY `reservations_pending` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Store reservation series' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_blackout_instances`
--

DROP TABLE IF EXISTS `#__jongman_blackout_instances`;
CREATE TABLE IF NOT EXISTS `#__jongman_blackout_instances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blackout_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='one reservation has more than one instance if its repeat option is not none.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_blackout_resources`
--

DROP TABLE IF EXISTS `#__jongman_blackout_resources`;
CREATE TABLE IF NOT EXISTS `#__jongman_blackout_resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blackout_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_id` (`blackout_id`,`resource_id`),
  UNIQUE KEY `blackout_id` (`blackout_id`,`resource_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='multiple resources per reservation' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_customers`
--

DROP TABLE IF EXISTS `#__jongman_customers`;
CREATE TABLE IF NOT EXISTS `#__jongman_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `address` text,
  `suburb` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `misc` mediumtext,
  `image` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `webpage` varchar(255) NOT NULL DEFAULT '',
  `sortname1` varchar(255) NOT NULL,
  `sortname2` varchar(255) NOT NULL,
  `sortname3` varchar(255) NOT NULL,
  `language` char(7) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`published`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__jongman_layouts`
--

DROP TABLE IF EXISTS `#__jongman_layouts`;
CREATE TABLE IF NOT EXISTS `#__jongman_layouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `alias` varchar(200) NOT NULL,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `timezone` varchar(100) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `note` varchar(200) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '1',
  `language` varchar(10) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_quotas`
--

DROP TABLE IF EXISTS `#__jongman_quotas`;
CREATE TABLE IF NOT EXISTS `#__jongman_quotas` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `alias` varchar(200) NOT NULL,
  `quota_limit` decimal(7,2) unsigned NOT NULL,
  `unit` varchar(25) NOT NULL,
  `duration` varchar(25) NOT NULL,
  `schedule_id` smallint(5) unsigned DEFAULT NULL,
  `resource_id` smallint(5) unsigned DEFAULT NULL,
  `group_id` smallint(5) unsigned DEFAULT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(11) NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `access` int(11) NOT NULL,
  `note` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`),
  KEY `group_id` (`group_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_reservations`
--

DROP TABLE IF EXISTS `#__jongman_reservations`;
CREATE TABLE IF NOT EXISTS `#__jongman_reservations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `customer_id` int(11) NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `alias` varchar(50) NOT NULL,
  `repeat_type` varchar(10) NOT NULL,
  `repeat_options` text NOT NULL,
  `attribs` text NULL,
  `created_by` int(11) unsigned NOT NULL COMMENT 'reserved by (user id)',
  `created` datetime NOT NULL COMMENT 'change from created',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `type_id` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=reservation,2=black out',
  `state` smallint(6) NOT NULL DEFAULT '1' COMMENT 'changed from is_pending;1=approved, -1=pending',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL DEFAULT '0',
  `note` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `res_created` (`created`),
  KEY `res_modified` (`modified`),
  KEY `reservations_pending` (`state`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Store reservation series' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_reservation_instances`
--

DROP TABLE IF EXISTS `#__jongman_reservation_instances`;
CREATE TABLE IF NOT EXISTS `#__jongman_reservation_instances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reference_number` varchar(20) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='one reservation has more than one instance if its repeat option is not none.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_reservation_resources`
--

DROP TABLE IF EXISTS `#__jongman_reservation_resources`;
CREATE TABLE IF NOT EXISTS `#__jongman_reservation_resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `resource_level` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_id` (`reservation_id`,`resource_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='multiple resources per reservation' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_reservation_users`
--

DROP TABLE IF EXISTS `#__jongman_reservation_users`;
CREATE TABLE IF NOT EXISTS `#__jongman_reservation_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_instance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_level` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_instance_id` (`reservation_instance_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Users in each reservation, owner=1 implies reservation owner' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__jongman_reservation_fields` (
  `reservation_id` int(11) unsigned NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `field_value` text,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_key`,`reservation_id`),
  KEY `fk_jongman_reservation_idx` (`reservation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__jongman_resources`
--

DROP TABLE IF EXISTS `#__jongman_resources`;
CREATE TABLE IF NOT EXISTS `#__jongman_resources` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `location` varchar(250) DEFAULT NULL,
  `contact_info` varchar(200) NOT NULL,
  `requires_approval` tinyint(4) NOT NULL DEFAULT '0',
  `note` varchar(200) NOT NULL,
  `rphone` varchar(16) DEFAULT NULL COMMENT 'deprecated',
  `notes` text COMMENT 'deprecated',
  `params` mediumtext NOT NULL,
  `asset_id` int(11) NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `auto_assign` tinyint(4) NOT NULL DEFAULT '1',
  `allow_multi_days` tinyint(4) NOT NULL DEFAULT '0',
  `max_participants` smallint(6) NOT NULL DEFAULT '0',
  `min_reservation_duration` int(11) NOT NULL DEFAULT '0',
  `max_reservation_duration` int(11) NOT NULL DEFAULT '0',
  `min_notice_duration` int(11) NOT NULL DEFAULT '0',
  `max_notice_duration` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rs_scheduleid` (`schedule_id`),
  KEY `rs_name` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jongman_schedules`
--

DROP TABLE IF EXISTS `#__jongman_schedules`;
CREATE TABLE IF NOT EXISTS `#__jongman_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `default` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',  
  `day_start` int(11) NOT NULL DEFAULT '480' COMMENT 'time start, default = 8:00',
  `day_end` int(11) NOT NULL DEFAULT '1200' COMMENT 'time end for reservation, default=20:00',
  `time_span` smallint(3) DEFAULT NULL,
  `time_format` tinyint(4) NOT NULL DEFAULT '24' COMMENT 'time format to show in calendar (12/24)',
  `layout_id` int(9) NOT NULL,
  `view_days` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'number of days to show (1-7)',
  `weekday_start` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Start day in calendar, 0=sunday, 1=monday..',
  `show_summary` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Show summary text for this schedule',
  `summary_required` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is Summary field required user input',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL,
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_email` varchar(100) NOT NULL,
  `notify_admin` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Notify admin on reservation made or change',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `#__jongman_time_blocks`;
CREATE TABLE IF NOT EXISTS `#__jongman_time_blocks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(85) DEFAULT NULL,
  `end_label` varchar(85) DEFAULT NULL,
  `availability_code` tinyint(2) unsigned NOT NULL,
  `layout_id` mediumint(8) unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `day_of_week` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `layout_id` (`layout_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Init initial data for table `#__jongman_layouts`
--

INSERT INTO `#__jongman_layouts` (`id`, `title`, `alias`, `default`, `timezone`, `published`, `note`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `access`, `language`) VALUES
(1, 'default', 'default', 1, 'Asia/Bangkok', 1, '', '2014-04-12 07:51:24', 223, '2014-09-18 16:53:15', 614, 0, '0000-00-00 00:00:00', 1, '*');
--
-- Init initial data for table `xkboa_jongman_time_blocks`
--

INSERT INTO `#__jongman_time_blocks` (`id`, `label`, `end_label`, `availability_code`, `layout_id`, `start_time`, `end_time`, `day_of_week`) VALUES
(1, NULL, NULL, 2, 1, '00:00:00', '08:00:00', NULL),
(2, NULL, NULL, 1, 1, '08:00:00', '08:30:00', NULL),
(3, NULL, NULL, 1, 1, '08:30:00', '09:00:00', NULL),
(4, NULL, NULL, 1, 1, '09:00:00', '09:30:00', NULL),
(5, NULL, NULL, 1, 1, '09:30:00', '10:00:00', NULL),
(6, NULL, NULL, 1, 1, '10:00:00', '10:30:00', NULL),
(7, NULL, NULL, 1, 1, '10:30:00', '11:00:00', NULL),
(8, NULL, NULL, 1, 1, '11:00:00', '11:30:00', NULL),
(9, NULL, NULL, 1, 1, '11:30:00', '12:00:00', NULL),
(10, NULL, NULL, 1, 1, '12:00:00', '12:30:00', NULL),
(11, NULL, NULL, 1, 1, '12:30:00', '13:00:00', NULL),
(12, NULL, NULL, 1, 1, '13:00:00', '13:30:00', NULL),
(13, NULL, NULL, 1, 1, '13:30:00', '14:00:00', NULL),
(14, NULL, NULL, 1, 1, '14:00:00', '14:30:00', NULL),
(15, NULL, NULL, 1, 1, '14:30:00', '15:00:00', NULL),
(16, NULL, NULL, 1, 1, '15:00:00', '15:30:00', NULL),
(17, NULL, NULL, 1, 1, '15:30:00', '16:00:00', NULL),
(18, NULL, NULL, 1, 1, '16:00:00', '16:30:00', NULL),
(19, NULL, NULL, 1, 1, '16:30:00', '17:00:00', NULL),
(20, NULL, NULL, 2, 1, '17:00:00', '00:00:00', NULL);


