
START TRANSACTION;

ALTER TABLE `users` ADD COLUMN `notify_sms` VARCHAR(255) DEFAULT NULL AFTER `notify_email`;

INSERT INTO `fields` VALUES (NULL, 'lblReceiveSms', 'backend', 'Label / Receive SMS', 'script', '2016-05-18 07:06:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Receive SMS', 'script');

INSERT INTO `fields` VALUES (NULL, 'receive_sms_ARRAY_confirm', 'arrays', 'receive_sms_ARRAY_confirm', 'script', '2016-05-18 07:07:22');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking confirmation SMS', 'script');

INSERT INTO `fields` VALUES (NULL, 'receive_sms_ARRAY_payment', 'arrays', 'receive_sms_ARRAY_payment', 'script', '2016-05-18 07:08:06');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment confirmation SMS', 'script');

COMMIT;