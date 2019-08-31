## This script drops the four tables which used to contain report configuration
##
##	Created by Peter Olszowka on 2019-02-06;
## 	Copyright (c) 2019 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
DROP TABLE CategoryHasReport;
DROP TABLE ReportCategories;
DROP TABLE ReportQueries;
DROP TABLE ReportTypes;
INSERT INTO PatchLog (patchname) VALUES ('45_drop_report_tables.sql');
