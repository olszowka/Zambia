#This script implements the db changes necessary to support division and Publication Characteristic
#on the edit/create session page.  It must be run at the same time as the php code is released for
#these changes
CREATE TABLE `Divisions` (
    `divisionid` int(11) NOT NULL auto_increment,
    `divisionname` varchar(30) character set latin1 collate latin1_general_ci default NULL,
    `display_order` int(11) NOT NULL default '0',
    PRIMARY KEY  (`divisionid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

Insert into Divisions values
    ('1','Other','5'),
    ('2','Programming','1'),
    ('3','Events','2'),
    ('4','Fixed Functions','3'),
    ('5','Hotel','4');
    
Alter table Sessions 
    add column divisionid int(11) after typeid;
   
Update Sessions set divisionid=1;

Alter table Sessions 
    modify column divisionid int(11) not null,
    add CONSTRAINT `Sessions_ibfk_7` FOREIGN KEY (`divisionid`) REFERENCES `Divisions` (`divisionid`);

Create table PubCharacteristics (
    pubcharid int(11) auto_increment,
    pubcharname varchar(30) character set latin1 collate latin1_general_ci default NULL,
    pubchartag varchar(10) character set latin1 collate latin1_general_ci default NULL,
    display_order int(11) NOT NULL default '0',
    PRIMARY KEY  (`pubcharid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

Insert into PubCharacteristics values
    (1,'35mm','35mm',1),
    (2,'16mm','16mm',2),
    (3,'dubbed','dubbed',3),
    (4,'anime','anime',4);
   
Create table SessionHasPubChar (
    sessionid int(11) not null,
    pubcharid int(11) not null,
    PRIMARY KEY  (`sessionid`,`pubcharid`),
    CONSTRAINT `Fkey1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `Fkey2` FOREIGN KEY (`pubcharid`) REFERENCES `PubCharacteristics` (`pubcharid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

