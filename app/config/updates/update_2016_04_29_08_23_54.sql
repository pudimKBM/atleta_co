
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'booking_slots_booked', 'backend', 'Label / Slot(s) of the calendar is booked.', 'script', '2016-04-29 08:23:40');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Slot(s) of the calendar is booked.', 'script');

COMMIT;