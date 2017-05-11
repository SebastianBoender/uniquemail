<?php
//Include database
require_once("../controllers/database.php");

$st = $db->prepare("INSERT INTO email_accounts (email, naam, user_id, password, mail_server, afzender) VALUES ('test@youmad.nl', 'test account', 1, 'Test1234', 'mail.mijndomein.nl:993/ssl', 'Test');
					INSERT INTO inbox (subject, message, message_html, sender, sender_email, cc, bcc, date, size, user_id, email_id, mid, deleted, timestamp, unique_hash, attachment, unread, flag, type) VALUES ('Test email', 'Test message', 'Test message', 'sebas', 'sebas@youmad.nl', 'test@cc.nl', 0, 27381237, 382, 1, 1, 0, 0, 213932189, 0, 0, 1, 0, 1);
					INSERT INTO inbox (subject, message, message_html, sender, sender_email, cc, bcc, date, size, user_id, email_id, mid, deleted, timestamp, unique_hash, attachment, unread, flag, type) VALUES ('Test email', 'Test message', 'Test message', 'sebas', 'sebas@youmad.nl', 'test@cc.nl', 0, 27381237, 382, 1, 1, 0, 0, 213932189, 0, 0, 1, 0, 2);
					INSERT INTO inbox (subject, message, message_html, sender, sender_email, cc, bcc, date, size, user_id, email_id, mid, deleted, timestamp, unique_hash, attachment, unread, flag, type) VALUES ('Test email', 'Test message', 'Test message', 'sebas', 'sebas@youmad.nl', 'test@cc.nl', 0, 27381237, 382, 1, 1, 0, 1, 213932189, 0, 0, 1, 0, 3);
					INSERT INTO inbox (subject, message, message_html, sender, sender_email, cc, bcc, date, size, user_id, email_id, mid, deleted, timestamp, unique_hash, attachment, unread, flag, type) VALUES ('Test email', 'Test message', 'Test message', 'sebas', 'sebas@youmad.nl', 'test@cc.nl', 0, 27381237, 382, 1, 1, 0, 0, 213932189, 0, 0, 1, 0, 3);
					INSERT INTO outbox (subject, message, receiver, date, size, user_id, email_id, priority, concept, flag) VALUES ('Test email', 'Test message', 'Test@test.com', 321832, 312, 1, 1, 3, 0, 1);
					INSERT INTO outbox (subject, message, receiver, date, size, user_id, email_id, priority, concept, flag) VALUES ('Test email', 'Test message', 'Test@test.com', 321832, 312, 1, 1, 3, 1, 1");
$st->execute();

?>