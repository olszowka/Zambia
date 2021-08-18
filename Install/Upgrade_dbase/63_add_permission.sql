## This script adds a missing administrator permission for table editing
##
## Created by Leane Verhulst on August 16,2021
## Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##



INSERT INTO Permissions(permatomid, phaseid, permroleid, badgeid)
VALUES (2, NULL, 1 NULL);

INSERT INTO PatchLog (patchname) VALUES ('63_add_permission.sql');
