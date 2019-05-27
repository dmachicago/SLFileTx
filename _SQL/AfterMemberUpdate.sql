GRANT ALL PRIVILEGES ON dbname.* TO 'wmiller'@'45.32.129.86' IDENTIFIED BY 'Lottieb@01';



drop trigger AfterMemberUpdate;

DELIMITER //
CREATE TRIGGER AfterMemberUpdate before UPDATE ON Member
FOR EACH ROW
BEGIN
		set NEW.MemberPassWordHash = SHA1(NEW.MemberPassWord); 
		set NEW.PWExpireDate = ADDDATE(NOW(), 90); 

	END;//
DELIMITER ;

select FromEmail, MemberPassWord, MemberPassWordHash from Member where FromEmail = 'wmiller';
select FromEmail, MemberPassWord, MemberPassWordHash,PWExpireDate from Member where FromEmail = 'wmiller';

update Member set MemberPassWord = 'Junebug@01' where FromEmail = 'wmiller';

select ExpireDate, PWExpire , FromEmail from Member where FromEmail = 'wmiller';