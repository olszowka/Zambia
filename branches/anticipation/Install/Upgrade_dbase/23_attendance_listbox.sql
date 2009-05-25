## This script does all the schema changes to support a reference table for a listbox for estimated attendance.
CREATE TABLE EstimatedAttendanceRef (
    estatten int(11) NOT NULL,
    description varchar(75) default NULL,
    display_order int(11) NOT NULL,
    PRIMARY KEY  (estatten)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO EstimatedAttendanceRef
        (estatten, description, display_order)
        values
        (50, "50", 1),
        (100, "100", 2),
        (150 , "150",  3),
        (200 , "200",  4),
        (250 , ">200",  5);
INSERT INTO PatchLog (patchname) VALUES ('23_attendance_listbox.sql');
