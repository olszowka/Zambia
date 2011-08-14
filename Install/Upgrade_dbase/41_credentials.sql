## This script creates two tables to support the new participant credentials info tracking
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
