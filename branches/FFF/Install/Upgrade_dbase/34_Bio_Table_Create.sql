## This script does schema changes to support the separate participant
## biography workflow and related pages. 
## NOTE this should be applied to the Bios or General database, not to
## the main one, so replace BIODB with your DB name.  If you are only
## hosting one database (and not having your BIODB available to
## multiple cons) you can remove all instances of "BIODB." below.
## NOTE if upgrading, the current bios should be migrated to this,
## before deleting the information in that table. 

CREATE TABLE BIODB.BioStates (
  `biostateid` INT(2) NOT NULL auto_increment,
  `biostatename` varchar(60) default NULL,
  `display_order` INT(2) NOT NULL default '0',
  PRIMARY KEY  (`biostateid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO BIODB.BioStates VALUES
        (1,'raw',1),
	(2,'edited',2),
	(3,'good',3);

CREATE TABLE BIODB.BioTypes (
  `biotypeid` INT(2) NOT NULL auto_increment,
  `biotypename` varchar(60) default NULL,
  `display_order` INT(2) NOT NULL default '0',
  PRIMARY KEY  (`biotypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO BIODB.BioTypes VALUES
        (1,'web',1),
	(2,'book',2),
	(3,'uri',3),
	(4,'picture',4);

CREATE TABLE BIODB.Bios (
  `bioid` INT(11) NOT NULL auto_increment,
  `badgeid` varchar(15) NOT NULL default '',
  `biolockedby` varchar(15) default NULL,
  `biotypeid` INT(2) NOT NULL default '1',
  `biostateid` INT(2) NOT NULL default '1',
  `biolang` varchar(5) NOT NULL default 'en-us',
  `biotext` text,
  PRIMARY KEY  (`bioid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO BIODB.Bios VALUES
       (1,'100',NULL,1,1,'en-us',' is the Idea Submission user'),
       (2,'100',NULL,2,1,'en-us',' is the Idea Submission user');

## To migrate existing bios information to the database, do:
#
## Migrate the information to the new bios table
## raw web bio
# INSERT BIODB.Bios (badgeid,biotypeid,biostateid,biolang,biotext)
#   SELECT badgeid,1,1,'en-us',bio FROM Participants where bio IS NOT NULL AND bio!="";
## edited web bio
# INSERT BIODB.Bios (badgeid,biotypeid,biostateid,biolang,biotext)
#   SELECT badgeid,1,2,'en-us',editedbio FROM Participants where editedbio IS NOT NULL AND editedbio!="";
## raw book bio
# INSERT BIODB.Bios (badgeid,biotypeid,biostateid,biolang,biotext)
#   SELECT badgeid,2,1,'en-us',progbio FROM Participants where progbio IS NOT NULL AND progbio!="";
## edited book bio
# INSERT BIODB.Bios (badgeid,biotypeid,biostateid,biolang,biotext)
#   SELECT badgeid,2,2,'en-us',progeditedbio FROM Participants where progeditedbio IS NOT NULL AND progeditedbio!="";
# edited web bio (second language)
# INSERT BIODB.Bios (badgeid,biotypeid,biostateid,biolang,biotext)
#   SELECT badgeid,1,2,'fr-ca',scndlangbio FROM Participants where scndlangbio IS NOT NULL AND scndlangbio!="";
# edited book bio (second language)
# INSERT BIODB.Bios (badgeid,biotypeid,biostateid,biolang,biotext)
#   SELECT badgeid,2,2,'fr-ca',progscndlangbio FROM Participants where progscndlangbio IS NOT NULL AND progscndlangbio!="";
#
## Get rid of the legacy bio information in Participants
# ALTER TABLE Participants 
#   DROP FOREIGN KEY Participants_fkey1,
#   DROP KEY bioeditstatusid;
#   DROP COLUMN bio,
#   DROP COLUMN editedbio, 
#   DROP COLUMN progbio, 
#   DROP COLUMN progeditedbio, 
#   DROP COLUMN scndlangbio, 
#   DROP COLUMN progscndlangbio, 
#   DROP COLUMN biolockedby, 
#   DROP COLUMN bioeditstatusid;
# DROP TABLE BioEditStatuses;

INSERT INTO `PatchLog` (patchname) VALUES ('34_Bio_Table_Create.sql');
