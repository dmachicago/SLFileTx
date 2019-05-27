--
-- ER/Studio 8.0 SQL Code Generation
-- Company :      DMA, Limited
-- Project :      K3_MySql.DM1
-- Author :       wdalemiller@gmail.com
--
-- Date Created : Friday, March 17, 2017 08:40:48
-- Target DBMS : MySQL 5.x
--

--mysql -u root -p k3 < k3_ddl.sql


--*************************************************************************************************
--
-- ER/Studio 8.0 SQL Code Generation
-- Company :      DMA, Limited
-- Project :      K3_MySql.DM1
-- Author :       wdalemiller@gmail.com
--
-- Date Created : Friday, November 10, 2017 11:06:03
-- Target DBMS : MySQL 5.x
--
create database k3 ;

ALTER TABLE Attachment
DROP FOREIGN KEY RefEmail38
;

ALTER TABLE BCC
DROP FOREIGN KEY RefEmail34
;

ALTER TABLE CC
DROP FOREIGN KEY RefEmail35
;

ALTER TABLE CompanyGroup
DROP FOREIGN KEY RefMember58
;

ALTER TABLE Email
DROP FOREIGN KEY RefK359
;

ALTER TABLE Email
DROP FOREIGN KEY RefMember41
;

ALTER TABLE EmailNbr
DROP FOREIGN KEY RefEmail61
;

ALTER TABLE EmailSentCnt
DROP FOREIGN KEY RefMember56
;

ALTER TABLE FileKeys
DROP FOREIGN KEY RefMemberFiles42
;

ALTER TABLE FromEmail
DROP FOREIGN KEY RefMember54
;

ALTER TABLE FromEmail
DROP FOREIGN KEY RefMember55
;

ALTER TABLE FromEmail
DROP FOREIGN KEY RefEmail57
;

ALTER TABLE GeoLoc
DROP FOREIGN KEY RefMachine63
;

ALTER TABLE GroupMember
DROP FOREIGN KEY RefGroups50
;

ALTER TABLE GroupMember
DROP FOREIGN KEY RefMember53
;

ALTER TABLE Machine
DROP FOREIGN KEY RefMember62
;

ALTER TABLE MemberFiles
DROP FOREIGN KEY RefMember40
;

ALTER TABLE MemberFiles
DROP FOREIGN KEY RefUploadedFiles39
;

ALTER TABLE MemberFiles
DROP FOREIGN KEY ToEmail
;

ALTER TABLE MemberParm
DROP FOREIGN KEY RefMember46
;

ALTER TABLE MemberParm
DROP FOREIGN KEY RefSysParm47
;

ALTER TABLE SendTO
DROP FOREIGN KEY RefEmail36
;

ALTER TABLE SessionKey
DROP FOREIGN KEY ref_SessionKey_EmailAddr
;

DROP TABLE Attachment
;
DROP TABLE BCC
;
DROP TABLE CC
;
DROP TABLE CompanyGroup
;
DROP TABLE dblog
;
DROP TABLE Email
;
DROP TABLE EmailNbr
;
DROP TABLE EmailSentCnt
;
DROP TABLE enckey
;
DROP TABLE FileKeys
;
DROP TABLE FromEmail
;
DROP TABLE GeoLoc
;
DROP TABLE GroupMember
;
DROP TABLE Groups
;
DROP TABLE K3
;
DROP TABLE Machine
;
DROP TABLE Member
;
DROP TABLE MemberFiles
;
DROP TABLE MemberParm
;
DROP TABLE PgmTrace
;
DROP TABLE RunTimeParm
;
DROP TABLE SendTO
;
DROP TABLE SessionKey
;
DROP TABLE SysParm
;
DROP TABLE ToEmail
;
DROP TABLE Tracking
;
DROP TABLE UploadedFiles
;
-- 
-- TABLE: Attachment 
--

