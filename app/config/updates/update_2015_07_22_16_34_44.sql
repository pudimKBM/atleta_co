
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'custom_filter_ARRAY_A', 'arrays', 'custom_filter_ARRAY_A', 'script', '2015-07-22 16:33:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `fields` VALUES (NULL, 'custom_filter_ARRAY_T', 'arrays', 'custom_filter_ARRAY_T', 'script', '2015-07-22 16:34:02');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Working days', 'script');

INSERT INTO `fields` VALUES (NULL, 'custom_filter_ARRAY_F', 'arrays', 'custom_filter_ARRAY_F', 'script', '2015-07-22 16:34:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Days off', 'script');

COMMIT;