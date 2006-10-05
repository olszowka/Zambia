--
-- Table structure for table `PermissionAtoms`
--

DROP TABLE IF EXISTS `PermissionAtoms`;
CREATE TABLE `PermissionAtoms` (
  `permatomid` int(11) NOT NULL auto_increment,
  `permatomtag` varchar(20) NOT NULL default '',
  `page` varchar(20) default NULL,
  `notes` text,
  PRIMARY KEY  (`permatomid`),
  UNIQUE KEY `taginx` (`permatomtag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PermissionAtoms`
--


/*!40000 ALTER TABLE `PermissionAtoms` DISABLE KEYS */;
LOCK TABLES `PermissionAtoms` WRITE;
INSERT INTO `PermissionAtoms` VALUES (1,'Staff','renderWelcome','Enables staff menu link'),(2,'Administrator','many','Use to be determined'),(3,'Participant','many','Use to be determined'),(4,'EditBio','renderMyContact','Allow write to biography on my contact page');
UNLOCK TABLES;
/*!40000 ALTER TABLE `PermissionAtoms` ENABLE KEYS */;

--
-- Table structure for table `PermissionRoles`
--

DROP TABLE IF EXISTS `PermissionRoles`;
CREATE TABLE `PermissionRoles` (
  `permroleid` int(11) NOT NULL auto_increment,
  `permrolename` varchar(100) default NULL,
  `notes` text,
  PRIMARY KEY  (`permroleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `PermissionRoles`
--


/*!40000 ALTER TABLE `PermissionRoles` DISABLE KEYS */;
LOCK TABLES `PermissionRoles` WRITE;
INSERT INTO `PermissionRoles` VALUES (1,'Staff','Can access staff pages'),(2,'Administrator','Use to be determined'),(3,'Participant','Use to be determined');
UNLOCK TABLES;
/*!40000 ALTER TABLE `PermissionRoles` ENABLE KEYS */;

--
-- Table structure for table `Phases`
--

DROP TABLE IF EXISTS `Phases`;
CREATE TABLE `Phases` (
  `phaseid` int(11) NOT NULL auto_increment,
  `phasename` varchar(100) default NULL,
  `current` tinyint(1) default '0',
  `notes` text,
  PRIMARY KEY  (`phaseid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Phases`
--


/*!40000 ALTER TABLE `Phases` DISABLE KEYS */;
LOCK TABLES `Phases` WRITE;
INSERT INTO `Phases` VALUES (1,'Survey',1,'My Suggestions and My Interests'),(2,'Availability',0,'My Availability and My Conflicts'),(3,'Brainstorm',0,'Staff creates sessions'),(4,'Choose Sessions',0,'Panelists indicate sessio n interests');
UNLOCK TABLES;
/*!40000 ALTER TABLE `Phases` ENABLE KEYS */;

--
-- Table structure for table `Permissions`
--

DROP TABLE IF EXISTS `Permissions`;
CREATE TABLE `Permissions` (
  `permissionid` int(11) NOT NULL auto_increment,
  `permatomid` int(11) NOT NULL default '0',
  `phaseid` int(11) default '0' COMMENT 'null indicates all phases',
  `permroleid` int(11) default '0' COMMENT 'null indicates not applicable',
  `badgeid` int(11) default '0' COMMENT 'null indicates not applicable',
  PRIMARY KEY  (`permissionid`),
  UNIQUE KEY `unique1` (`permatomid`,`phaseid`,`permroleid`,`badgeid`),
  KEY `FK_Permissions` (`phaseid`),
  KEY `FK_PRoles` (`permroleid`),
  CONSTRAINT `Permissions_ibfk_1` FOREIGN KEY (`permatomid`) REFERENCES `PermissionAtoms` (`permatomid`),
  CONSTRAINT `Permissions_ibfk_2` FOREIGN KEY (`phaseid`) REFERENCES `Phases` (`phaseid`),
  CONSTRAINT `Permissions_ibfk_3` FOREIGN KEY (`permroleid`) REFERENCES `PermissionRoles` (`permroleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB';

--
-- Dumping data for table `Permissions`
--


/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
LOCK TABLES `Permissions` WRITE;
INSERT INTO `Permissions` VALUES (1,1,NULL,1,NULL),(2,3,NULL,3,NULL),(3,4,1,3,NULL),(4,4,2,3,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;

--
-- Table structure for table `UserHasPermissionRole`
--

DROP TABLE IF EXISTS `UserHasPermissionRole`;
CREATE TABLE `UserHasPermissionRole` (
  `badgeid` varchar(15) NOT NULL default '',
  `permroleid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`badgeid`,`permroleid`),
  KEY `FK_UserHasPermissionRole` (`permroleid`),
  CONSTRAINT `UserHasPermissionRole_ibfk_2` FOREIGN KEY (`permroleid`) REFERENCES `PermissionRoles` (`permroleid`),
  CONSTRAINT `UserHasPermissionRole_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
