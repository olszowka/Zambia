## This script adds the Interesetd table which permits the easy translation 
## of values to meanings. 
create table `Interested` ( 
  `interested` tinyint(1), 
  `interestedname` varchar(100), 
  primary key (`interested`)
) ENGINE=InnoDB;
insert into Interested set interested=1, interestedname='Will attend';
insert into Interested set interested=2, interestedname='Will not attend';
insert into Interested set interested=0, interestedname='didnot say';

alter table Participants 
  add foreign key  (`interested`) references Interested (`interested`);

insert into `PatchLog` (`patchname`) values ('14_interested_table.sql');
