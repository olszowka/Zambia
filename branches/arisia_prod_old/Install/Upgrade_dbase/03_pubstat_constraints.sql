# These are the statements to implement pubstatusid constraint which goes along with modification
# of php code to implement pubstatusid on edit/create session page
# While we're at it, we're adding missing constraints to other fields on Sessions.
Update Sessions set pubstatusid=2 where pubstatusid is null;
Update Sessions set pubstatusid=2 where pubstatusid not in (select pubstatusid from PubStatuses);
alter table Sessions
    modify column trackid int not null,
    modify column typeid int not null,
    modify column kidscatid int not null,
    modify column roomsetid int not null,
    modify column statusid int not null,
    add CONSTRAINT `Sessions_ibfk_6` FOREIGN KEY (`pubstatusid`) REFERENCES `PubStatuses` (`pubstatusid`);
