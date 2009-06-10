CREATE TABLE `DataCleanupRef` (
  `datacleanupid` int(11) NOT NULL auto_increment,
  `datacleanupname` varchar(50) default NULL,
  `display_order` int(11) default NULL,
  PRIMARY KEY  (`datacleanupid`)
) ENGINE=InnoDB CHARSET=latin1;
INSERT INTO `DataCleanupRef`(`datacleanupid`,`datacleanupname`,`display_order`)
    VALUES (1,'None',1),(2,'By inference only',2),(3,'Confirmed with participant',3);
ALTER TABLE Participants
    add column `datacleanupid` int(11) default 1,
    add index datacleanupid (`datacleanupid`),
    add constraint `Participants_ibfk_2` FOREIGN KEY (`datacleanupid`) REFERENCES `DataCleanupRef` (`datacleanupid`);
INSERT INTO PatchLog (patchname) VALUES ('28_DataCleanupTracking.sql');