CREATE TABLE Attachment(
    FileName            NATIONAL VARCHAR(250),
    FileContents        NATIONAL VARCHAR(4000),
    CreateDate          DATETIME                  NOT NULL,
    AttachmentID        INT                       AUTO_INCREMENT,
    FileSize            INT,
    Processed           BIT(1)                    NOT NULL,
    HashFileContents    NATIONAL VARCHAR(75),
    bAttachedToEmail    BIT(1)                    NOT NULL,
    SegmentID           INT                       NOT NULL,
    EmailGuid           NATIONAL VARCHAR(75)      NOT NULL,
    EmailNbr            INT                       NOT NULL,
    RealFileName        NATIONAL VARCHAR(100)     NOT NULL,
    SID                 INT                       NOT NULL,
    PRIMARY KEY (AttachmentID)
)ENGINE=INNODB
;



-- 
-- TABLE: BCC 
--

CREATE TABLE BCC(
    FromAddr      NATIONAL VARCHAR(80)    NOT NULL,
    ToAddr        NATIONAL VARCHAR(80)    NOT NULL,
    CreateDate    DATETIME                NOT NULL,
    Processed     BIT(1)                  NOT NULL,
    TblCode       INT                     NOT NULL,
    ExpireDate    DATETIME                NOT NULL,
    EmailGuid     NATIONAL VARCHAR(75)    NOT NULL,
    EmailNbr      INT                     NOT NULL,
    SID           INT                     NOT NULL,
    PRIMARY KEY (EmailGuid, EmailNbr, ToAddr, SID)
)ENGINE=INNODB
;



-- 
-- TABLE: CC 
--

CREATE TABLE CC(
    FromAddr      NATIONAL VARCHAR(80)    NOT NULL,
    ToAddr        NATIONAL VARCHAR(80)    NOT NULL,
    CreateDate    DATETIME                DEFAULT NOW(),
    Processed     BIT(1)                  DEFAULT 0,
    TblCode       INT                     DEFAULT 2,
    ExpireDate    DATETIME                DEFAULT NOW() NOT NULL,
    EmailGuid     NATIONAL VARCHAR(75)    NOT NULL,
    EmailNbr      INT                     NOT NULL,
    SID           INT                     NOT NULL,
    PRIMARY KEY (EmailGuid, EmailNbr, SID)
)ENGINE=INNODB
;



-- 
-- TABLE: CompanyGroup 
--

CREATE TABLE CompanyGroup(
    GroupName    NATIONAL VARCHAR(50)    NOT NULL,
    FromEmail    NATIONAL VARCHAR(80)    NOT NULL,
    PRIMARY KEY (GroupName, FromEmail)
)ENGINE=INNODB
;



-- 
-- TABLE: dblog 
--

CREATE TABLE dblog(
    LogDate     DATETIME         DEFAULT now(),
    logEntry    VARCHAR(4000),
    RowNbr      INT              AUTO_INCREMENT,
    PRIMARY KEY (RowNbr)
)ENGINE=MYISAM
;



-- 
-- TABLE: Email 
--

CREATE TABLE Email(
    EmailGuid             NATIONAL VARCHAR(75)      NOT NULL,
    EmailNbr              INT                       NOT NULL,
    EmailSubject          NATIONAL VARCHAR(4000),
    EmailBody             NATIONAL VARCHAR(4000),
    FromEmail             NATIONAL VARCHAR(80),
    SentDate              DATETIME                  NOT NULL,
    AddrHash              NATIONAL VARCHAR(75),
    Processed             BIT(1),
    HashSubject           NATIONAL VARCHAR(75),
    HashBody              NATIONAL VARCHAR(75),
    ToEmail               NATIONAL VARCHAR(400)     NOT NULL,
    AllEmailsProcessed    BIT(1),
    ExpireByDate          DATETIME,
    CommType              CHAR(1),
    ReqNotify             BIT(1),
    NoPrint               BIT(1),
    NoKeep                BIT(1),
    NoUnattended          BIT(1),
    SavedEmail            BIT(1),
    DownloadDate          DATETIME,
    SID                   INT                       NOT NULL,
    isNote                BOOLEAN,
    PRIMARY KEY (EmailGuid, EmailNbr, SID)
)ENGINE=INNODB
;



