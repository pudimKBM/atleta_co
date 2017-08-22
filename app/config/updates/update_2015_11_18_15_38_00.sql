
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblCalendar', 'backend', 'Label / Calendar', 'script', '2015-11-18 11:56:49');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Calendar', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAllCalendars', 'backend', 'Label / All calendars', 'script', '2015-11-18 12:00:20');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All calendars', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_slot_selected', 'frontend', 'Label / slot selected', 'script', '2015-11-18 12:54:18');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '%s slot selected', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_slots_selected', 'frontend', 'Label / slots selected', 'script', '2015-11-18 12:54:33');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '%s slots selected', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblOptionCopy', 'backend', 'Copy options from', 'script', '2015-11-18 13:24:31');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy options from', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblOptionCopyTip', 'backend', 'Copy options tip', 'script', '2015-11-18 13:25:32');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can copy all the options below from any of your other calendars.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblOptionCopyTitle', 'backend', 'Copy options confirmation', 'script', '2015-11-18 13:26:05');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy options confirmation', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblOptionCopyDesc', 'backend', 'Copy options confirmation', 'script', '2015-11-18 13:27:03');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to copy selected options?', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnCopy', 'backend', 'Button / Copy', 'script', '2015-11-18 13:39:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copy', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_views_ARRAY_1', 'arrays', 'front_views_ARRAY_1', 'script', '2015-11-18 13:55:09');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Calendar view', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_views_ARRAY_2', 'arrays', 'front_views_ARRAY_2', 'script', '2015-11-18 13:55:28');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Weekly view', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAllowSwitchLayout', 'backend', 'Label / Allow switching layout', 'script', '2015-11-18 13:58:09');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow switching layout', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblMultiCalendar', 'backend', 'Label / Multi calendar', 'script', '2015-11-18 13:58:42');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Multi calendar', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_select_calendar', 'frontend', 'Label / Select calendar', 'script', '2015-11-18 14:32:51');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select calendar', 'script');

COMMIT;