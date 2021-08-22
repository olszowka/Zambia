## This script adds new table Interests, ParticipantHasInterest and permissions for these
##
##  Created by Leane Verhulst on August 21, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##


CREATE TABLE `Interests` (
  `interestid` int(11) NOT NULL AUTO_INCREMENT,
  `interestname` varchar(100) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`interestid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Interests` (`interestid`, `interestname`, `display_order`) VALUES
(1, 'Science Fiction Literature', 30),
(2, 'Astronomy/Space Exploration', 5),
(3, 'Fantasy Literature', 16),
(4, 'Computers / Information Technology', 10),
(5, 'Paranormal Romance', 25),
(6, 'Technology', 32),
(7, 'Urban Fantasy', 34),
(8, 'Science', 29),
(9, 'Horror Literature', 22),
(10, 'Medicine / Health', 23),
(11, 'YA / Kids Genre Literature', 38),
(12, 'Communication Systems', 9),
(13, 'Comics', 8),
(14, 'Transportation', 33),
(15, 'Environmental Issues', 14),
(16, 'Editing', 13),
(17, 'Writing', 37),
(18, 'Publishing', 27),
(19, 'Art - 2D', 2),
(20, 'Art - 3D', 3),
(21, 'Art Demos', 4),
(22, 'Filk / Music', 18),
(23, 'Genre TV Series', 21),
(24, 'Genre Movies', 20),
(25, 'Anime', 1),
(26, 'Cosplay / Costuming', 12),
(27, 'Weaponry (Discussion)', 36),
(28, 'Weaponry (Demos)', 35),
(29, 'Role Playing Games (RPGs)', 28),
(30, 'Collectible Card Games (CCGs)', 7),
(31, 'Board Games', 6),
(32, 'Online Gaming', 24),
(33, 'Game Design', 19),
(34, 'Philosophy', 26),
(35, 'Fandom and Social Issues', 15),
(36, 'Social Media', 31),
(37, 'Convention Running', 11),
(38, 'Fanzines', 17);


CREATE TABLE `ParticipantHasInterest` (
  `badgeid` varchar(15) NOT NULL,
  `interestid` int(11) NOT NULL,
  PRIMARY KEY (`badgeid`,`interestid`),
  KEY `phifk2` (`interestid`),
  CONSTRAINT `phifk1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `phifk2` FOREIGN KEY (`interestid`) REFERENCES `Interests` (`interestid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

## Set up permissions. Check that permissionatom 2023 does not already exist.
INSERT INTO `PermissionAtoms` (`permatomid`, `permatomtag`, `page`, `notes`) VALUES ('2023', 'ce_Interests', 'Edit Configuration Tables', 'enables edit');
INSERT INTO `Permissions` (`permissionid`, `permatomid`, `phaseid`, `permroleid`, `badgeid`) VALUES (NULL, '2023', NULL, '1', NULL);


INSERT INTO PatchLog (patchname) VALUES ('66CAP_interests.sql');
