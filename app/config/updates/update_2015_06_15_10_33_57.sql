
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblDayOfWeek', 'backend', 'Label / Day of the week', 'script', '2015-06-15 05:24:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Day of the week', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSetAsDayOff', 'backend', 'Label / Set as day off', 'script', '2015-06-15 05:28:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set as day off', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStartTime', 'backend', 'Label / Start time', 'script', '2015-06-15 05:33:33');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start time', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNumberOfSlots', 'backend', 'Label / Number of slots', 'script', '2015-06-15 05:58:09');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Number of slots', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFrom', 'backend', 'Label / From', 'script', '2015-06-15 05:59:55');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'from', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLength', 'backend', 'Label / length', 'script', '2015-06-15 06:25:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'length', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblMinutes', 'backend', 'Label / minutes', 'script', '2015-06-15 06:27:13');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'minutes', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNone', 'backend', 'Label / none', 'script', '2015-06-15 06:37:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'none', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEndTime', 'backend', 'Label / End time', 'script', '2015-06-15 07:19:40');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'End time', 'script');

COMMIT;