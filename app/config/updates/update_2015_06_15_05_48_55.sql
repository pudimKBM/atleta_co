
START TRANSACTION;

ALTER TABLE `working_times` ADD COLUMN `monday_slots` smallint(5) unsigned DEFAULT '9' AFTER `monday_length`;

ALTER TABLE `working_times` ADD COLUMN `tuesday_slots` smallint(5) unsigned DEFAULT '9' AFTER `tuesday_length`;

ALTER TABLE `working_times` ADD COLUMN `wednesday_slots` smallint(5) unsigned DEFAULT '9' AFTER `wednesday_length`;

ALTER TABLE `working_times` ADD COLUMN `thursday_slots` smallint(5) unsigned DEFAULT '9' AFTER `thursday_length`;

ALTER TABLE `working_times` ADD COLUMN `friday_slots` smallint(5) unsigned DEFAULT '9' AFTER `friday_length`;

ALTER TABLE `working_times` ADD COLUMN `saturday_slots` smallint(5) unsigned DEFAULT '9' AFTER `saturday_length`;

ALTER TABLE `working_times` ADD COLUMN `sunday_slots` smallint(5) unsigned DEFAULT '9' AFTER `sunday_length`;

COMMIT;