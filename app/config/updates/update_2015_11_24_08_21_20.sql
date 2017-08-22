
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblPreviewOptions', 'backend', 'Label / Preview options', 'script', '2015-11-24 08:20:31');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Preview options', 'script');

COMMIT;