-- 
-- TABLE: EmailNbr 
--

CREATE TABLE EmailNbr(
    EmailNbr     INT                     NOT NULL,
    cdate        DATETIME,
    EmailGuid    NATIONAL VARCHAR(75)    NOT NULL,
    SID          INT                     NOT NULL,
    PRIMARY KEY (EmailNbr, EmailGuid, SID)
)ENGINE=INNODB
;



-- 
-- TABLE: EmailSentCnt 
--

CREATE TABLE EmailSentCnt(
    FromEmail     NATIONAL VARCHAR(80)    NOT NULL,
    MO            INT                     NOT NULL,
    DA            INT                     NOT NULL,
    YR            INT                     NOT NULL,
    SentCnt       INT                     NOT NULL,
    CreateDate    DATETIME                NOT NULL,
    PRIMARY KEY (MO, DA, YR, FromEmail)
)ENGINE=INNODB
;



-- 
-- TABLE: enckey 
--

CREATE TABLE enckey(
    FileName       VARCHAR(250)    NOT NULL,
    skey           VARCHAR(80)     NOT NULL,
    CreatedDate    DATETIME        DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (FileName)
)ENGINE=INNODB
;



-- 
-- TABLE: FileKeys 
--

CREATE TABLE FileKeys(
    RowNbr         INT                      NOT NULL,
    IV             NATIONAL VARCHAR(50)     NOT NULL,
    SecretKey      NATIONAL VARCHAR(100)    NOT NULL,
    FileName       VARCHAR(250),
    CreatedDate    DATETIME                 DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (RowNbr)
)ENGINE=INNODB
;



-- 
-- TABLE: FromEmail 
--

CREATE TABLE FromEmail(
    EmailNbr      INT                     NOT NULL,
    FromAddr      NATIONAL VARCHAR(80)    NOT NULL,
    ToAddr        NATIONAL VARCHAR(80)    NOT NULL,
    EmailGuid     NATIONAL VARCHAR(75)    NOT NULL,
    CreateDate    DATETIME                NOT NULL,
    Processed     BIT(1)                  NOT NULL,
    TblCode       INT                     NOT NULL,
    SID           INT                     NOT NULL,
    PRIMARY KEY (FromAddr, ToAddr, EmailGuid, EmailNbr, SID)
)ENGINE=INNODB
;



-- 
-- TABLE: GeoLoc 
--

CREATE TABLE GeoLoc(
    GeoID           INT                     AUTO_INCREMENT,
    ip              NATIONAL VARCHAR(50),
    country_code    NATIONAL VARCHAR(10),
    country_name    NATIONAL VARCHAR(50),
    region_code     NATIONAL VARCHAR(10),
    region_name     NATIONAL VARCHAR(50),
    city            NATIONAL VARCHAR(50),
    zip_code        NATIONAL VARCHAR(50),
    time_zone       NATIONAL VARCHAR(50),
    latitude        NATIONAL VARCHAR(50),
    longitude       NATIONAL VARCHAR(50),
    metro_code      NATIONAL VARCHAR(10),
    MachineID       NATIONAL VARCHAR(50)    NOT NULL,
    FromEmail       NATIONAL VARCHAR(80)    NOT NULL,
    PRIMARY KEY (GeoID, MachineID, FromEmail)
)ENGINE=INNODB
;



-- 
-- TABLE: GroupMember 
--

CREATE TABLE GroupMember(
    GroupName    NATIONAL VARCHAR(50)    NOT NULL,
    FromEmail    NATIONAL VARCHAR(80)    NOT NULL,
    PRIMARY KEY (GroupName, FromEmail)
)ENGINE=INNODB
;



-- 
-- TABLE: Groups 
--

CREATE TABLE Groups(
    GroupName    NATIONAL VARCHAR(50)    NOT NULL,
    PRIMARY KEY (GroupName)
)ENGINE=INNODB
;



-- 
-- TABLE: K3 
--

