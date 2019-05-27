 
-- ************************************************************************
-- MatterMost
select distinct U.Username, from_unixtime(floor(S.CreateAt/1000)) from Posts S join Users U on U.Id = S.UserId  group by U.Username,S.CreateAt;
select distinct U.Username, from_unixtime(floor(S.CreateAt/1000)) from Audits S join Users U on U.Id = S.UserId  group by U.Username,S.CreateAt;
select U.Username, from_unixtime(floor(S.CreateAt/1000)), from_unixtime(floor(S.LastActivityAt/1000)) from Sessions S join Users U on U.Id = S.UserId  order by U.Username,S.CreateAt;
 
 select username from users;
 select uid,created,changed,dtstart,dtend from kolab_cache_event;
 
 
 -- ************************************************************************
 -- kolab email
 
 select username, last_login from users;  
 CREATE TABLE audit(username varchar(50), dt datetime, app varchar(50), svr varchar(50));
 
 CREATE INDEX idxAudit ON audit (username, dt);

 alter table audit add app varchar(50);
 alter table audit add svr varchar(50);
 
  
-- drop trigger upd_audit_users;
delimiter //
CREATE TRIGGER upd_audit_users AFTER UPDATE ON Users
       FOR EACH ROW
       BEGIN
           SET @UID = (select UserName from Users where Id = NEW.Id);
           insert into audit (username, dt, app, svr) values (@UID , NOW(), 'k3chat', 'MMCHI-UD');
       END;//
delimiter ;

-- DROP TRIGGER upd_audit ;
delimiter //
CREATE TRIGGER upd_audit AFTER INSERT ON Audits
       FOR EACH ROW
       BEGIN
			SET @UID = (select UserName from Users where Id = NEW.Id);
           insert into audit (username, dt, app, svr) values (@UID , NOW(), 'k3chat', 'MMCHI-AUD');
       END;//
delimiter ;

-- ************************************************************************************************
select distinct U.Username, from_unixtime(floor(S.CreateAt/1000)) from Posts S join Users U on U.Id = S.UserId  group by U.Username,S.CreateAt;
-- DROP TRIGGER upd_audit ;
delimiter //
CREATE TRIGGER upd_audit_Posts AFTER INSERT ON Posts
       FOR EACH ROW
       BEGIN
			SET @UID = (select UserName from Users where Id = NEW.UserId);
           insert into audit (username, dt, app, svr) values (@UID , NOW(), 'k3chat', 'MMCHI-POST');
       END;//
delimiter ;
-- ************************************************************************************************

select * from audit order by username, last_login; 

-- *********************************************************
select U.Username, from_unixtime(floor(S.CreateAt/1000)), from_unixtime(floor(S.LastActivityAt/1000)) from Sessions S join Users U on U.Id = S.UserId  order by U.Username,S.CreateAt;

-- 
drop procedure proc_audit_update;

DELIMITER $$

CREATE procedure proc_audit_update  ()
BEGIN  

	DECLARE no_more_data INTEGER DEFAULT 0;
	DECLARE change_dt datetime;
    DECLARE UID varchar(50);
	DEClARE audit_cursor CURSOR FOR 
        select U.Username UID, from_unixtime(floor(S.CreateAt/1000)) change_dt from Sessions S join Users U on U.Id = S.UserId;
	DECLARE CONTINUE HANDLER 
        FOR NOT FOUND SET no_more_data = 1;
 
	OPEN audit_cursor;
	get_data: LOOP
        fetch audit_cursor into UID, change_dt;
		IF no_more_data = 1 THEN 
			LEAVE get_data;
		END IF;
		IF NOT EXISTS (select 1 from audit where username = UID and dt = change_dt) 
		THEN  
			insert into audit (username, dt, app, svr) values (UID, change_dt, 'k3chat','MMCHI-SESS');
		END IF;
    END LOOP get_data;
	CLOSE audit_cursor;
 
END; $$

DELIMITER ;

-- enable scheduler
nano /etc/mysql/my.cnf
[mysqld]
# turning on event_scheduler  
event_scheduler=ON
SET GLOBAL event_scheduler = ON;

DELIMITER ;

-- ************************************
CREATE EVENT event_audit_update
    ON SCHEDULE EVERY 15 MINUTE
    DO
      CALL proc_audit_update();