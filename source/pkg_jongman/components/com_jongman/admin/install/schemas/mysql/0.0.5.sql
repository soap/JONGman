ALTER TABLE `#__jongman_resources` ADD COLUMN `auto_assign` tinyint(4) NOT NULL DEFAULT '1',
ADD COLUMN `allow_multi_days` tinyint(4) NOT NULL DEFAULT '0',
ADD COLUMN `max_participants` smallint(6) NOT NULL DEFAULT '0',
ADD COLUMN `min_reservation_duration` int(11) NOT NULL DEFAULT '0',
ADD COLUMN `max_reservation_duration` int(11) NOT NULL DEFAULT '0',
ADD COLUMN `min_notice_duration` int(11) NOT NULL DEFAULT '0',
ADD COLUMN `max_notice_duration` int(11) NOT NULL DEFAULT '0',
ADD COLUMN `requires_approval` tinyint(4) NOT NULL DEFAULT '0' AFTER `contact_info`;
