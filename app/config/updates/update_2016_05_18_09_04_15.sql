
START TRANSACTION;

DROP TABLE IF EXISTS `calendars_users`;
CREATE TABLE IF NOT EXISTS `calendars_users` (
  `calendar_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`calendar_id`, `user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

COMMIT;