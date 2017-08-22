
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'opt_o_allow_cash', 'backend', 'Options / Allow cash payments', 'script', '2014-12-12 07:32:18');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow cash payments', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_ARRAY_cash', 'arrays', 'payment_methods_ARRAY_cash', 'script', '2014-12-12 07:32:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

ALTER TABLE `bookings` CHANGE `payment_method` `payment_method` ENUM('paypal','authorize','creditcard','bank','cash') DEFAULT NULL;

COMMIT;