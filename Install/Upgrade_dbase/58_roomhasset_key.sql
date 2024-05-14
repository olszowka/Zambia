## This script modifies the RoomHasSet table to have a unique increment primary key for use in table editing
##
##	Created by Syd Weinstein on January 7,2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
CREATE TABLE RoomHasSet_tmp (
  roomhassetid int NOT NULL AUTO_INCREMENT,
  roomid int NOT NULL,
  roomsetid int NOT NULL,
  capacity int DEFAULT NULL,
  display_order int NULL DEFAULT 0,
  PRIMARY KEY (roomhassetid),
  KEY roomid (roomid),
  KEY roomsetid (roomsetid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO RoomHasSet_tmp(roomid, roomsetid, capacity)
SELECT roomid, roomsetid, capacity
FROM RoomHasSet;

ALTER TABLE RoomHasSet DROP FOREIGN KEY RoomHasSet_ibfk_1;
ALTER TABLE RoomHasSet DROP FOREIGN KEY RoomHasSet_ibfk_2;

RENAME TABLE RoomHasSet TO RoomHasSet_obsolete;
RENAME TABLE RoomHasSet_tmp to RoomHasSet;

ALTER TABLE RoomHasSet 
	ADD CONSTRAINT RoomHasSet_ibfk_1 FOREIGN KEY (roomid) REFERENCES Rooms (roomid);
ALTER TABLE RoomHasSet 
	ADD CONSTRAINT RoomHasSet_ibfk_2 FOREIGN KEY (roomsetid) REFERENCES RoomSets (roomsetid);

##When you are sure it worked, (or in a later patch), drop the old _obsolete table
##DROP TABLE RoomHasSet_obsolete;


INSERT INTO PatchLog (patchname) VALUES ('58_roomhasset_key.sql');
