## This script adds new table for service type and adds field to service table.
##   Also adds config table permissions.
##
##  Created by Leane Verhulst on August 23, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

CREATE TABLE `ServiceTypes` (
  `servicetypeid` int(11) NOT NULL AUTO_INCREMENT,
  `servicetypename` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`servicetypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ServiceTypes` (`servicetypeid`, `servicetypename`, `display_order`) VALUES
(1, 'Programming', 1),
(2, 'AV', 2),
(3, 'Hotel', 3),
(100, 'Other', 100);


## Add new field and key for service type
ALTER TABLE `Services` ADD COLUMN `servicetypeid` int(11) NOT NULL AFTER `servicename`;
ALTER TABLE `Services` ADD KEY `servicetypeid` (`servicetypeid`);


## Set up permissions. Check that permissionatom 2027 does not already exist.
INSERT INTO `PermissionAtoms` (`permatomid`, `permatomtag`, `page`, `notes`) VALUES ('2027', 'ce_ServiceTypes', 'Edit Configuration Tables', 'enables edit');
INSERT INTO `Permissions` (`permissionid`, `permatomid`, `phaseid`, `permroleid`, `badgeid`) VALUES (NULL, '2027', NULL, '1', NULL);


INSERT INTO PatchLog (patchname) VALUES ('69CAP_service_type.sql');
