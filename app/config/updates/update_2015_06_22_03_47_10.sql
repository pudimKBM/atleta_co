
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblSlotLimitation', 'backend', 'Label / Slots limitation', 'script', '2015-06-22 03:29:49');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can not make slots after {AFTER}. Please select "Start time" or "Slot length" or "Number of slots" again.', 'script');

COMMIT;