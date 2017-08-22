
START TRANSACTION;

ALTER TABLE `dates` ADD COLUMN `slots` smallint(5) unsigned DEFAULT NULL AFTER `end_time`;

COMMIT;