## This script adds a table Imported that is a reference to all the users imported from the Anticipation mbox format into Zambia
## and their associated badge id.
CREATE TABLE Imported (
	mbox VARCHAR(40),
	message_number INTEGER default NULL,
	badgeid VARCHAR(15) NOT NULL,
    PRIMARY KEY  (badgeid)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
CREATE INDEX rawidx USING BTREE ON Imported(mbox, message_number);

CREATE TABLE LastBadgeId (
	badgeid VARCHAR(15),
	id VARCHAR(10) NOT NULL,
    PRIMARY KEY  (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO LastBadgeId (badgeid, id) VALUES ('3000', 'last');

INSERT INTO PatchLog (patchname) VALUES ('25_create_import_info.sql');
