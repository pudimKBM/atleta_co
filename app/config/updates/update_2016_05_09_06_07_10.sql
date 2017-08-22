
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'cal_del_title', 'backend', 'Label / Delete booking', 'script', '2016-05-09 06:05:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete booking', 'script');

INSERT INTO `fields` VALUES (NULL, 'cal_del_body', 'backend', 'Label / Delete booking', 'script', '2016-05-09 06:06:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Do you want to delete the selected booking?', 'script');

INSERT INTO `fields` VALUES (NULL, 'cal_del_ts_title', 'backend', 'Label / Delete slot', 'script', '2016-05-09 06:05:58');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete slot', 'script');

INSERT INTO `fields` VALUES (NULL, 'cal_del_ts_body', 'backend', 'Label / Delete slot', 'script', '2016-05-09 06:06:21');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Do you want to delete the selected slot?', 'script');

COMMIT;