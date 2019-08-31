## This script modifies one table and creates another for code changes to track changes to participants on sessions.
##
##	$Header$
##	Created by Peter Olszowka on 2016-05-01;
## 	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.
##
INSERT INTO Participants (badgeid, password, pubsname) VALUES ('unrecorded', '12345678901234567890123456789012', 'unrecorded')
    ON DUPLICATE KEY UPDATE badgeid = 'unrecorded', password = '12345678901234567890123456789012', pubsname = 'unrecorded';

ALTER TABLE ParticipantOnSession
    CHANGE COLUMN ts createdts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DROP PRIMARY KEY,
    DROP FOREIGN KEY ParticipantOnSession_ibfk_1,
    DROP FOREIGN KEY ParticipantOnSession_ibfk_2;

ALTER TABLE ParticipantOnSession
    ADD COLUMN participantonsessionid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE ParticipantOnSession
	ADD UNIQUE KEY uniqueness (badgeid, sessionid),
	ADD CONSTRAINT ParticipantOnSession_ibfk_1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid),
    ADD CONSTRAINT ParticipantOnSession_ibfk_2 FOREIGN KEY (sessionid) REFERENCES Sessions (sessionid);

CREATE TABLE ParticipantOnSessionHistory (
    participantonsessionhistoryid int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    badgeid varchar(15) NOT NULL DEFAULT '',
    sessionid int(11) NOT NULL DEFAULT '0',
    moderator tinyint(4) NOT NULL DEFAULT '0',
    participantonsessionid int(11) NULL DEFAULT NULL,
    createdts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    createdbybadgeid varchar(15) NOT NULL DEFAULT '',
    inactivatedts timestamp NULL DEFAULT NULL,
    inactivatedbybadgeid varchar(15) NULL,
    PRIMARY KEY (`participantonsessionhistoryid`),
    KEY `badgeid` (`badgeid`),
    KEY `sessionid` (`sessionid`),
    KEY `ParticipantOnSessionHistory_ibfk_3` (`createdbybadgeid`),
    KEY `ParticipantOnSessionHistory_ibfk_4` (`inactivatedbybadgeid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_2` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_3` FOREIGN KEY (`createdbybadgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_4` FOREIGN KEY (`inactivatedbybadgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
INSERT INTO ParticipantOnSessionHistory (badgeid, sessionid, moderator, participantonsessionid, createdts, createdbybadgeid)
    SELECT
            badgeid, sessionid, moderator, participantonsessionid, createdts, 'unrecorded'
        FROM
            ParticipantOnSession;
            
ALTER TABLE ParticipantOnSession
    DROP COLUMN createdts;

DELIMITER //
CREATE TRIGGER POSH_delete_trig BEFORE DELETE ON ParticipantOnSessionHistory FOR EACH ROW
    SIGNAL sqlstate '45000' SET MESSAGE_TEXT = 'May only insert and update table ParticipantOnSessionHistory.';
//
CREATE TRIGGER POSH_insert_trig BEFORE INSERT ON ParticipantOnSessionHistory FOR EACH ROW
    BEGIN
        IF NEW.inactivatedts IS NOT NULL OR NEW.inactivatedbybadgeid IS NOT NULL THEN
            SIGNAL sqlstate '45000' SET MESSAGE_TEXT = 'Insert table ParticipantOnSessionHistory with non null inactivated... field.';
        END IF;
        INSERT INTO ParticipantOnSession (badgeid, sessionid, moderator)
            VALUES (NEW.badgeid, NEW.sessionid, NEW.moderator);
    END;
//
CREATE TRIGGER POSH_update_trig BEFORE UPDATE ON ParticipantOnSessionHistory FOR EACH ROW
    BEGIN
        IF NEW.inactivatedts IS NULL OR NEW.inactivatedbybadgeid IS NULL THEN
            SIGNAL sqlstate '45000' SET message_text = 'Update table ParticipantOnSessionHistory with null inactivated... field.';
        END IF;
        IF OLD.inactivatedts IS NOT NULL OR OLD.inactivatedbybadgeid IS NOT NULL THEN
            SIGNAL sqlstate '45000' SET message_text = 'Update table ParticipantOnSessionHistory when record previously inactivated.';
        END IF;
        IF NOT(NEW.participantonsessionhistoryid <=> OLD.participantonsessionhistoryid) OR
            NOT(NEW.badgeid <=> OLD.badgeid) OR
            NOT(NEW.sessionid <=> OLD.sessionid) OR
            NOT(NEW.moderator <=> OLD.moderator) OR
            NOT(NEW.createdts <=> OLD.createdts) OR
            NOT(NEW.createdbybadgeid <=> OLD.createdbybadgeid) THEN
            SIGNAL sqlstate '45000' SET message_text = 'Update field other than inactivated... in table ParticipantOnSessionHistory.';
        END IF;	
        DELETE FROM ParticipantOnSession
            WHERE
                    badgeid = OLD.badgeid
                AND sessionid = OLD.sessionid
                AND moderator = OLD.moderator;
	END;
//
CREATE TRIGGER POS_insert_trig AFTER INSERT ON ParticipantOnSession FOR EACH ROW
    IF (
        SELECT count(*) FROM ParticipantOnSession
            WHERE sessionid = NEW.sessionid AND moderator = 1)
        > 1 THEN
        SIGNAL sqlstate '45000' SET MESSAGE_TEXT = 'Attempted to insert more than one record with moderator = 1 for a single session into ParticipantOnSession.';
    END IF;
//
DELIMITER ;

INSERT INTO PatchLog (patchname) VALUES ('44_participant_on_session_history.sql');
