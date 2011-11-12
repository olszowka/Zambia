## This script does schema changes to support the separate participant biography workflow and related pages.
## NOTE this should be applied to the Bios or General database, not to the main one, so replace OTHERDB with your DB name.
## NOTE if upgrading, the current bios should be migrated to this, before deleting the information in that table.

CREATE TABLE `OTHERDB.BioStates` (
  `biostateid` INT(2) NOT NULL auto_increment,
  `biostatename` varchar(60) default NULL,
  `display_order` INT(2) NOT NULL default '0',
  PRIMARY KEY  (`biostateid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `OTHERDB.BioStates` VALUES
        (1,'raw',1),
	(2,'edited',2),
	(3,'good',3);

CREATE TABLE `OTHERDB.BioTypes` (
  `biotypeid` INT(2) NOT NULL auto_increment,
  `biotypename` varchar(60) default NULL,
  `display_order` INT(2) NOT NULL default '0',
  PRIMARY KEY  (`biotypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `OTHERDB.BioTypes` VALUES
        (1,'web',1),
	(2,'book',2),
	(3,'uri',3),
	(4,'picture',4);

CREATE TABLE `OTHERDB.Bios` (
  `bioid` INT(11) NOT NULL auto_increment,
  `badgeid` varchar(15) NOT NULL default '',
  `biolockedby` varchar(15) default NULL,
  `biotypeid` INT(2) NOT NULL default '1',
  `biostateid` INT(2) NOT NULL default '1',
  `biolang` varchar(5) NOT NULL default 'en-us',
  `biotext` text,
  PRIMARY KEY  (`bioid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `OTHERDB.Bios` VALUES
       (1,'100',NULL,1,1,'en-us',' is the Idea Submission user'),
       (2,'100',NULL,2,1,'en-us',' is the Idea Submission user');

INSERT INTO `PatchLog` (patchname) VALUES ('34_Bio_Table_Create.sql');
