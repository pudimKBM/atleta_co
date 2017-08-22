
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_button_back', 'frontend', 'Label / Back to calendar', 'script', '2014-10-21 06:27:27');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back to calendar', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_bf_terms");
UPDATE `multi_lang` SET `content` = 'I agree with {STAG}terms and conditions{ETAG}' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;