CREATE TABLE K3(
    SID          INT                       AUTO_INCREMENT,
    K1           BINARY(16)                DEFAULT newid() NOT NULL,
    K2           BINARY(16)                DEFAULT newid() NOT NULL,
    K3           BINARY(16)                DEFAULT newid() NOT NULL,
    K4           BINARY(16)                DEFAULT newid() NOT NULL,
    K5           BINARY(16)                DEFAULT newid() NOT NULL,
    K6           BINARY(16)                DEFAULT newid() NOT NULL,
    K7           BINARY(16)                DEFAULT newid() NOT NULL,
    K8           BINARY(16)                DEFAULT newid() NOT NULL,
    K9           BINARY(16)                DEFAULT newid() NOT NULL,
    K10          BINARY(16)                DEFAULT newid() NOT NULL,
    IssueDate    DATETIME                  DEFAULT NOW() NOT NULL,
    K11          BINARY(16)                DEFAULT newid() NOT NULL,
    K12          BINARY(16)                DEFAULT newid() NOT NULL,
    K13          BINARY(16)                DEFAULT newid() NOT NULL,
    K14          BINARY(16)                DEFAULT newid() NOT NULL,
    K15          BINARY(16)                DEFAULT newid() NOT NULL,
    K16          BINARY(16)                DEFAULT newid() NOT NULL,
    K17          BINARY(16)                DEFAULT newid() NOT NULL,
    K18          BINARY(16)                DEFAULT newid() NOT NULL,
    K19          BINARY(16)                DEFAULT newid() NOT NULL,
    K20          BINARY(16)                DEFAULT newid() NOT NULL,
    K21          BINARY(16)                DEFAULT newid() NOT NULL,
    K22          BINARY(16)                DEFAULT newid() NOT NULL,
    K23          BINARY(16)                DEFAULT newid() NOT NULL,
    K24          BINARY(16)                DEFAULT newid() NOT NULL,
    K25          BINARY(16)                DEFAULT newid() NOT NULL,
    K26          BINARY(16)                DEFAULT newid() NOT NULL,
    K27          BINARY(16)                DEFAULT newid() NOT NULL,
    K28          BINARY(16)                DEFAULT newid() NOT NULL,
    K29          BINARY(16)                DEFAULT newid() NOT NULL,
    K30          BINARY(16)                DEFAULT newid() NOT NULL,
    K31          BINARY(16)                DEFAULT newid() NOT NULL,
    K32          BINARY(16)                DEFAULT newid() NOT NULL,
    K33          BINARY(16)                DEFAULT newid() NOT NULL,
    K34          BINARY(16)                DEFAULT newid() NOT NULL,
    K35          BINARY(16)                DEFAULT newid() NOT NULL,
    K36          BINARY(16)                DEFAULT newid() NOT NULL,
    K37          BINARY(16)                DEFAULT newid() NOT NULL,
    K38          BINARY(16)                DEFAULT newid() NOT NULL,
    K39          BINARY(16)                DEFAULT newid() NOT NULL,
    K40          BINARY(16)                DEFAULT newid() NOT NULL,
    KID          BINARY(16)                DEFAULT newid() NOT NULL,
    keyseq       NATIONAL VARCHAR(4000),
    seed         NATIONAL VARCHAR(4000),
    PRIMARY KEY (SID)
)ENGINE=INNODB
;



-- 
-- TABLE: Machine 
--

CREATE TABLE Machine(
    MachineID    NATIONAL VARCHAR(50)    NOT NULL,
    FromEmail    NATIONAL VARCHAR(80)    NOT NULL,
    PRIMARY KEY (MachineID, FromEmail)
)ENGINE=INNODB
;



-- 
-- TABLE: Member 
--

