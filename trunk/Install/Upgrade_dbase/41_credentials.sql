## This script adds a new table to support the custom text mechanism
CREATE TABLE `Credentials` (
	`credentialid` int(11) NOT NULL auto_increment,
	`credentialname` varchar(100),
	`display_order` int(11),
	PRIMARY KEY  (`credentialid`)
) ENGINE=InnoDB;
CREATE TABLE `ParticipantHasCredential` (
	`badgeid` varchar(15) NOT NULL,
	`credentialid` int(11) NOT NULL,
	PRIMARY KEY  (`badgeid`,`credentialid`),
	CONSTRAINT phcfk1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid),
	CONSTRAINT phcfk2 FOREIGN KEY (credentialid) REFERENCES Credentials(credentialid)
) ENGINE=InnoDB;
INSERT INTO PatchLog (patchname) VALUES ('41_credentials.sql');
