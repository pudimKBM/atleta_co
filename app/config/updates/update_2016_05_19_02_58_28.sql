
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_custom_wtime', 'backend', 'Label / Custom working time is set', 'script', '2016-05-19 02:18:27');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom working time is set for this day.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_view_slots', 'backend', 'Label / View slots', 'script', '2016-05-19 02:19:17');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View slots', 'script');

COMMIT;