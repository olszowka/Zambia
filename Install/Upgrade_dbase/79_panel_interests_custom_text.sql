## This script adds a CustomText entry for the ranking-scale instructions at the top of the Panel Interests page,
## making them customizable, defaulting to inactive so the built-in default is used.
##
##  Created by Peter Olszowka on 2026-11-15;
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
INSERT IGNORE INTO `CustomText`
    (page, tag, textcontents, active, html_block_level)
    VALUES
    ('Panel Interests','ranking_scale','<p>Please use the following scale when ranking your interest in the sessions you have chosen:</p>\n<p><strong>1 &mdash;<em> Oooh! Oh! Pick Me!</em>&nbsp;&nbsp;&nbsp;2-3 &mdash; <em>I''d like to if I can</em>&nbsp;&nbsp;&nbsp;4-5 &ndash; <em>I am qualified but this is not one of my primary interests</em></strong></p>\n<p>You are limited to 4 sessions each of preferences 1-4.  There is no limit to the number of sessions for which you can express preference 5.</p>',0,1);

INSERT INTO `PatchLog` (patchname) VALUES ('79_panel_interests_custom_text.sql');