CREATE TABLE Member(
    FromEmail             NATIONAL VARCHAR(80)     NOT NULL,
    MemberPassWord        NATIONAL VARCHAR(50)     DEFAULT 'welcome1!' NOT NULL,
    MemberPassWordHash    NATIONAL VARCHAR(50)     NOT NULL,
    JoinDate              DATETIME                 DEFAULT now() NOT NULL,
    ExpireDate            DATETIME,
    RenewDate             DATETIME,
    HashMemberEmail       NATIONAL VARCHAR(75),
    IV                    NATIONAL VARCHAR(50),
    SecretKey             NATIONAL VARCHAR(100),
    LoginRevoked          BOOLEAN                  DEFAULT 0,
    BadLoginCnt           INT                      DEFAULT 0,
    PRIMARY KEY (FromEmail)
)ENGINE=INNODB
;

CREATE TABLE MemberFiles(
    RowNbr           INT                      AUTO_INCREMENT,
    FileID           INT,
    FromEmail        NATIONAL VARCHAR(80),
    ToEmail          NATIONAL VARCHAR(80),
    DownloadedFlg    BOOL,
    SentDate         DATETIME,
    ExpireDate       DATETIME,
    segmentNbr       INT,
    SegmentCnt       INT,
    pw               NATIONAL VARCHAR(75),
    iv               NATIONAL VARCHAR(50),
    FileHash         NATIONAL VARCHAR(50),
    FromFQN          NATIONAL VARCHAR(250),
    ToFQN            NATIONAL VARCHAR(250),
    bEncrypted       BIT(10)                  DEFAULT false,
    CreatedDate      DATETIME                 DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (RowNbr)
)ENGINE=INNODB
;



-- 
-- TABLE: MemberParm 
--

CREATE TABLE MemberParm(
    FromEmail    NATIONAL VARCHAR(80)    NOT NULL,
    ParmName     NATIONAL VARCHAR(50)    NOT NULL,
    MemberVal    NATIONAL VARCHAR(50),
    PRIMARY KEY (FromEmail, ParmName)
)ENGINE=INNODB
;



-- 
-- TABLE: PgmTrace 
--

CREATE TABLE PgmTrace(
    RowNbr           INT                       AUTO_INCREMENT,
    StmtID           NATIONAL VARCHAR(50),
    PgmName          NATIONAL VARCHAR(50),
    Stmt             NATIONAL VARCHAR(4000),
    CreateDate       DATETIME,
    RowIdentifier    BINARY(16),
    IDGUID           BINARY(16),
    LastModDate      DATETIME                  NOT NULL,
    PRIMARY KEY (RowNbr)
)ENGINE=MYISAM
;



-- 
-- TABLE: RunTimeParm 
--

CREATE TABLE RunTimeParm(
    ParmName    VARCHAR(50)    NOT NULL,
    ParmVal     VARCHAR(50),
    PRIMARY KEY (ParmName)
)ENGINE=INNODB
;



-- 
-- TABLE: SendTO 
--

CREATE TABLE SendTO(
    FromAddr      NATIONAL VARCHAR(80)    NOT NULL,
    ToAddr        NATIONAL VARCHAR(80)    NOT NULL,
    CreateDate    DATETIME                DEFAULT NOW(),
    Processed     BIT(1)                  DEFAULT 0,
    TblCode       INT                     DEFAULT 3,
    ExpireDate    DATETIME                DEFAULT NOW() NOT NULL,
    EmailGuid     NATIONAL VARCHAR(75)    NOT NULL,
    EmailNbr      INT                     NOT NULL,
    SID           INT                     NOT NULL,
    PRIMARY KEY (EmailGuid, EmailNbr, SID)
)ENGINE=INNODB
;



-- 
-- TABLE: SessionKey 
--


CREATE TABLE SessionKey(
    SessionID              NATIONAL VARCHAR(75)     NOT NULL,
    CreateDate             DATETIME                 DEFAULT NOW() NOT NULL,
    EmailAddr              NATIONAL VARCHAR(80),
    GuidID                 NATIONAL VARCHAR(50),
    IV                     NATIONAL VARCHAR(50),
    SecretKey              NATIONAL VARCHAR(100),
    LastAcquisitionDate    DATETIME                 DEFAULT NOW(),
    PRIMARY KEY (SessionID)
)ENGINE=INNODB
;



-- 
-- TABLE: SessionKey 
--

