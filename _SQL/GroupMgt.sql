
/*** ADD NEW USER ***/
/*
insert into Member (FromEmail, MemberPassWord) values ('NewMEmberName', 'welcome1!');
insert into GroupMember (GroupName, FromEmail) values ('Company', 'NewMEmberName') ;
insert into GroupMember (GroupName, FromEmail) values ('<MemberGroup>', 'NewMEmberName') ;

insert into Member (FromEmail, MemberPassWord) values ('Charles', 'welcome1!');
insert into GroupMember (GroupName, FromEmail) values ('Company', 'Charles') ;
insert into GroupMember (GroupName, FromEmail) values ('<MemberGroup>', 'Charles') ;

insert into Member (FromEmail, MemberPassWord) values ('wmiller', 'Junebgu@01!');
insert into GroupMember (GroupName, FromEmail) values ('Company', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('tech', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('CapGroup', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('Management', 'wmiller') ;

*/
/***********************************************************/
insert into  Member (FromEmail,MemberPassWord) values ("dean","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("mr","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("chongshan","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("scott","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("mj","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("Visitor1","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("Visitor2","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("Visitor3","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("Visitor4","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values ("Visitor5","Welcome1!") ;
insert into  Member (FromEmail,MemberPassWord) values (wmiller,"Welcome1!") ;


insert into RunTimeParm (ParmName, ParmVal) values ('global_FileExpirationDays', '7');
insert into RunTimeParm (ParmName, ParmVal) values ('global_UserFileExpirationDays', '3');
insert into RunTimeParm (ParmName, ParmVal) values ('global_UserDownloadExpirationDays', '1');

insert into Groups (GroupName) value ('Company') ;
insert into Groups (GroupName) value ('Tech') ;
insert into Groups (GroupName) value ('China') ;
insert into Groups (GroupName) value ('US') ;
insert into Groups (GroupName) value ('Management') ;
insert into Groups (GroupName) value ('HKG Project') ;
insert into Groups (GroupName) value ('CapGroup') ;

insert into Groups (GroupName) value ('Maxwell') ;
insert into GroupMember (GroupName, FromEmail) values ('Maxwell', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('Maxwell', 'wmiller') ;

insert into GroupMember (GroupName, FromEmail) values ('CapGroup', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('CapGroup', 'MR') ;
insert into GroupMember (GroupName, FromEmail) values ('CapGroup', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('CapGroup', 'MJ') ;

insert into GroupMember (GroupName, FromEmail) values ('Company', 'Dale') ;
insert into GroupMember (GroupName, FromEmail) values ('Company', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('Company', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('Company', 'MR') ;
insert into GroupMember (GroupName, FromEmail) values ('Company', 'MJ') ;
insert into GroupMember (GroupName, FromEmail) values ('Company', 'EDS') ;

insert into GroupMember (GroupName, FromEmail) values ('Tech', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('Tech', 'Dale') ;
insert into GroupMember (GroupName, FromEmail) values ('Tech', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('Tech', 'MJ') ;

insert into GroupMember (GroupName, FromEmail) values ('Management', 'wmiller') ;
insert into GroupMember (GroupName, FromEmail) values ('Management', 'Dale') ;
insert into GroupMember (GroupName, FromEmail) values ('Management', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('Management', 'MR') ;
insert into GroupMember (GroupName, FromEmail) values ('Management', 'MJ') ;

insert into GroupMember (GroupName, FromEmail) values ('China', 'Dale') ;
insert into GroupMember (GroupName, FromEmail) values ('China', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('China', 'MR') ;
insert into GroupMember (GroupName, FromEmail) values ('China', 'MJ') ;
insert into GroupMember (GroupName, FromEmail) values ('China', 'EDS') ;


insert into GroupMember (GroupName, FromEmail) values ('US', 'Dale') ;
insert into GroupMember (GroupName, FromEmail) values ('US', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('US', 'MR') ;
insert into GroupMember (GroupName, FromEmail) values ('US', 'EDS') ;


insert into GroupMember (GroupName, FromEmail) values ('HKG Project', 'Dale') ;
insert into GroupMember (GroupName, FromEmail) values ('HKG Project', 'Dean') ;
insert into GroupMember (GroupName, FromEmail) values ('HKG Project', 'MR') ;
insert into GroupMember (GroupName, FromEmail) values ('HKG Project', 'MJ') ;
insert into GroupMember (GroupName, FromEmail) values ('HKG Project', 'EDS') ;

insert into  Member (FromEmail,MemberPassWord, MemberPassWordHash, JoinDate,ExpireDate,RenewDate,HashMemberEmail,IV,SecretKey ) 
	values ("wmiller","Junebug@01", "XXX", NOW(), NOW()+80, NOW()+90, "XXX", "XXX", "XXX");
insert into  Member (FromEmail,MemberPassWord) values ("dale","Junebug@01") ;

