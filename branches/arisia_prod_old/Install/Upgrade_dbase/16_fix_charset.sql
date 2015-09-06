## This script convert all the tables with anything else to charset latin1
## collation latin1_swedish_ci which is the default.
alter table Divisions convert to character set latin1 collate latin1_swedish_ci;
alter table Participants convert to character set latin1 collate latin1_swedish_ci;
alter table PubCharacteristics convert to character set latin1 collate latin1_swedish_ci;
alter table Sessions convert to character set latin1 collate latin1_swedish_ci;
Insert into PatchLog (patchname) values ('16_fix_charset.sql');
