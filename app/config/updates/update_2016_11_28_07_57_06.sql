
START TRANSACTION;

INSERT IGNORE INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT `id`, 'pjCalendar', '::LOCALE::', 'cancel_subject_client', 'Client cancellation email', 'data'
FROM `calendars` WHERE `id` IS NOT NULL;

INSERT IGNORE INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT `id`, 'pjCalendar', '::LOCALE::', 'cancel_tokens_client', 'Cancellation email\n---------------------------\n{Name} - Customer name;\n{Phone} - Customer phone number;\n{Email} - Customer e-mail address;\n{BookingID} - Booking ID;\n\n{Price} - Price for selected slots;\n{Deposit} - Deposit amount;\n{Tax} - Tax amount;\n{Total} - Total amount;', 'data'
FROM `calendars` WHERE `id` IS NOT NULL;

INSERT IGNORE INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT `id`, 'pjCalendar', '::LOCALE::', 'cancel_subject_admin', 'Admin cancellation email', 'data'
FROM `calendars` WHERE `id` IS NOT NULL;

INSERT IGNORE INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT `id`, 'pjCalendar', '::LOCALE::', 'cancel_tokens_admin', 'Cancellation email\n---------------------------\n{Name} - Customer name;\n{Phone} - Customer phone number;\n{Email} - Customer e-mail address;\n{BookingID} - Booking ID;\n\n{Price} - Price for selected slots;\n{Deposit} - Deposit amount;\n{Tax} - Tax amount;\n{Total} - Total amount;', 'data'
FROM `calendars` WHERE `id` IS NOT NULL;

COMMIT;