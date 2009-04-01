## This script does all the schema changes to support the enhanced participant biography workflow and related pages.
CREATE TABLE `BioEditStatuses` (
  `bioeditstatusid` INT(11) NOT NULL auto_increment,
  `bioeditstatusname` varchar(60) DEFAULT NULL,
  `display_order` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`bioeditstatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO BioEditStatuses
        (bioeditstatusid,bioeditstatusname,display_order)
    VALUES
        (1,"Needs editing and translation",1),
        (2,"Editing done--needs translation",2),
        (3,"Translation done--needs editing",3),
        (4,"Editing and translation done",4);
ALTER TABLE Participants
    ADD COLUMN `biolockedby` VARCHAR(15) AFTER `bio`,
    ADD COLUMN `bioeditstatusid` INT(11) NOT NULL DEFAULT 1 AFTER `bio`,
    ADD COLUMN `scndlangbio` TEXT AFTER `bio`,
    ADD COLUMN `editedbio` TEXT AFTER `bio`,
    ADD KEY `bioeditstatusid` (`bioeditstatusid`),
    ADD CONSTRAINT Participants_fkey1 FOREIGN KEY (`bioeditstatusid`) REFERENCES `BioEditStatuses` (`bioeditstatusid`);
INSERT INTO PatchLog (patchname) VALUES ('21_more_bios.sql');
