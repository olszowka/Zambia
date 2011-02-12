## This script creates the tracking on interactions with a participant for the programming staff.
CREATE TABLE `NotesOnParticipants` (
  `badgeid` varchar(15) NOT NULL default '',
  `rbadgeid` varchar(15) NOT NULL default '',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `noteid` INT(11) NOT NULL auto_increment,
  `note` text,
  PRIMARY KEY  (`noteid`),
  KEY `badgeid` (`badgeid`),
  KEY `rbadgeid` (`rbadgeid`),
  CONSTRAINT `NotesOnParticipants_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `NotesOnParticipants_ibfk_2` FOREIGN KEY (`rbadgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO PatchLog (patchname) VALUES ('31_participant_notes.sql');
