
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblSelectSlotToBook', 'backend', 'Label / Select at lest one slot', 'script', '2015-01-22 10:30:49');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select at least one slot to complete the booking.', 'script');

COMMIT;