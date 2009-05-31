## This script adds a table Imported that is a reference to all the users imported from the Anticipation mbox format into Zambia
## and their associated badge id.
CREATE TABLE Imported (
	mbox VARCHAR(40),
	message_number INTEGER default NULL,
	badgeid VARCHAR(15) NOT NULL,
    PRIMARY KEY  (badgeid)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
CREATE INDEX rawidx USING BTREE ON Imported(mbox, message_number);

INSERT INTO PatchLog (patchname) VALUES ('25_create_import_info.sql');
