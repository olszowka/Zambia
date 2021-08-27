## This script adds new table for locations.
##   Also adds config table permissions.
##
##  Created by Leane Verhulst on August 26, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

CREATE TABLE `Locations` (
  `locationid` int(11) NOT NULL AUTO_INCREMENT,
  `locationname` varchar(30) NOT NULL,
  `locationhours` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`locationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


## Set up permissions. Check that permissionatom 2028 does not already exist.
INSERT INTO `PermissionAtoms` (`permatomid`, `permatomtag`, `page`, `notes`) VALUES ('2028', 'ce_Locations', 'Edit Configuration Tables', 'enables edit');
INSERT INTO `Permissions` (`permissionid`, `permatomid`, `phaseid`, `permroleid`, `badgeid`) VALUES (NULL, '2028', NULL, '1', NULL);


INSERT INTO PatchLog (patchname) VALUES ('72CAP_locations.sql');
