## This script creates 4 tables which are used by the new report engine
CREATE TABLE `ReportTypes` (
  `reporttypeid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text,
  `technology` varchar(200) DEFAULT NULL COMMENT 'notes for tracking migration only',
  `oldmechanism` tinyint(4) NOT NULL DEFAULT '0',
  `ondemand` tinyint(4) DEFAULT NULL COMMENT '=1 for all new tech and some old',
  `filename` varchar(35) DEFAULT NULL COMMENT 'populate only if oldmechanism = 1',
  `xsl` text COMMENT 'populate only if oldmechanism = 0',
  `download` tinyint(4) DEFAULT NULL COMMENT 'disregard if oldmechanism = 1',
  `downloadfilename` varchar(25) DEFAULT NULL COMMENT 'populate only if download = 1',
  `display_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`reporttypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=latin1;
CREATE TABLE `ReportQueries` (
  `reportqueryid` int(11) NOT NULL AUTO_INCREMENT,
  `reporttypeid` int(11) NOT NULL,
  `queryname` varchar(25) NOT NULL,
  `query` text,
  PRIMARY KEY (`reportqueryid`),
  KEY `FK_ReportQueries` (`reporttypeid`),
  CONSTRAINT `FK_ReportQueries` FOREIGN KEY (`reporttypeid`) REFERENCES `ReportTypes` (`reporttypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
CREATE TABLE `ReportCategories` (
  `reportcategoryid` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`reportcategoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
CREATE TABLE `CategoryHasReport` (
  `reportcategoryid` int(11) NOT NULL,
  `reporttypeid` int(11) NOT NULL,
  PRIMARY KEY (`reportcategoryid`,`reporttypeid`),
  KEY `FK_CategoryHasReport2` (`reporttypeid`),
  CONSTRAINT `FK_CategoryHasReport` FOREIGN KEY (`reportcategoryid`) REFERENCES `ReportCategories` (`reportcategoryid`),
  CONSTRAINT `FK_CategoryHasReport2` FOREIGN KEY (`reporttypeid`) REFERENCES `ReportTypes` (`reporttypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;