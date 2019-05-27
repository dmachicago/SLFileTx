select count(*) from MemberFiles;
select count(*) from UploadedFiles;
select count(*)  from FileKeys;

select * from MemberFiles;
select * from UploadedFiles;
select * from FileKeys;

SELECT User, Host, Password FROM mysql.user;
SELECT count(*) as CNT FROM Member where FromEmail = 'userid=dale' and MemberPassWord = 'pwhash=Junebug'

SET foreign_key_checks = 0;	
delete from MemberFiles;
delete from UploadedFiles;
delete from FileKeys;
SET foreign_key_checks = 1;	


SELECT count(*) as CNT FROM Member where FromEmail = 'dale' and MemberPassWord = 'Junebug@01'

SELECT FileID FROM UploadedFiles U WHERE U.segmentID = (SELECT MAX(segmentNbr) FROM UserFiles WHERE = UF.FileID = FileID)

select distinct GroupName from Groups G where Owner_FromEmail = 'wmiller'  union  select distinct Groupname from CompanyGroup order by GroupName

/* getMemberPendingFiles.php */
select UF.FileName, MF.FromEmail, MF.SentDate, MF.ExpireDate, MF.FileID, MF.bEncrypted
from MemberFiles MF 
join UploadedFiles UF 
	on UF.FileID = MF.FileID 
join (Select FileID, max(segmentNbr) from MemberFiles M group by M.FileID) MX
	on MX.FileID = MF.FileID
where ToEmail = 'dean'
and (DownloadedFlg is null or DownLoadedFlg = false) 
order by UF.FileName, MF.FromEmail, MF.SentDate, MF.ExpireDate;

select distinct UF.FileName, MF.FromEmail, max(MF.FileID) as FileID, Max(SentDate), Max(ExpireDate), Max(MF.segmentNbr)
from MemberFiles MF 
join UploadedFiles UF 
	on UF.FileID = MF.FileID 
where ToEmail = 'dean'
and (DownloadedFlg is null or DownLoadedFlg = false) 
group by UF.FileName, MF.FromEmail;

select distinct UF.FileName, MF.FromEmail, max(MF.FileID) as FileID, Max(SentDate), Max(ExpireDate), Max(MF.segmentNbr)  
from MemberFiles MF  
join UploadedFiles UF
    on UF.FileID = MF.FileID 
where ToEmail = 'dean'  
and (DownloadedFlg is null or DownLoadedFlg = false)  
group by UF.FileName, MF.FromEmail;

	
select distinct UF.FileName, MF.ToEmail, MF.FromEmail, max(MF.SentDate), max(MF.ExpireDate), max(MF.FileID), directory
from MemberFiles MF 
join UploadedFiles UF 
 	on UF.FileID = MF.FileID 
where ToEmail = 'dale'
and (DownloadedFlg is null or DownLoadedFlg = false)
group by MF.FromEmail, MF.ToEmail, UF.FileName, directory;

/*
Update MemberFiles set DownloadedFlg = 0, SentDate = now(), ExpireDate = ADDDATE(now(), 14) 
where DownloadedFlg is null 
and FromEmail = 'dale' ;
*/

