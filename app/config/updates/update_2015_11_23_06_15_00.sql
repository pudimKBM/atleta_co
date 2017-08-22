
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_duplicated_slots', 'frontend', 'Label / Duplicated slots', 'script', '2015-11-23 06:14:53');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Slot(s) you selected is/are booked by another while you are placing the booking.', 'script');

COMMIT;