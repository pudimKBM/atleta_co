
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblInstallJs1_body");
UPDATE `multi_lang` SET `content` = 'Set different options for the front end calendar, then copy the code below and put it on the web page where you want the calendar to appear. ' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;