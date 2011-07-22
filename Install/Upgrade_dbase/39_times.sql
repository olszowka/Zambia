## This script modifies the Times table and repopulates it for more flexibility in specifying possible 
## participant availability times
TRUNCATE TABLE Times;
ALTER TABLE Times
	CHANGE timeindex timeid INT(11) NOT NULL,
	CHANGE timetext timedisplay CHAR(14),
	ADD COLUMN next_day TINYINT(4) AFTER timedisplay,
	ADD COLUMN timevalue TIME AFTER timedisplay,
	ADD COLUMN avail_end TINYINT(4) AFTER display_order,
	ADD COLUMN avail_start TINYINT(4) AFTER display_order;
INSERT INTO Times
 	(timeid, timedisplay, timevalue, next_day, display_order, avail_start, avail_end)
	VALUES
	(1, '8:30a', '08:30:00', 0, 1, 1, 0),
	(2, '10:00a', '10:00:00', 0, 2, 1, 1),
	(3, '11:30a', '11:30:00', 0, 3, 1, 1),
	(4, '1:00p', '13:00:00', 0, 4, 1, 1),
	(5, '2:30p', '14:30:00', 0, 5, 1, 1),
	(6, '4:00p', '16:00:00', 0, 6, 1, 1),
	(7, '5:30p', '17:30:00', 0, 7, 1, 1),
	(8, '7:00p', '19:00:00', 0, 8, 1, 1),
	(9, '8:30p', '20:30:00', 0, 9, 1, 1),
	(10, '10:00p', '22:00:00', 0, 10, 1, 1),
	(11, '11:30p', '23:30:00', 0, 11, 1, 1),
	(12, '1:00a (+1d)', '01:00:00', 1, 12, 0, 1);
INSERT INTO PatchLog (patchname) VALUES ('39_times.sql');

        
