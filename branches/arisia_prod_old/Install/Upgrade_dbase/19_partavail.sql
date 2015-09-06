## This script removes the daily maxprog columns and create a new table for that data
## so that the number of days of the con is completely flexible.
ALTER TABLE ParticipantAvailability DROP COLUMN fridaymaxprog, DROP COLUMN saturdaymaxprog, 
   DROP COLUMN sundaymaxprog, DROP COLUMN mondaymaxprog;
CREATE TABLE `ParticipantAvailabilityDays` (
  `badgeid` varchar(15) NOT NULL,
  `day` smallint(6) NOT NULL,
  `maxprog` int(11) default NULL,
  PRIMARY KEY  (`badgeid`,`day`),
  CONSTRAINT `ParticipantAvailabilityDays_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;
Insert into PatchLog (patchname) values ('19_partavail.sql');
