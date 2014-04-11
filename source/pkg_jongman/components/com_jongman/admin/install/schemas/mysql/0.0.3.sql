ALTER TABLE `#__jongman_reservations` ADD COLUMN `owner_id` INT(11) NOT NULL AFTER `schedule_id`;
ALTER TABLE `#__jongman_reservation_users` CHANGE COLUMN `reservation_id` `schedule_id` INT(11);
