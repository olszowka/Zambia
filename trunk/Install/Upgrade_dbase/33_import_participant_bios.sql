## This script creates 1 new table for capture of participant data from previous cons and importing into current con.
CREATE TABLE PreviousParticipants (
    badgeid varchar(15) NOT NULL,
    bio text,
    PRIMARY KEY (badgeid)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO PatchLog (patchname) VALUES ('33_import_participant_bios.sql');

