## This script adds the 'ResetUserPassword' permission atom and grants it to senior staff.
##
##	Created by Peter Olszowka on 2020-04-22;
## 	Copyright (c) 2020

CREATE TABLE ParticipantPasswordResetRequests (
    badgeidentered varchar(15) NOT NULL DEFAULT '' COMMENT 'Not necessary a valid badgeid, so no Foreign Key',
    email varchar(255) NOT NULL DEFAULT '',
    ipaddress varchar(225) NOT NULL DEFAULT '',
    creationdatetime datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expirationdatetime datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    selector char(16) NOT NULL DEFAULT '',
    token char(64) NOT NULL DEFAULT '',
    cancelled tinyint(4) NOT NULL DEFAULT '0',
    PRIMARY KEY (badgeidentered, creationdatetime),
    UNIQUE KEY PPRR_selector (selector)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO PatchLog (patchname) VALUES ('50_password_reset_self.sql');
