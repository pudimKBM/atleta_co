
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_1', 'arrays', 'enum_arr_ARRAY_1', 'script', '2015-08-27 10:59:45');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_2', 'arrays', 'enum_arr_ARRAY_2', 'script', '2015-08-27 11:00:08');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_3', 'arrays', 'enum_arr_ARRAY_3', 'script', '2015-08-27 11:00:26');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes (Required)', 'script');

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_confirmed', 'arrays', 'enum_arr_ARRAY_confirmed', 'script', '2015-08-27 11:01:35');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirmed', 'script');

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_pending', 'arrays', 'enum_arr_ARRAY_pending', 'script', '2015-08-27 11:01:57');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_amount', 'arrays', 'enum_arr_ARRAY_amount', 'script', '2015-08-27 11:04:17');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Amount', 'script');

INSERT INTO `fields` VALUES (NULL, 'enum_arr_ARRAY_percent', 'arrays', 'enum_arr_ARRAY_percent', 'script', '2015-08-27 11:04:51');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Percent', 'script');

COMMIT;