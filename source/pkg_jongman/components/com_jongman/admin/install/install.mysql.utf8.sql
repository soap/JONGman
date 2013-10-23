

CREATE TABLE IF NOT EXISTS `#__jongman_reservations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) unsigned NOT NULL COMMENT 'resource id',
  `schedule_id` int(11) unsigned NOT NULL COMMENT 'schedule id',
  `title` varchar(200) DEFAULT NULL COMMENT 'Title or subject of reservation',
  `alias` varchar(200) NOT NULL COMMENT 'alias for user reference',
  `start_date` int(11) NOT NULL DEFAULT '0',
  `end_date` int(11) NOT NULL DEFAULT '0',
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `created_by` int(11) NOT NULL COMMENT 'reserved by (user id)',
  `created_time` datetime NOT NULL COMMENT 'change from created',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `modified_time` datetime DEFAULT NULL,
  `reserved_for` int(11) DEFAULT NULL COMMENT 'reserver for (user id)',
  `parent_id` int(11) DEFAULT '0',
  `is_blackout` smallint(6) NOT NULL DEFAULT '0',
  `state` smallint(6) NOT NULL DEFAULT '0' COMMENT 'changed from is_pending',
  `description` text,
  `allow_participation` smallint(6) NOT NULL DEFAULT '0',
  `allow_anon_participation` smallint(6) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `res_machid` (`resource_id`),
  KEY `res_scheduleid` (`schedule_id`),
  KEY `reservations_startdate` (`start_date`),
  KEY `reservations_enddate` (`end_date`),
  KEY `res_startTime` (`start_time`),
  KEY `res_endTime` (`end_time`),
  KEY `res_created` (`created_time`),
  KEY `res_modified` (`modified_time`),
  KEY `res_parentid` (`parent_id`),
  KEY `res_isblackout` (`is_blackout`),
  KEY `reservations_pending` (`state`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Store reservation data' ;

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__jongman_reservation_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `owner` tinyint(4) NOT NULL DEFAULT '0',
  `can_edit` tinyint(4) NOT NULL DEFAULT '0',
  `can_delete` tinyint(4) NOT NULL DEFAULT '0',
  `accept_code` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_id` (`reservation_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users in each reservation, owner=1 implies reservation owner';

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__jongman_resources` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `location` varchar(250) DEFAULT NULL,
  `rphone` varchar(16) DEFAULT NULL,
  `notes` text,
  `status` char(1) NOT NULL DEFAULT 'a',
  `min_res` int(11) NOT NULL COMMENT 'Minimum reservation length for this resource',
  `max_res` int(11) NOT NULL COMMENT 'Maximum reservation length for this resource',
  `auto_assign` smallint(6) DEFAULT '0',
  `need_approval` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Need admin approval or not',
  `allow_multi` smallint(6) DEFAULT '1' COMMENT 'Allow multiple day reservation (not same as recur. )',
  `max_participants` int(11) DEFAULT '0',
  `min_notice_time` int(11) NOT NULL DEFAULT '0' COMMENT 'hours prior to start time',
  `max_notice_time` int(11) NOT NULL DEFAULT '0' COMMENT 'hours from current time',
  `asset_id` int(11) NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `rs_scheduleid` (`schedule_id`),
  KEY `rs_name` (`title`),
  KEY `rs_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__jongman_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `day_start` int(11) NOT NULL DEFAULT '480' COMMENT 'time start, default = 8:00',
  `day_end` int(11) NOT NULL DEFAULT '1200' COMMENT 'time end for reservation, default=20:00',
  `time_span` smallint(3) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `time_format` tinyint(4) NOT NULL DEFAULT '24' COMMENT 'time format to show in calendar (12/24)',
  `view_days` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'number of days to show (1-7)',
  `weekday_start` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Start day in calendar, 0=sunday, 1=monday..',
  `show_summary` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Show summary text for this schedule',
  `summary_required` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is Summary field required user input',
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_email` varchar(100) NOT NULL,
  `notify_admin` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Notify admin on reservation made or change',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__jongman_quotas` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
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
