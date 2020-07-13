## This script extends the length of the password field in the Participants table.
##
##	Created by Peter Olszowka on July 12, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Participants
    CHANGE password password VARCHAR(255) NOT NULL DEFAULT '';

INSERT INTO PatchLog (patchname) VALUES ('52_password_security.sql');
