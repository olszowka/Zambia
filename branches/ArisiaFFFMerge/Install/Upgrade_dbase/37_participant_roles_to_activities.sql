## This script renames some tables and columns to make room for new flexible particpant roles functionality
DROP TABLE IF EXISTS ParticipantHasActivity, ParticipantActivities;
CREATE TABLE ParticipantActivities (
	activityid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	activityname VARCHAR(50),
	display_order INT) ENGINE=INNODB DEFAULT CHARSET=utf8;
CREATE TABLE ParticipantHasActivity (
	badgeid VARCHAR(15) NOT NULL DEFAULT '',
	activityid INT NOT NULL,
	PRIMARY KEY (badgeid, activityid),
	KEY activityid (activityid),
	CONSTRAINT ParticipantHasActivity_ibfk_1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid),
	CONSTRAINT ParticipantHasActivity_ibfk_2 FOREIGN KEY (activityid) REFERENCES ParticipantActivities (activityid)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
INSERT INTO ParticipantActivities
	(activityid, activityname, display_order)
	SELECT roleid, rolename, display_order FROM Roles ORDER BY roleid;
INSERT INTO ParticipantHasActivity
	(badgeid, activityid)
	SELECT badgeid, roleid FROM ParticipantHasRole ORDER BY badgeid, roleid;
DROP TABLE ParticipantHasRole;
DROP TABLE Roles;
INSERT INTO PatchLog (patchname) VALUES ('37_participant_roles_to_activities.sql');
