
-- 
drop trigger audit_update;

DELIMITER $$

CREATE TRIGGER audit_update  
	AFTER INSERT ON kolab_folders  
FOR EACH ROW  
BEGIN  
	
    call proc_audit_update;
    
END $$

DELIMITER ;



-- 
drop procedure proc_audit_update;

DELIMITER $$

CREATE procedure proc_audit_update  ()
BEGIN  

	DECLARE no_more_data INTEGER DEFAULT 0;
	DECLARE change_dt datetime;
    DECLARE UID varchar(50);
	DEClARE audit_cursor CURSOR FOR 
		select substring(substring_index(resource,'%',1),POSITION("//" IN resource)+2 ) , changed from kolab_folders;
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
			insert into audit (username, dt, app, svr) values (UID, change_dt, 'k3email','k3mail');
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