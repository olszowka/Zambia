INSERT INTO PermissionAtoms (permatomid, permatomtag, page, notes)
	VALUES (16, "configure_rooms", "Configure Rooms", "Also triggers \"Configuration\" menu in staff menu bar.");
INSERT INTO Permissions (permatomid, permroleid, phaseid)
	VALUES (16, 1, NULL);
INSERT INTO PatchLog (patchname) VALUES ('43_configure_rooms.sql');
