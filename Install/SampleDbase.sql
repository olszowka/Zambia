-- Copyright (c) 2011-2026 by Peter Olszowka. All rights reserved. See copyright document for more details.

INSERT INTO `Credentials`
    (credentialid, credentialname, display_order)
    VALUES
    (1, 'Professional Artist', 10),
    (2, 'Professional Editor', 20),
    (3, 'Professional Musician', 30),
    (4, 'Published Author', 40),
    (5, 'Web Comics Creator', 50);

INSERT INTO `Divisions`
    (divisionid, divisionname, display_order)
    VALUES
    (1, 'Programming', 10),
    (2, 'Events', 20),
    (3, 'Multimedia', 30),
    (4, 'Other', 40);

INSERT INTO `EmailTo`
    (emailtoid, emailtodescription, display_order, emailtoquery)
    VALUES
    (1, 'All Program Participants', 10, 'SELECT\n            CD.badgeid, CD.firstname, CD.lastname, CD.email, P.pubsname, CD.badgename\n    FROM\n             CongoDump CD\n        JOIN Participants P USING (badgeid)\n        JOIN UserHasPermissionRole UHPR USING (badgeid)\n    WHERE\n        UHPR.permroleid = 4;'),
    (2, 'Program Participants who are attending', 20, 'SELECT\n        CD.badgeid, CD.firstname, CD.lastname, CD.email, P.pubsname, CD.badgename\n    FROM\n             Participants P\n        JOIN CongoDump CD USING (badgeid)\n        JOIN UserHasPermissionRole UHPR USING (badgeid)\n    WHERE\n            UHPR.permroleid = 3\n        AND P.interested = 1;'),
    (3, 'Program Participants who are attending and scheduled', 30, 'SELECT\n        CD.badgeid, CD.firstname, CD.lastname, CD.email, P.pubsname, CD.badgename\n    FROM\n             Participants P\n        JOIN CongoDump CD USING (badgeid)\n        JOIN UserHasPermissionRole UHPR USING (badgeid)\n    WHERE\n            UHPR.permroleid = 3\n        AND P.interested = 1\n        AND EXISTS (\n            SELECT *\n                FROM\n                         Schedule SCH\n                    JOIN ParticipantOnSession POS USING (sessionid)\n                WHERE\n                    POS.badgeid = P.badgeid\n            );');

INSERT INTO `Features`
    (featureid, featurename, display_order)
    VALUES
    (1, 'Blackout Curtains', 10),
    (2, 'Internet', 20),
    (3, 'Power (110v)', 30),
    (4, 'Power (special)', 40),
    (5, 'Sound Isolation', 50);

INSERT INTO `KidsCategories`
    (kidscatid, kidscatname, display_order)
    VALUES
    (1, 'Targeted', 10),
    (2, 'Welcome', 20),
    (3, 'Only w/ Parent', 30),
    (4, 'Not Allowed', 40);

INSERT INTO `ParticipantTags`
    (participanttagid, participanttagname, display_order)
    VALUES
    (1, 'Science Fiction', 10),
    (2, 'Fantasy', 20),
    (3, 'Science', 30);

INSERT INTO `PhotoDenialReasons`
    (photodenialreasonid, reasontext, display_order)
    VALUES
    (1, 'Inappropriate', 10),
    (2, 'Code of Conduct', 20),
    (3, 'Poor Image Quality', 30),
    (4, 'Other', 40);

INSERT INTO `Roles`
    (roleid, rolename, display_order)
    VALUES
    (1, 'Other', 10),
    (2, 'Panelist', 20),
    (3, 'Reading my own works', 30),
    (4, 'Working with children', 40),
    (5, 'Guest interviewer', 50);

INSERT INTO `RoomSets`
    (roomsetid, roomsetname, display_order)
    VALUES
    (1, 'Head table and audience (Panel)', 10),
    (2, 'Circle of chairs', 20),
    (3, 'High top tables distributed (Reception) ', 30);

INSERT INTO `Services`
    (serviceid, servicename, display_order)
    VALUES
    (1, 'Projector for laptop', 10),
    (2, 'Lectern', 20),
    (3, 'Flip Chart', 30),
    (4, 'Play audio from phone', 40);

INSERT INTO `Tags`
        (tagid, tagname, display_order)
    VALUES
    (1, 'Alternate History',  10),
    (2, 'Art', 20),
    (3, 'Anime / Manga', 30),
    (4, 'Audio Books / Podcast / Radio', 40),
    (5, 'Children', 50),
    (6, 'Comics / Graphic Novels', 60),
    (7, 'Crafts / Makers', 70),
    (8, 'Crime / Mystery', 80),
    (9, 'Fandom', 90),
    (10, 'Fantasy', 100),
    (11, 'Gaming', 110),
    (12, 'Guest', 120),
    (13, 'Horror / Dark Fantasy', 130),
    (14, 'Humor', 140),
    (15, 'Movement / Dance', 150),
    (16, 'Movies / TV / Theater', 160),
    (17, 'Music', 170),
    (18, 'Mythology / Folklore', 180),
    (19, 'Romantasy', 190),
    (20, 'Science', 200),
    (21, 'Science Fiction', 210),
    (22, 'Social Topics', 220),
    (23, 'Technology', 230),
    (24, 'Writing / Business of Writing', 240),
    (25, 'Young Adult / Teen', 250);

INSERT INTO `Tracks`
    (trackid, trackname, display_order, selfselect)
    VALUES
    (1, 'Programming', 10, 1),
    (2, 'Filk', 20, 0),
    (3, 'Children\'s Programming', 30, 0),
    (4, 'Events', 40, 0);

INSERT INTO `Types`
    (typeid, typename, display_order, selfselect)
    VALUES
      (1, 'Panel', 10, 1),
      (2, 'Autographing', 20, 0),
      (3, 'Concert', 30, 0),
      (4, 'Discussion Group', 40, 0),
      (5, 'Dramatic Performance', 50, 0),
      (6, 'Interview', 60, 0),
      (7, 'Kaffeeklatsch', 70, 0),
      (8, 'LARP', 80, 0),
      (9, 'Lecture', 90, 0),
      (10, 'Open Filk', 100, 0),
      (11, 'Open Gaming', 110, 0),
      (12, 'Projected Media', 120, 0),
      (13, 'Reading', 130, 0),
      (14, 'Scheduled Game', 140, 0),
      (15, 'Tabletop RPG', 150, 0),
      (16, 'Workshop', 160, 0),
      (17, 'Room Turn', 170, 0);
