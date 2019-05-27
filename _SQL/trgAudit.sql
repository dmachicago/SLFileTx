use k3;
select * from audit order by CreateDate desc ;
/* 
   select * from audit ;
   DROP TRIGGER trgAudit;
   drop table `audit`  ; 
   drop TRIGGER trgBeforeSessionDelete 
*/
CREATE TABLE `audit` (
  `SessionID` varchar(75) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EndDate` datetime NULL ,
  `EmailAddr` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`SessionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* DROP TRIGGER trgAudit */

DELIMITER //

CREATE TRIGGER trgAudit
AFTER INSERT
   ON SessionKey FOR EACH ROW
BEGIN

   INSERT INTO audit
   ( EmailAddr,
     CreateDate,
     SessionID)
   VALUES
   ( NEW.EmailAddr,
     SYSDATE(),
     NEW.SessionID );

END; //

DELIMITER ;


DELIMITER //

CREATE TRIGGER trgBeforeSessionDelete
BEFORE DELETE
   ON SessionKey FOR EACH ROW

BEGIN
   DECLARE vUser varchar(50);
-- Insert record into audit table
   update audit set EndDate = SYSDATE() where SessionID = OLD.SessionID;
END; //

DELIMITER ;