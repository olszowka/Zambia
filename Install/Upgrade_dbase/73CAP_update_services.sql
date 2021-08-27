## This script updates a field.
##
##  Created by Leane Verhulst on August 26, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##


## Update field
ALTER TABLE `Services` ALTER `servicetypeid` SET DEFAULT 1;



INSERT INTO PatchLog (patchname) VALUES ('73CAP_update_services.sql');
