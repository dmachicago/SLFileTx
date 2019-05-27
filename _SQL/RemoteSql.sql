use k3;
select * from Member;
select * from information_schema.tables where table_schema = 'k3' ;
select * from information_schema.columns where table_schema = 'k3' and table_name = 'Member';