CREATE TABLE `PubStatuses` (
  `pubstatusid` int(11) NOT NULL auto_increment,
  `pubstatusname` varchar(12) default NULL,
  `display_order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pubstatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

Insert into PubStatuses values (1,'Staff only',1),(2,'Public',2),(3,'Do not print',3);

Alter table Sessions
     add column `pubstatusid` int(11) default '0' after typeid,
     add column `progguiddesc` text character set latin1
         collate latin1_general_ci default NULL after pocketprogtext;

Update Sessions set pubstatusid=2;

Alter table Participants
    add column `pubsname` varchar(50) character set latin1 collate latin1_general_ci default NULL;
