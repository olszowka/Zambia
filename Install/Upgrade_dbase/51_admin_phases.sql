## This script Adds fields to phases in prep for new phases screen and to allow for display order sort
##
##	Created by Syd Weinstein on April 30, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add new atom for the AdminPhases page to change Zambia Phases
##
insert into PermissionAtoms(permatomtag, page, notes)
values('AdminPhases', 'AdminPhases', 'Change phase of Zambia use, allowing which sections are current');

## Add both Administrator and AdminPhases to the Administrator role
##
insert into Permissions(permatomid, phaseid, permroleid, badgeid)
 VALUES (2, NULL, 1, NULL), (19, NULL, 1, NULL);

INSERT INTO PatchLog (patchname) VALUES ('51_admin_phases.sql');
