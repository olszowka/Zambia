# Zambia Version 2.3 Release Notes

## Changes since release 2.2

### Bug Fixes
* 

### New Features
* On Panel Interests page, add session portion moved to bottom of page
* On Panel Interests page, the ranking-scale instructions can now be customized with Custom Text (page
  "Panel Interests", tag `ranking_scale`)
 
### Invisible code cleanup
* Remove redundant calls to fetch custom text and change query to prepared

## Application Notes

1) 

### db_name.php

### Schema Patches

#### 79_panel_interests_custom_text.sql

Just apply this patch as normal. It adds an inactive Custom Text entry (page "Panel Interests", tag
`ranking_scale`) holding the default ranking-scale instructions shown at the top of the Panel Interests page. While
it is inactive (or missing), the page shows the built-in default text. To customize the instructions, edit the entry
and make it active. 
