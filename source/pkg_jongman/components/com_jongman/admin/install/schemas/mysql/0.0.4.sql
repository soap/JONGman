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
