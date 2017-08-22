
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblSetOptionsFor', 'backend', 'Label / Set options for', 'script', '2015-11-23 10:04:33');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set options for', 'script');

COMMIT;