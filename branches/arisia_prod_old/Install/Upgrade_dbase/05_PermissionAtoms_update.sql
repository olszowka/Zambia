#This script implements the db changes necessary to db-specific permissions model. 
#It must be run at the same time as the php code is released for these changes
Alter table PermissionAtoms 
    add column elementid int(11) after permatomtag;
drop index taginx on PermissionAtoms;
create unique index taginx on PermissionAtoms (permatomtag, elementid);

