
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_goto_cart', 'frontend', 'Label / Go to cart', 'script', '2015-11-06 02:32:09');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Go to cart', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_availability', 'frontend', 'Label / Availability', 'script', '2015-11-06 03:22:32');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Availability', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_available', 'frontend', 'Label / available', 'script', '2015-11-06 03:26:37');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'available', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_booked', 'frontend', 'Label / booked', 'script', '2015-11-06 03:27:05');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'booked', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_selected', 'frontend', 'Label / selected', 'script', '2015-11-06 03:27:27');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'selected', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_close', 'frontend', 'Button / Close', 'script', '2015-11-06 16:40:50');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Close', 'script');

COMMIT;