## This script does all the schema changes to support the email queueing mechanism
CREATE TABLE `EmailQueue` (
  `emailqueueid` int(11) NOT NULL auto_increment,
  `emailto` varchar(255) default NULL,
  `emailfrom` varchar(255) default NULL,
  `emailcc` varchar(255) default NULL,
  `emailsubject` varchar(255) default NULL,
  `body` text,
  `status` int(11) NOT NULL,
  `emailtimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`emailqueueid`)
) ENGINE=InnoDB; 
INSERT INTO PatchLog (patchname) VALUES ('29_email_queue.sql');
