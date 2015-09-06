
/* create the entry in CongoDump */
select concat('insert into CongoDump set badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and p.prop_value=1
   ;


/* update the badgename, firstname and lastname in CongoDump */
select concat('update CongoDump set badgename="',m.master_badgename,
                                '", firstname="',m.master_firstname,
                                '", lastname="',m.master_lastname,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and p.prop_value=1
   ;

/* update the phone number in CongoDump */
select concat('update CongoDump set phone="',a.loc_data,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   and a.loc_type='PHONE'
   and a.loc_primary=1
   ;

/* update the email in CongoDump */
select concat('update CongoDump set email="',a.loc_data,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   and a.loc_type='EMAIL'
   and a.loc_primary=1
   ;

/* update the postal address in CongoDump */
select concat('update CongoDump set postaddress="',a.loc_data,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   and a.loc_type='POSTAL'
   and a.loc_primary=1
   ;

/* update the registration information in CongoDump */
select concat('update CongoDump set regtype="',h.hist_arg1,
              '" where badgeid="',X.master_rid,'";') ""
  from (select master_rid 
          from reg_master as m, reg_properties as p,
               con_detail as c
	 where p.prop_rid=m.master_rid
   	   and c.con_cid=p.prop_cid
           and c.con_cid=19
           and p.prop_name like 'Z_%' 
	   and p.prop_value=1 and c.con_cid=19) X, 
	reg_history h 
  where X.master_rid=h.hist_rid 
    and h.hist_actcode='REGISTERED' 
    and h.hist_cid=19
    ;

/* set up an entry in Participant with the default password 
   (if there is not already an entry present) */
select concat('insert into Participants set badgeid="',m.master_rid,
              '", password="4cb9c8a8048fd02294477fcb1a41191a";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and p.prop_value=1
   ;

/* set up an entry in ParticipantAvailability */
select concat('insert into ParticipantAvailability set badgeid="',m.master_rid,
              '";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name like 'Z_%' 
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   ;

/* Remove any dead people from CongoDump */
select concat('delete from CongoDump where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and p.prop_name='Deceased' 
   and p.prop_value=1
   ;

/* Setting up access rights for participants (permroleid=3) */
select concat('insert into UserHasPermissionRole set badgeid="',m.master_rid,'", permroleid=3;') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name='Z_%Guest' 
   and p.prop_value=1
   ;

/* Setting up access rights for staff (permroleid=1) */
select concat('insert into UserHasPermissionRole set badgeid="',m.master_rid,'", permroleid=1;') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=19
   and p.prop_name='Z_%Staff' 
   and p.prop_value=1
   ;
