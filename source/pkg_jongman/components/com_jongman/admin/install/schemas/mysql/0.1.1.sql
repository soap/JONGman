ALTER TABLE `#__jongman_layouts` ADD COLUMN `ordering` int(11) NOT NULL DEFAULT '0';

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


CREATE TABLE IF NOT EXISTS `#__jongman_reservation_fields` (
  `reservation_id` int(11) unsigned NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `field_value` text,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_key`,`reservation_id`),
  KEY `fk_jongman_reservation_idx` (`reservation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;