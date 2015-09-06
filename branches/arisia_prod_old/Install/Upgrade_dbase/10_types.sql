alter table Types add column selfselect tinyint(1) after display_order; 
update Types set selfselect=0;
update Types set selfselect=1 
 where typename in ('Panel','Presentation','Game','Participatory Session');