ALTER TABLE SessionKey ADD CONSTRAINT ref_SessionKey_EmailAddr 
    FOREIGN KEY (EmailAddr)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: SysParm 
--

CREATE TABLE SysParm(
    ParmName       NATIONAL VARCHAR(50)      NOT NULL,
    ParmVal        NATIONAL VARCHAR(50)      NOT NULL,
    Description    NATIONAL VARCHAR(2000),
    PRIMARY KEY (ParmName)
)ENGINE=INNODB
;

-- 
-- TABLE: ToEmail 
--

CREATE TABLE ToEmail(
    ToEmail       NATIONAL VARCHAR(80)    NOT NULL,
    EmailGuid     NATIONAL VARCHAR(50)    NOT NULL,
    CreateDate    DATETIME                DEFAULT NOW(),
    ExpireDate    DATETIME                DEFAULT NOW() NOT NULL,
    PRIMARY KEY (EmailGuid, ToEmail)
)
;



-- 
-- TABLE: Tracking 
--

CREATE TABLE Tracking(
    RowNbr        INT                     AUTO_INCREMENT,
    SystemCode    NATIONAL VARCHAR(15)    NOT NULL,
    EventID       NATIONAL VARCHAR(50)    NOT NULL,
    email         NATIONAL VARCHAR(50)    NOT NULL,
    identifier    NATIONAL VARCHAR(50)    NOT NULL,
    DT            VARCHAR(50)             NOT NULL,
    amt           NATIONAL VARCHAR(50),
    qty           NATIONAL VARCHAR(50),
    EntryDate     DATETIME                DEFAULT now(),
    PRIMARY KEY (RowNbr)
)ENGINE=MYISAM
;



-- 
-- TABLE: UploadedFiles 
--

CREATE TABLE UploadedFiles(
    FileID                  INT                      AUTO_INCREMENT,
    FileName                NATIONAL VARCHAR(254)    NOT NULL,
    segmentCount            INTEGER                  NOT NULL,
    segmentNbr              INT                      NOT NULL,
    segmentSize             INT,
    directory               NATIONAL VARCHAR(254),
    filehash                BINARY(75),
    SecureName              NATIONAL VARCHAR(50),
    commguid                NATIONAL VARCHAR(50),
    PendingDownloadCount    INT                      DEFAULT 0,
    CreatedDate             DATETIME                 DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (FileID)
)ENGINE=INNODB
;



-- 
-- INDEX: UI_Attachment 
--

CREATE UNIQUE INDEX UI_Attachment ON Attachment(AttachmentID)
;
-- 
-- INDEX: PI_BCC_ToAddr 
--

CREATE INDEX PI_BCC_ToAddr ON BCC(ToAddr)
;
-- 
-- INDEX: PK_Email 
--

CREATE UNIQUE INDEX PK_Email ON Email(EmailGuid, EmailNbr)
;
-- 
-- INDEX: PI_AddrHash 
--

CREATE INDEX PI_AddrHash ON Email(AddrHash)
;
-- 
-- INDEX: PI_EmailGuid 
--

CREATE INDEX PI_EmailGuid ON Email(EmailGuid)
;
-- 
-- INDEX: PI_ToAddr 
--

CREATE INDEX PI_ToAddr ON Email(ToEmail, Processed)
;
-- 
-- INDEX: idx_IP 
--

CREATE INDEX idx_IP ON GeoLoc(ip)
;
-- 
-- INDEX: PK_GeoLoc 
--

CREATE UNIQUE INDEX PK_GeoLoc ON GeoLoc(ip, country_code, city, latitude, longitude)
;
-- 
-- INDEX: PK_K3 
--

CREATE UNIQUE INDEX PK_K3 ON K3(SID)
;
-- 
-- INDEX: PI_ExpireK3 
--

CREATE INDEX PI_ExpireK3 ON K3(IssueDate)
;
-- 
-- INDEX: UIPI_K3 
--

CREATE UNIQUE INDEX UIPI_K3 ON K3(KID)
;
-- 
-- INDEX: idxMemberPwHash 
--

