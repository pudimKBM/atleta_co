
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AR21', 'arrays', 'error_titles_ARRAY_AR21', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Export bookings', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AR21', 'arrays', 'error_bodies_ARRAY_AR21', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can export bookings in different formats. You can either download a file with bookings details or use a link for a feed which load all the bookings.', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_formats_ARRAY_ical', 'arrays', 'export_formats_ARRAY_ical', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'iCal', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_formats_ARRAY_xml', 'arrays', 'export_formats_ARRAY_xml', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'XML', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_formats_ARRAY_csv', 'arrays', 'export_formats_ARRAY_csv', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CSV', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_types_ARRAY_file', 'arrays', 'export_types_ARRAY_file', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'File', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_types_ARRAY_feed', 'arrays', 'export_types_ARRAY_feed', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Feed', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_periods_ARRAY_next', 'arrays', 'export_periods_ARRAY_next', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Coming', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_periods_ARRAY_last', 'arrays', 'export_periods_ARRAY_last', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created or Modified', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFormat', 'backend', 'Label / Format', 'script', '2014-11-27 06:54:47');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Format', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEnterPassword', 'backend', 'Label / Enter password', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enter password', 'script');

INSERT INTO `fields` VALUES (NULL, 'coming_arr_ARRAY_1', 'arrays', 'coming_arr_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Today', 'script');

INSERT INTO `fields` VALUES (NULL, 'coming_arr_ARRAY_2', 'arrays', 'coming_arr_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tomorrow', 'script');

INSERT INTO `fields` VALUES (NULL, 'coming_arr_ARRAY_3', 'arrays', 'coming_arr_ARRAY_3', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This week', 'script');

INSERT INTO `fields` VALUES (NULL, 'coming_arr_ARRAY_4', 'arrays', 'coming_arr_ARRAY_4', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next week', 'script');

INSERT INTO `fields` VALUES (NULL, 'coming_arr_ARRAY_5', 'arrays', 'coming_arr_ARRAY_5', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This month', 'script');

INSERT INTO `fields` VALUES (NULL, 'coming_arr_ARRAY_6', 'arrays', 'coming_arr_ARRAY_6', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Next month', 'script');

INSERT INTO `fields` VALUES (NULL, 'made_arr_ARRAY_1', 'arrays', 'made_arr_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Today', 'script');

INSERT INTO `fields` VALUES (NULL, 'made_arr_ARRAY_2', 'arrays', 'made_arr_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yesterday', 'script');

INSERT INTO `fields` VALUES (NULL, 'made_arr_ARRAY_3', 'arrays', 'made_arr_ARRAY_3', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This week', 'script');

INSERT INTO `fields` VALUES (NULL, 'made_arr_ARRAY_4', 'arrays', 'made_arr_ARRAY_4', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last week', 'script');

INSERT INTO `fields` VALUES (NULL, 'made_arr_ARRAY_5', 'arrays', 'made_arr_ARRAY_5', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This month', 'script');

INSERT INTO `fields` VALUES (NULL, 'made_arr_ARRAY_6', 'arrays', 'made_arr_ARRAY_6', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last month', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnExport', 'backend', 'Button / Export', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Export', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnGetFeedURL', 'backend', 'Button / Get Feed URL', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Get Feed URL', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNoAccessToFeed', 'backend', 'Label / No access to feed', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No access to feed', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoBookingsFeedTitle', 'backend', 'Infobox / Bookings Feed URL', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings Feed URL', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoBookingsFeedDesc', 'backend', 'Infobox / Bookings Feed URL', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the URL below to have access to all bookings. Please, note that if you change the password the URL will change too as password is used in the URL itself so no one else can open it.', 'script');

DROP TABLE IF EXISTS `password`;
CREATE TABLE IF NOT EXISTS `password` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD `modified` DATETIME DEFAULT NULL AFTER `ip`;

COMMIT;