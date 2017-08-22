
START TRANSACTION;

INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, 0, 'pjCalendar', 1, 'title', 'Initial Calendar', 'data'),
(NULL, 0, 'pjCalendar', 1, 'terms_url', 'http://www.google.com/', 'data'),
(NULL, 0, 'pjCalendar', 1, 'terms_body', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque consectetur sollicitudin mi. Cras scelerisque lorem non nunc iaculis lacinia id nec mauris. Nunc suscipit tincidunt velit, et gravida enim blandit nec. Cras pellentesque blandit interdum. Ut porttitor risus felis. Donec vestibulum risus neque, in auctor tortor tincidunt at. Suspendisse varius metus dui, at consectetur turpis lobortis sit amet. Donec eget accumsan sapien. Proin posuere nibh et bibendum consectetur. Integer imperdiet nibh est.', 'data'),
(NULL, 0, 'pjCalendar', 1, 'confirm_subject_client', 'Client confirmation email', 'data'),
(NULL, 0, 'pjCalendar', 1, 'confirm_tokens_client', 'Confirmation email\r\n---------------------------\r\n{Name} - Customer name;\r\n{Phone} - Customer phone number;\r\n{Email} - Customer e-mail address;\r\n{BookingID} - Booking ID;\r\n\r\n{Price} - Price for selected slots;\r\n{Deposit} - Deposit amount;\r\n{Tax} - Tax amount;\r\n{Total} - Total amount;\r\n\r\n{CancelURL} ', 'data'),
(NULL, 0, 'pjCalendar', 1, 'payment_subject_client', 'Client payment email', 'data'),
(NULL, 0, 'pjCalendar', 1, 'payment_tokens_client', 'Payment email\r\n---------------------------\r\n{Name} - Customer name;\r\n{Phone} - Customer phone number;\r\n{Email} - Customer e-mail address;\r\n{BookingID} - Booking ID;\r\n\r\n{Price} - Price for selected slots;\r\n{Deposit} - Deposit amount;\r\n{Tax} - Tax amount;\r\n{Total} - Total amount;', 'data'),
(NULL, 0, 'pjCalendar', 1, 'confirm_subject_admin', 'Admin confirmation email', 'data'),
(NULL, 0, 'pjCalendar', 1, 'confirm_tokens_admin', 'Confirmation email\r\n---------------------------\r\n{Name} - Customer name;\r\n{Phone} - Customer phone number;\r\n{Email} - Customer e-mail address;\r\n{BookingID} - Booking ID;\r\n\r\n{Price} - Price for selected slots;\r\n{Deposit} - Deposit amount;\r\n{Tax} - Tax amount;\r\n{Total} - Total amount;', 'data'),
(NULL, 0, 'pjCalendar', 1, 'payment_subject_admin', 'Admin payment email', 'data'),
(NULL, 0, 'pjCalendar', 1, 'payment_tokens_admin', 'Payment email\r\n---------------------------\r\n{Name} - Customer name;\r\n{Phone} - Customer phone number;\r\n{Email} - Customer e-mail address;\r\n{BookingID} - Booking ID;\r\n\r\n{Price} - Price for selected slots;\r\n{Deposit} - Deposit amount;\r\n{Tax} - Tax amount;\r\n{Total} - Total amount;', 'data'),
(NULL, 0, 'pjCalendar', 1, 'reminder_subject_client', 'Booking Reminder', 'data'),
(NULL, 0, 'pjCalendar', 1, 'reminder_tokens_client', '{Name} - Customer name;\r\n{Phone} - Customer phone number;\r\n{Email} - Customer e-mail address;\r\n{BookingID} - Booking ID;\r\n\r\n{Price} - Price for selected slots;\r\n{Deposit} - Deposit amount;\r\n{Tax} - Tax amount;\r\n{Total} - Total amount;\r\n\r\n{CancelURL} ', 'data'),
(NULL, 0, 'pjCalendar', 1, 'confirm_sms_admin', 'Booking received', 'data'),
(NULL, 0, 'pjCalendar', 1, 'payment_sms_admin', 'Payment received', 'data'),
(NULL, 0, 'pjCalendar', 1, 'reminder_sms_client', '{Name}, your booking is coming.', 'data');

INSERT INTO `working_times` (`id`, `foreign_id`, `monday_from`, `monday_to`, `monday_lunch_from`, `monday_lunch_to`, `monday_price`, `monday_limit`, `monday_length`, `monday_slots`, `monday_dayoff`, `tuesday_from`, `tuesday_to`, `tuesday_lunch_from`, `tuesday_lunch_to`, `tuesday_price`, `tuesday_limit`, `tuesday_length`, `tuesday_slots`, `tuesday_dayoff`, `wednesday_from`, `wednesday_to`, `wednesday_lunch_from`, `wednesday_lunch_to`, `wednesday_price`, `wednesday_limit`, `wednesday_length`, `wednesday_slots`, `wednesday_dayoff`, `thursday_from`, `thursday_to`, `thursday_lunch_from`, `thursday_lunch_to`, `thursday_price`, `thursday_limit`, `thursday_length`, `thursday_slots`, `thursday_dayoff`, `friday_from`, `friday_to`, `friday_lunch_from`, `friday_lunch_to`, `friday_price`, `friday_limit`, `friday_length`, `friday_slots`, `friday_dayoff`, `saturday_from`, `saturday_to`, `saturday_lunch_to`, `saturday_lunch_from`, `saturday_price`, `saturday_limit`, `saturday_length`, `saturday_slots`, `saturday_dayoff`, `sunday_from`, `sunday_to`, `sunday_lunch_from`, `sunday_lunch_to`, `sunday_price`, `sunday_limit`, `sunday_length`, `sunday_slots`, `sunday_dayoff`) VALUES
(NULL, 0, '08:00:00', '18:00:00', '00:00:00', '00:00:00', '10.00', 1, 60, 10, 'F', '08:00:00', '18:00:00', '00:00:00', '00:00:00', '10.00', 1, 60, 10, 'F', '08:00:00', '18:00:00', '00:00:00', '00:00:00', '10.00', 1, 60, 10, 'F', '08:00:00', '18:00:00', '00:00:00', '00:00:00', '10.00', 1, 60, 10, 'F', '08:00:00', '18:00:00', '00:00:00', '00:00:00', '10.00', 1, 60, 10, 'F', '08:00:00', '18:00:00', '00:00:00', '00:00:00', '0.00', 1, 60, 10, 'T', '08:00:00', '18:00:00', '00:00:00', '00:00:00', '0.00', 1, 60, 10, 'T');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(0, 'o_allow_authorize', 7, '1|0::1', NULL, 'bool', 18, 1, NULL),
(0, 'o_allow_bank', 7, '1|0::1', NULL, 'bool', 25, 1, NULL),
(0, 'o_allow_cash', 7, '1|0::1', NULL, 'bool', 24, 1, NULL),
(0, 'o_allow_creditcard', 7, '1|0::1', NULL, 'bool', 23, 1, NULL),
(0, 'o_allow_paypal', 7, '1|0::1', NULL, 'bool', 16, 1, NULL),
(0, 'o_authorize_hash', 7, NULL, NULL, 'string', 21, 1, NULL),
(0, 'o_authorize_key', 7, NULL, NULL, 'string', 20, 1, NULL),
(0, 'o_authorize_mid', 7, NULL, NULL, 'string', 19, 1, NULL),
(0, 'o_authorize_tz', 7, '-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0', 'GMT-12:00|GMT-11:00|GMT-10:00|GMT-09:00|GMT-08:00|GMT-07:00|GMT-06:00|GMT-05:00|GMT-04:00|GMT-03:00|GMT-02:00|GMT-01:00|GMT|GMT+01:00|GMT+02:00|GMT+03:00|GMT+04:00|GMT+05:00|GMT+06:00|GMT+07:00|GMT+08:00|GMT+09:00|GMT+10:00|GMT+11:00|GMT+12:00|GMT+13:00', 'enum', 22, 1, NULL),
(0, 'o_bank_account', 7, 'Bank of America', NULL, 'text', 26, 1, NULL),
(0, 'o_bf_address_1', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 9, 1, NULL),
(0, 'o_bf_address_2', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 10, 1, NULL),
(0, 'o_bf_captcha', 4, '1|3::3', 'No|Yes (Required)', 'enum', 11, 1, NULL),
(0, 'o_bf_city', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 7, 1, NULL),
(0, 'o_bf_country', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 5, 1, NULL),
(0, 'o_bf_email', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 2, 1, NULL),
(0, 'o_bf_name', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 1, 1, NULL),
(0, 'o_bf_notes', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 4, 1, NULL),
(0, 'o_bf_phone', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 3, 1, NULL),
(0, 'o_bf_state', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 6, 1, NULL),
(0, 'o_bf_terms', 4, '1|3::3', 'No|Yes (Required)', 'enum', 12, 1, NULL),
(0, 'o_bf_zip', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 8, 1, NULL),
(0, 'o_deposit', 7, '20', NULL, 'float', 12, 1, NULL),
(0, 'o_deposit_type', 7, 'amount|percent::percent', 'Amount|Percent', 'enum', NULL, 0, NULL),
(0, 'o_disable_payments', 7, '1|0::0', NULL, 'bool', 4, 1, NULL),
(0, 'o_hide_prices', 3, '1|0::0', NULL, 'bool', 2, 1, NULL),
(0, 'o_hours_before', 3, '0', NULL, 'int', 11, 1, NULL),
(0, 'o_layout', 1, '1|2::1', 'Default layout|Weekly layout', 'enum', 1, 1, NULL),
(0, 'o_multi_lang', 99, '1|0::0', NULL, 'enum', NULL, 0, NULL),
(0, 'o_paypal_address', 7, 'paypal_seller@example.com', NULL, 'string', 17, 1, NULL),
(0, 'o_reminder_email_before', 8, '2', NULL, 'int', 2, 1, NULL),
(0, 'o_reminder_enable', 8, '1|0::1', NULL, 'bool', 1, 1, NULL),
(0, 'o_reminder_sms_hours', 8, '1', NULL, 'int', 5, 1, NULL),
(0, 'o_show_legend', 3, '1|0::1', NULL, 'bool', 3, 1, NULL),
(0, 'o_show_week_numbers', 3, '1|0::0', NULL, 'bool', 5, 1, NULL),
(0, 'o_status_if_not_paid', 3, 'confirmed|pending::pending', 'Confirmed|Pending', 'enum', 10, 1, NULL),
(0, 'o_status_if_paid', 3, 'confirmed|pending::confirmed', 'Confirmed|Pending', 'enum', 9, 1, NULL),
(0, 'o_tax', 7, '10', NULL, 'float', 14, 1, NULL),
(0, 'o_thankyou_page', 7, 'http://www.phpjabbers.com/', NULL, 'string', 26, 1, NULL);

COMMIT;