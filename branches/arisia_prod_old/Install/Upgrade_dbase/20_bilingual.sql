## This script creates a new table to hold status options for session language
## Then it modifies the Sessions table to add a column to record that status and a column for the title in the second 
##  language.  The assumption is that the second description will go in the unused pocketprogtext field.
CREATE TABLE `LanguageStatuses` (
  `languagestatusid` int(11) NOT NULL auto_increment,
  `languagestatusname` varchar(30) default NULL,
  `display_order` int(11) default NULL,
  PRIMARY KEY  (`languagestatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

insert into LanguageStatuses 
        (languagestatusid, languagestatusname, display_order)
    values
        (1, "English", 1),
        (2, "French", 2),
        (3, "Bilingual", 3);

Alter Table Sessions
    Add Column `languagestatusid` int(11) default 1 after pubstatusid,
    Add Column `secondtitle` varchar(100) default NULL after title,
    Add Key `languagestatusid` (`languagestatusid`),
    Add Constraint Sessions_ibfk_8 FOREIGN KEY (`languagestatusid`) REFERENCES `LanguageStatuses` (`languagestatusid`);

Insert into PatchLog (patchname) values ('20_bilingual.sql');
