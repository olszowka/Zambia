## This script does all the schema changes to support a reference table for a listbox for estimated attendance.
CREATE TABLE DurationRef (
    duration int(11) NOT NULL,
    description varchar(25) default NULL,
    display_order int(11) NOT NULL,
    PRIMARY KEY  (duration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO DurationRef 
        (duration, description, display_order)
        values
        (30, "30", 1),
        (60, "60", 2),
        (90, "90", 3),
        (120, "120", 4),
        (150, "150", 5),
        (180, "180", 6),
        (210, "210", 7),
        (240, "240", 8),
        (360, "360", 9),
        (1440, "1440", 10);
INSERT INTO PatchLog (patchname) VALUES ('24_duration_listbox.sql');
