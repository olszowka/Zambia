## This script adds a new table to support the custom text mechanism
CREATE TABLE `CustomText` (
	`customtextid` int(11) NOT NULL auto_increment,
	`page` varchar(100),
	`tag` varchar(25),
	`textcontents` text,
	PRIMARY KEY  (`customtextid`),
	UNIQUE INDEX(page, tag)
) ENGINE=InnoDB; 
INSERT INTO PatchLog (patchname) VALUES ('40_custom_text.sql');
