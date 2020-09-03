## This script adds the data retention consent field to the participants table
##
##	Created by Syd Weinstein on September 3, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Participants
ADD COLUMN data_retention TINYINT NOT NULL DEFAULT '0' AFTER use_photo;

INSERT INTO CustomText(customtextid, page, tag, textcontents)
VALUES(7, 'Data Retention Consent', 'consent', 'We collect your personal data to allow us to schedule you into programming items, to publish data about your participation in programming items, and to administer the convention.  We retain this data for the duration of this convention and to assist in planning future conventions.</p><p>We do not share this data with other conventions outside of what is published for our program guides, website, and other sites we use to publish the schedule for the convention.</p><p>We transfer this data as required to print the guides, and to provide any online guides to the programming for our convention including but not limited to our website and any other on-line programing guides we may use from time to time.</p><p>Without your consent we are unable to have you as a program participant.');

INSERT INTO PatchLog (patchname) VALUES ('53_data_retention.sql');