CREATE INDEX idxMemberPwHash ON Member(MemberPassWordHash)
;
-- 
-- INDEX: FileHash_idx 
--

CREATE INDEX FileHash_idx ON MemberFiles(FileHash)
;
-- 
-- INDEX: PI_MemberFiles_ToEmail 
--

CREATE UNIQUE INDEX PI_MemberFiles_ToEmail ON MemberFiles(ToEmail, segmentNbr, FileID)
;
-- 
-- INDEX: IDX_UF_SecureName 
--

CREATE UNIQUE INDEX IDX_UF_SecureName ON UploadedFiles(SecureName)
;
-- 
-- INDEX: idxUploadFile_FN 
--

CREATE UNIQUE INDEX idxUploadFile_FN ON UploadedFiles(FileName)
;
-- 
-- TABLE: Attachment 
--

ALTER TABLE Attachment ADD CONSTRAINT RefEmail381 
    FOREIGN KEY (EmailGuid, EmailNbr, SID)
    REFERENCES Email(EmailGuid, EmailNbr, SID)
;


-- 
-- TABLE: BCC 
--

ALTER TABLE BCC ADD CONSTRAINT RefEmail341 
    FOREIGN KEY (EmailGuid, EmailNbr, SID)
    REFERENCES Email(EmailGuid, EmailNbr, SID)
;


-- 
-- TABLE: CC 
--

ALTER TABLE CC ADD CONSTRAINT RefEmail351 
    FOREIGN KEY (EmailGuid, EmailNbr, SID)
    REFERENCES Email(EmailGuid, EmailNbr, SID)
;


-- 
-- TABLE: CompanyGroup 
--

ALTER TABLE CompanyGroup ADD CONSTRAINT RefMember581 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: Email 
--

ALTER TABLE Email ADD CONSTRAINT RefK3591 
    FOREIGN KEY (SID)
    REFERENCES K3(SID)
;

ALTER TABLE Email ADD CONSTRAINT RefMember411 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: EmailNbr 
--

ALTER TABLE EmailNbr ADD CONSTRAINT RefEmail611 
    FOREIGN KEY (EmailNbr, EmailGuid, SID)
    REFERENCES Email(EmailGuid, EmailNbr, SID)
;


-- 
-- TABLE: EmailSentCnt 
--

ALTER TABLE EmailSentCnt ADD CONSTRAINT RefMember561 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: FileKeys 
--

ALTER TABLE FileKeys ADD CONSTRAINT RefMemberFiles421 
    FOREIGN KEY (RowNbr)
    REFERENCES MemberFiles(RowNbr)
;


-- 
-- TABLE: FromEmail 
--

ALTER TABLE FromEmail ADD CONSTRAINT RefMember541 
    FOREIGN KEY (FromAddr)
    REFERENCES Member(FromEmail)
;

ALTER TABLE FromEmail ADD CONSTRAINT RefMember551 
    FOREIGN KEY (ToAddr)
    REFERENCES Member(FromEmail)
;

ALTER TABLE FromEmail ADD CONSTRAINT RefEmail571 
    FOREIGN KEY (EmailNbr, EmailGuid, SID)
    REFERENCES Email(EmailGuid, EmailNbr, SID)
;


-- 
-- TABLE: GeoLoc 
--

ALTER TABLE GeoLoc ADD CONSTRAINT RefMachine631 
    FOREIGN KEY (MachineID, FromEmail)
    REFERENCES Machine(MachineID, FromEmail)
;


-- 
-- TABLE: GroupMember 
--

ALTER TABLE GroupMember ADD CONSTRAINT RefGroups501 
    FOREIGN KEY (GroupName)
    REFERENCES Groups(GroupName)
;

ALTER TABLE GroupMember ADD CONSTRAINT RefMember531 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: Machine 
--

ALTER TABLE Machine ADD CONSTRAINT RefMember621 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: MemberFiles 
--

ALTER TABLE MemberFiles ADD CONSTRAINT RefMember401 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;

