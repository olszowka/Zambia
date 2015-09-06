## This script creates tables their keys for tracking session edit history.
CREATE TABLE `SessionEditCodes` (
  `sessioneditcode` int(11) NOT NULL auto_increment,
  `description` varchar(40) default NULL,
  `display_order` int(11) NOT NULL default '1',
  PRIMARY KEY  (`sessioneditcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
insert into `SessionEditCodes` (`sessioneditcode`,`description`,`display_order`) values (1,'Created in brainstorm',1);
insert into `SessionEditCodes` (`sessioneditcode`,`description`,`display_order`) values (2,'Created in staff create session',2);
insert into `SessionEditCodes` (`sessioneditcode`,`description`,`display_order`) values (3,'Unknown edit',3);
CREATE TABLE `SessionEditHistory` (
  `sessionid` int(11) NOT NULL default '0',
  `badgeid` varchar(15) default NULL,
  `name` varchar(40) default NULL,
  `email_address` varchar(75) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `sessioneditcode` int(11) NOT NULL default '0',
  `statusid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`sessionid`,`timestamp`),
  KEY `FK_SessionEditHistory` (`badgeid`),
  KEY `FK_SessionEditCodes` (`sessioneditcode`),
  KEY `FK_SessionEditHistory4` (`statusid`),
  CONSTRAINT `SessionEditHistory_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
  CONSTRAINT `SessionEditHistory_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `SessionEditHistory_ibfk_3` FOREIGN KEY (`sessioneditcode`) REFERENCES `SessionEditCodes` (`sessioneditcode`),
  CONSTRAINT `SessionEditHistory_ibfk_4` FOREIGN KEY (`statusid`) REFERENCES `SessionStatuses` (`statusid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 8192 kB';
