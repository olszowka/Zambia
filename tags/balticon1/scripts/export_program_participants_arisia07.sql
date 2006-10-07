select concat('insert into CongoDump set badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and p.prop_value=1
   ;


select concat('update CongoDump set badgename="',m.master_badgename,
                                '", firstname="',m.master_firstname,
                                '", lastname="',m.master_lastname,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and p.prop_value=1
   ;

select concat('update CongoDump set phone="',a.loc_data,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   and a.loc_type='PHONE'
   and a.loc_primary=1
   ;

select concat('update CongoDump set email="',a.loc_data,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   and a.loc_type='EMAIL'
   and a.loc_primary=1
   ;

select concat('update CongoDump set postaddress="',a.loc_data,
              '" where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   and a.loc_type='POSTAL'
   and a.loc_primary=1
   ;

select concat('update CongoDump set regtype="',h.hist_arg1,
              '" where badgeid="',X.master_rid,'";') ""
  from (select master_rid 
          from reg_master as m, reg_properties as p,
               con_detail as c
	 where p.prop_rid=m.master_rid
   	   and c.con_cid=p.prop_cid
           and c.con_cid=18
           and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
	   and p.prop_value=1 and c.con_cid=17) X, 
	reg_history h 
  where X.master_rid=h.hist_rid 
    and h.hist_actcode='REGISTERED' 
    and h.hist_cid=18
    ;

select concat('insert into Participants set badgeid="',m.master_rid,
              '", password="4cb9c8a8048fd02294477fcb1a41191a";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   ;

select concat('insert into ParticipantAvailability set badgeid="',m.master_rid,
              '";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c, reg_locations as a 
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and c.con_cid=18
   and (p.prop_name='db_ProgramStaff' or p.prop_name='db_Program' )
   and a.loc_rid=m.master_rid 
   and p.prop_value=1
   ;

select concat('delete from CongoDump where badgeid="',m.master_rid,'";') ""
  from reg_master as m, reg_properties as p, 
       con_detail as c
 where p.prop_rid=m.master_rid 
   and c.con_cid=p.prop_cid 
   and p.prop_name='Deceased' 
   and p.prop_value=1
   ;
