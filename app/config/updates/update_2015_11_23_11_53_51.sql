
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblPreviewCalendar', 'backend', 'Label / Preview calendar', 'script', '2015-11-23 06:41:32');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview calendar', 'script');

COMMIT;