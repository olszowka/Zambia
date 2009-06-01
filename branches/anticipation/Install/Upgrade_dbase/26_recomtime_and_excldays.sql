## This script does two big changes
## Implement "recommended time" by creating reference table, populating it,
##   and add field to Sessions with foreign key.
## Implement "excluded days" by creating table with foreign key.
CREATE TABLE `RecommendTimeRef` (
  `recommendedtimeid` int(11) NOT NULL auto_increment,
  `recommendedtimename` varchar(25) default NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY  (`recommendedtimeid`)
) ENGINE=InnoDB;
INSERT INTO RecommendTimeRef 
    (recommendedtimeid, recommendedtimename, display_order)
    VALUES
    (1, 'AM', 1),
    (2, 'PM', 2),
    (3, 'PPM', 3);
ALTER TABLE Sessions
    add column `recommendedtimeid` int(11) default NULL,
    add index recommendedtimeid (`recommendedtimeid`);
ALTER TABLE Sessions
    add constraint `Sessions_ibfk_9` FOREIGN KEY (`recommendedtimeid`) REFERENCES `RecommendTimeRef` (`recommendedtimeid`);
CREATE TABLE `SessionExcludedDays` (
  `sessionid` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `excluded` tinyint(4) default NULL,
  PRIMARY KEY  (`sessionid`,`day`),
  CONSTRAINT `FK_SessionExcludedDays` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`)
) ENGINE=InnoDB;
INSERT INTO PatchLog (patchname) VALUES ('25_recomtime_and_excldays.sql');
