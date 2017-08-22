
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblDuplidatedSlot', 'backend', 'Label / Duplicated slots', 'script', '2015-12-15 09:15:23');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Slot(s) is/are already added for this reservation.', 'script');

COMMIT;