ALTER TABLE MemberFiles ADD CONSTRAINT RefUploadedFiles391 
    FOREIGN KEY (FileID)
    REFERENCES UploadedFiles(FileID)
;

ALTER TABLE MemberFiles ADD CONSTRAINT ToEmail 
    FOREIGN KEY (ToEmail)
    REFERENCES Member(FromEmail)
;


-- 
-- TABLE: MemberParm 
--

ALTER TABLE MemberParm ADD CONSTRAINT RefMember461 
    FOREIGN KEY (FromEmail)
    REFERENCES Member(FromEmail)
;

ALTER TABLE MemberParm ADD CONSTRAINT RefSysParm471 
    FOREIGN KEY (ParmName)
    REFERENCES SysParm(ParmName)
;


-- 
-- TABLE: SendTO 
--

ALTER TABLE SendTO ADD CONSTRAINT RefEmail361 
    FOREIGN KEY (EmailGuid, EmailNbr, SID)
    REFERENCES Email(EmailGuid, EmailNbr, SID)
;

--*************************************************************************************************

SET foreign_key_checks = 0;

drop trigger if exists SessionKeyGuidID;
DELIMITER //
CREATE TRIGGER SessionKeyGuidID
    BEFORE insert ON SessionKey
    for each row
    begin
	   SET NEW.CreateDate = NOW();
        SET NEW.SessionID = uuid();
    end;//

DELIMITER ;
drop trigger if exists before_insert_Member;
DELIMITER //
CREATE TRIGGER before_insert_Member
  BEFORE INSERT ON Member
  FOR EACH ROW
  begin
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
        RESIGNAL;
    DECLARE EXIT HANDLER FOR SQLWARNING
        RESIGNAL;
    DECLARE EXIT HANDLER FOR NOT FOUND
        RESIGNAL; 
	SET new.MemberPassWordHash = SHA1(new.MemberPassWord);
	SET new.HashMemberEmail = SHA1(new.FromEmail);
	SET new.JoinDate = NOW();	
end; //

 
DELIMITER ;
drop trigger if exists before_insert_Member;
DELIMITER //
CREATE TRIGGER before_insert_Member
  BEFORE INSERT ON Member	
   	FOR EACH ROW
	begin
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
        RESIGNAL;
    DECLARE EXIT HANDLER FOR SQLWARNING
        RESIGNAL;
    DECLARE EXIT HANDLER FOR NOT FOUND
        RESIGNAL; 
		SET new.MemberPassWordHash = SHA1(new.MemberPassWord);
		SET new.HashMemberEmail = SHA1(new.FromEmail);
		SET new.IV = SHA1(uuid());
		SET new.SecretKey = SHA1(uuid());
		SET new.JoinDate = NOW();
		SET new.ExpireDate = NOW()+INTERVAL 90 DAY;
		SET new.RenewDate = NOW()+INTERVAL 80 DAY;
	end; //


/****************************************************/
/* drop PROCEDURE ckFileExpirationDays */
DELIMITER ;
drop PROCEDURE if exists ckFileExpirationDays;

DELIMITER //
CREATE PROCEDURE ckFileExpirationDays
(IN con CHAR(20))
BEGIN
	/*SELECT @days := `Parmval` FROM RunTimeParm WHERE ParmName = 'global_FileExpirationDays'; 
	Delete from UploadedFiles WHERE CreatedDate < NOW() - INTERVAL @days DAY;
	Delete from MemberFiles WHERE CreatedDate < NOW() - INTERVAL @days DAY;
	Delete from FileKeys WHERE CreatedDate < NOW() - INTERVAL @days DAY;
	Delete from enckey WHERE CreatedDate < NOW() - INTERVAL @days DAY; */
END //


DELIMITER //
CREATE EVENT `evtFileExpirationDays`
  ON SCHEDULE EVERY 4 HOUR
  STARTS NOW()
  ENDS '2020-01-01 00:00:00'
  ON COMPLETION NOT PRESERVE ENABLE
DO 
    call ckFileExpirationDays(); //
/****************************************************/

DELIMITER ;