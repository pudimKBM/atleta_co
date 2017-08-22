
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'btnAddCalendar', 'backend', 'Button / + Add calendar', 'script', '2015-11-01 08:51:57');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add calendar', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCalendarViewTitle', 'backend', 'Infobox / View calendar', 'script', '2015-11-01 08:59:40');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View calendar', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCalendarViewDesc', 'backend', 'Infobox / View calendar', 'script', '2015-11-01 09:00:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'On below you can view the details of the current calendar.', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddUser', 'backend', 'Button / + Add user', 'script', '2015-11-01 09:03:35');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add user', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUsersTitle', 'backend', 'Infobox / Users list', 'script', '2015-11-01 09:05:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Users list', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUsersDesc', 'backend', 'Infobox / Users list', 'script', '2015-11-01 09:05:44');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of users. If you want to add new user, click on the button "+ Add user".', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddBooking', 'backend', 'Button / + Add booking', 'script', '2015-11-01 10:11:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add booking', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoBookingsTitle', 'backend', 'Infobox / Bookings list', 'script', '2015-11-01 10:12:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings list', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoBookingsDesc', 'backend', 'Infobox / Bookings list', 'script', '2015-11-01 10:12:57');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list bookings. To add new booking, you can click on the button "+ Add booking".', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSetWtimeAndPrice', 'backend', 'Lable / Set Working Time and Price for', 'script', '2015-11-01 10:38:50');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set Working Time and Price for', 'script');

COMMIT;