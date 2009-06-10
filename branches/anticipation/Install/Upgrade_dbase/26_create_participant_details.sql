##
ALTER TABLE Participants
   ADD COLUMN masque TINYINT(1),
   ADD COLUMN willmoderate TINYINT(1),
   ADD COLUMN willparteng TINYINT(1),
   ADD COLUMN willpartengtrans TINYINT(1),
   ADD COLUMN willpartfre TINYINT(1),
   ADD COLUMN willpartfretrans TINYINT(1),
   ADD COLUMN speaksFrench TINYINT(1),
   ADD COLUMN speaksEnglish TINYINT(1),
   ADD COLUMN speaksOther TINYINT(1),
   ADD COLUMN otherLangs TEXT;

CREATE TABLE ParticipantTrackInterest (
   badgeid VARCHAR(15) NOT NULL default '',
   trackid INT(11) NOT NULL default '0',
   PRIMARY KEY (badgeid,trackid),
   KEY trackid1 (trackid),
   CONSTRAINT PTIfk_1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid),
   CONSTRAINT PTIfk_2 FOREIGN KEY (trackid) REFERENCES Tracks (trackid))
   ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE GeneralInfoRef (
 infoid int(11) NOT NULL auto_increment,
 info_description varchar(200) default NULL,
 display_order int(11) NOT NULL,
 PRIMARY KEY  (`infoid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1;

INSERT INTO GeneralInfoRef
	(infoid, info_description, display_order)
	VALUES
	(1, "General special knowledge", 1),
	(2, "favourite talks", 2),
	(3, "favourite tv", 3),
	(4, "favourite reads", 4),
	(5, "favourite listen", 5),
	(6, "I do not want to talk about", 6),
	(7, "I do not want to share panels with", 7),
	(8, "I have demonstration skills of", 8),
	(9, "My special needs are", 9),
	(10, "My recent publications are", 10),
	(11, "My gaming knowledge is", 11),
	(12, "My visual arts knowledge is", 12),
	(13, "My culture knowledge is", 13),
	(14, "My kids track experiance is", 14)
	;

CREATE TABLE ParticipantGeneralInfo (
   badgeid VARCHAR(15) NOT NULL default '',
   infoid INT(11) NOT NULL default '0',
   infovalue TEXT,
   PRIMARY KEY (badgeid,infoid),
   KEY infoid1 (infoid),
   CONSTRAINT PGIfk_1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid),
   CONSTRAINT PGIfk_2 FOREIGN KEY (infoid) REFERENCES GeneralInfoRef (infoid))
   ENGINE=InnoDB DEFAULT CHARSET=latin1;
   
INSERT INTO PatchLog (patchname) VALUES ('26_create_participant_details.sql');

