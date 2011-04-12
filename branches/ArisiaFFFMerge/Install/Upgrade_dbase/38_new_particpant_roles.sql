## This script renames some tables and columns to make room for new flexible particpant roles functionality
DROP TABLE IF EXISTS SessionRoleDefaults, SessionRoleOverrides;
DROP TABLE IF EXISTS ParticipantRoles;
CREATE TABLE ParticipantRoles (
	roleid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	rolename VARCHAR(50),
	display_order INT,
	is_published TINYINT,
	is_unique TINYINT,
	is_self_nominated TINYINT,
    check_conflicts TINYINT,
    interest_is_ranked TINYINT) ENGINE=INNODB DEFAULT CHARSET=utf8;
CREATE TABLE SessionRoleDefaults (
	typeid INT NOT NULL,
	roleid INT NOT NULL,
	PRIMARY KEY (typeid, roleid),
	FOREIGN KEY (typeid) REFERENCES `Types` (typeid),
	KEY roleid (roleid),
	FOREIGN KEY (roleid) REFERENCES ParticipantRoles (roleid)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
CREATE TABLE SessionRoleOverrides (
	sessionid INT NOT NULL,
	roleid INT NOT NULL,
	PRIMARY KEY (sessionid, roleid),
	FOREIGN KEY (sessionid) REFERENCES Sessions (sessionid),
	KEY roleid (roleid),
	FOREIGN KEY (roleid) REFERENCES ParticipantRoles (roleid)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE Sessions ADD COLUMN has_role_override TINYINT DEFAULT 0;
INSERT INTO PatchLog (patchname) VALUES ('38_new_particpant_roles.sql');
