TYPE=TRIGGERS
triggers='CREATE DEFINER=`zambiademo`@`%` TRIGGER POS_insert_trig AFTER INSERT ON ParticipantOnSession FOR EACH ROW\n    IF (\n        SELECT count(*) FROM ParticipantOnSession\n            WHERE sessionid = NEW.sessionid AND moderator = 1)\n        > 1 THEN\n        SIGNAL sqlstate \'45000\' SET MESSAGE_TEXT = \'Attempted to insert more than one record with moderator = 1 for a single session into ParticipantOnSession.\';\n    END IF'
sql_modes=524288
definers='zambiademo@%'
client_cs_names='utf8'
connection_cl_names='utf8_general_ci'
db_cl_names='latin1_swedish_ci'
