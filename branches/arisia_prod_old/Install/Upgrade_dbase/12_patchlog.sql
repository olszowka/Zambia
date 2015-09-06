## This script creates a table for tracking which patches have been applied to a db.
CREATE TABLE `PatchLog` (
  `patchname` varchar(40) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
insert into `PatchLog` (`patchname`) values ('01_addpermtables.sql'),
   ('02_pubstats.sql.sql'),('03_pubstat_constraints.sql'),('04_divinfo.sql'),
   ('05_PermissionAtoms_update.sql'),('06_some_participant_perms.sql'),
   ('07_participant_write_perms.sql'),('08_add_brainstorm.sql'),('09_session_history.sql'),
   ('10_types.sql'),('11_perm_bug.sql'),('12_patchlog.sql');
