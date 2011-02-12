## This script does all the schema changes to support comments on both participants and classes.
CREATE TABLE `CommentsOnParticipants` (
  `badgeid` varchar(15) NOT NULL default '',
  `rbadgeid` varchar(15) NOT NULL default '',
  `cn` INT(11) NOT NULL auto_increment,
  `commenter` text,
  `comment` text,
  PRIMARY KEY  (`cn`),
  KEY `badgeid` (`badgeid`),
  KEY `rbadgeid` (`rbadgeid`),
  CONSTRAINT `CommentsOnParticipants_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `CommentsOnSessions` (
  `sessionid` int(11) NOT NULL default '0',
  `rbadgeid` varchar(15) NOT NULL default '',
  `cn` INT(11) NOT NULL auto_increment,
  `commenter` text,
  `comment` text,
  PRIMARY KEY  (`cn`),
  KEY `sessionid` (`sessionid`),
  KEY `rbadgeid` (`rbadgeid`),
  CONSTRAINT `CommentsOnSessions_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `CommentsOnProgramming` (
  `rbadgeid` varchar(15) NOT NULL default '',
  `cn` INT(11) NOT NULL auto_increment,
  `commenter` text,
  `comment` text, 
  PRIMARY KEY  (`cn`), 
  KEY `rbadgeid` (`rbadgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO PatchLog (patchname) VALUES ('24_comments_tables.sql');
