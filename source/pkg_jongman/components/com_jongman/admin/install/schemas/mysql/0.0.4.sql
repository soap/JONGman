ALTER TABLE `#__jongman_resources` ADD COLUMN `auto_assign` tinyint(4) NOT NULL DEFAULT '1',
  `allow_multi_days` tinyint(4) NOT NULL DEFAULT '0',
  `max_participants` smallint(6) NOT NULL DEFAULT '0',
  `min_reservation_duration` int(11) NOT NULL DEFAULT '0',
  `max_reservation_duration` int(11) NOT NULL DEFAULT '0',
  `min_notice_duration` int(11) NOT NULL DEFAULT '0',
  `max_notice_duration` int(11) NOT NULL DEFAULT '0',
  `requires_approval` tinyint(4) NOT NULL DEFAULT '0' AFTER `contact_info`;
