
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'confirmation_client_cancellation', 'backend', 'Client - Cancellation email', 'script', '2016-11-28 08:33:10');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client - Cancellation email', 'script');

INSERT INTO `fields` VALUES (NULL, 'confirmation_admin_cancellation', 'backend', 'Admin - Cancellation email', 'script', '2016-11-28 08:38:46');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin - Cancellation email', 'script');

INSERT INTO `fields` VALUES (NULL, 'receive_emails_ARRAY_cancel', 'arrays', 'receive_emails_ARRAY_cancel', 'script', '2016-11-28 08:53:03');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking cancellation email', 'script');

COMMIT;