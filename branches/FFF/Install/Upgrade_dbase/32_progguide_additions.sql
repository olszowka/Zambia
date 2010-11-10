## This script modifies Participants adding the progguide bio sections.
ALTER TABLE Participants
      ADD COLUMN progbio text AFTER bio,
      ADD COLUMN progeditedbio text AFTER editedbio,
      ADD COLUMN progscndlangbio text AFTER scndlangbio;
INSERT INTO PatchLog (patchname) VALUES ('32_progguide_additions.sql');