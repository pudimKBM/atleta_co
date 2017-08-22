
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'send_sms_title', 'backend', 'Label / Sms title', 'script', '2014-12-29 06:24:54');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The following SMS message will be sent to %s.', 'script');

INSERT INTO `fields` VALUES (NULL, 'send_email_title', 'backend', 'Label / Sms title', 'script', '2014-12-29 06:43:40');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The following email message will be sent to %s.', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_subject', 'backend', 'Label / Subject', 'script', '2014-12-29 06:49:07');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'booking_message', 'backend', 'Label / Message', 'script', '2014-12-29 06:49:26');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnEmail', 'backend', 'Button / Email', 'script', '2014-12-29 07:08:09');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnSMS', 'backend', 'Button / SMS', 'script', '2014-12-29 07:08:35');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS', 'script');

INSERT INTO `fields` VALUES (NULL, 'btniCal', 'backend', 'Button / iCal', 'script', '2014-12-29 07:09:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'iCal', 'script');

COMMIT;