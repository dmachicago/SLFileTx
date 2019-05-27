-- MySQL dump 10.13  Distrib 5.7.18, for Linux (x86_64)
--
-- Host: localhost    Database: k3
-- ------------------------------------------------------
-- Server version	5.7.18-0ubuntu0.16.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `k3`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `k3` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `k3`;

--
-- Table structure for table `Attachment`
--

DROP TABLE IF EXISTS `Attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attachment` (
  `FileName` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `FileContents` varchar(4000) CHARACTER SET utf8 DEFAULT NULL,
  `CreateDate` datetime NOT NULL,
  `AttachmentID` int(11) NOT NULL AUTO_INCREMENT,
  `FileSize` int(11) DEFAULT NULL,
  `Processed` bit(1) NOT NULL,
  `HashFileContents` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `bAttachedToEmail` bit(1) NOT NULL,
  `SegmentID` int(11) NOT NULL,
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `EmailNbr` int(11) NOT NULL,
  `RealFileName` varchar(100) CHARACTER SET utf8 NOT NULL,
  `SID` int(11) NOT NULL,
  PRIMARY KEY (`AttachmentID`),
  UNIQUE KEY `UI_Attachment` (`AttachmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attachment`
--

LOCK TABLES `Attachment` WRITE;
/*!40000 ALTER TABLE `Attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BCC`
--

DROP TABLE IF EXISTS `BCC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BCC` (
  `FromAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `ToAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime NOT NULL,
  `Processed` bit(1) NOT NULL,
  `TblCode` int(11) NOT NULL,
  `ExpireDate` datetime NOT NULL,
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `EmailNbr` int(11) NOT NULL,
  `SID` int(11) NOT NULL,
  PRIMARY KEY (`EmailGuid`,`EmailNbr`,`ToAddr`,`SID`),
  KEY `PI_BCC_ToAddr` (`ToAddr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BCC`
--

LOCK TABLES `BCC` WRITE;
/*!40000 ALTER TABLE `BCC` DISABLE KEYS */;
/*!40000 ALTER TABLE `BCC` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CC`
--

DROP TABLE IF EXISTS `CC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CC` (
  `FromAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `ToAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Processed` bit(1) DEFAULT b'0',
  `TblCode` int(11) DEFAULT '2',
  `ExpireDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `EmailNbr` int(11) NOT NULL,
  `SID` int(11) NOT NULL,
  PRIMARY KEY (`EmailGuid`,`EmailNbr`,`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CC`
--

LOCK TABLES `CC` WRITE;
/*!40000 ALTER TABLE `CC` DISABLE KEYS */;
/*!40000 ALTER TABLE `CC` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CompanyGroup`
--

DROP TABLE IF EXISTS `CompanyGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CompanyGroup` (
  `GroupName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`GroupName`,`FromEmail`),
  KEY `RefMember581` (`FromEmail`),
  CONSTRAINT `RefMember581` FOREIGN KEY (`FromEmail`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CompanyGroup`
--

LOCK TABLES `CompanyGroup` WRITE;
/*!40000 ALTER TABLE `CompanyGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `CompanyGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Email`
--

DROP TABLE IF EXISTS `Email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Email` (
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `EmailNbr` int(11) NOT NULL,
  `EmailSubject` varchar(4000) CHARACTER SET utf8 DEFAULT NULL,
  `EmailBody` varchar(4000) CHARACTER SET utf8 DEFAULT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `SentDate` datetime NOT NULL,
  `AddrHash` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `Processed` bit(1) DEFAULT NULL,
  `HashSubject` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `HashBody` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `ToEmail` varchar(400) CHARACTER SET utf8 NOT NULL,
  `AllEmailsProcessed` bit(1) DEFAULT NULL,
  `ExpireByDate` datetime DEFAULT NULL,
  `CommType` char(1) DEFAULT NULL,
  `ReqNotify` bit(1) DEFAULT NULL,
  `NoPrint` bit(1) DEFAULT NULL,
  `NoKeep` bit(1) DEFAULT NULL,
  `NoUnattended` bit(1) DEFAULT NULL,
  `SavedEmail` bit(1) DEFAULT NULL,
  `DownloadDate` datetime DEFAULT NULL,
  `SID` int(11) NOT NULL,
  `isNote` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`EmailGuid`,`EmailNbr`,`SID`),
  UNIQUE KEY `PK_Email` (`EmailGuid`,`EmailNbr`),
  KEY `PI_AddrHash` (`AddrHash`),
  KEY `PI_EmailGuid` (`EmailGuid`),
  KEY `PI_ToAddr` (`ToEmail`,`Processed`),
  KEY `RefK3591` (`SID`),
  KEY `RefMember411` (`FromEmail`),
  CONSTRAINT `RefMember411` FOREIGN KEY (`FromEmail`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Email`
--

LOCK TABLES `Email` WRITE;
/*!40000 ALTER TABLE `Email` DISABLE KEYS */;
/*!40000 ALTER TABLE `Email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EmailNbr`
--

DROP TABLE IF EXISTS `EmailNbr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EmailNbr` (
  `EmailNbr` int(11) NOT NULL,
  `cdate` datetime DEFAULT NULL,
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `SID` int(11) NOT NULL,
  PRIMARY KEY (`EmailNbr`,`EmailGuid`,`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `EmailNbr`
--

LOCK TABLES `EmailNbr` WRITE;
/*!40000 ALTER TABLE `EmailNbr` DISABLE KEYS */;
/*!40000 ALTER TABLE `EmailNbr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EmailSentCnt`
--

DROP TABLE IF EXISTS `EmailSentCnt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EmailSentCnt` (
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  `MO` int(11) NOT NULL,
  `DA` int(11) NOT NULL,
  `YR` int(11) NOT NULL,
  `SentCnt` int(11) NOT NULL,
  `CreateDate` datetime NOT NULL,
  PRIMARY KEY (`MO`,`DA`,`YR`,`FromEmail`),
  KEY `RefMember561` (`FromEmail`),
  CONSTRAINT `RefMember561` FOREIGN KEY (`FromEmail`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `EmailSentCnt`
--

LOCK TABLES `EmailSentCnt` WRITE;
/*!40000 ALTER TABLE `EmailSentCnt` DISABLE KEYS */;
/*!40000 ALTER TABLE `EmailSentCnt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FileKeys`
--

DROP TABLE IF EXISTS `FileKeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileKeys` (
  `RowNbr` int(11) NOT NULL,
  `IV` varchar(50) CHARACTER SET utf8 NOT NULL,
  `SecretKey` varchar(100) CHARACTER SET utf8 NOT NULL,
  `FileName` varchar(250) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RowNbr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `FileKeys`
--

LOCK TABLES `FileKeys` WRITE;
/*!40000 ALTER TABLE `FileKeys` DISABLE KEYS */;
INSERT INTO `FileKeys` VALUES (6,'a01990c-4b20-4e3a-ab00-15','961cef80-3f19-48a4-b133-149353d64953','Illisa.jpeg','2017-11-15 22:54:57'),(7,'e8f77b7-e718-40fd-8aec-e7','bde21c93-ff15-43a4-9642-dae90782b972','Lana.jpeg','2017-11-15 22:54:58'),(8,'8bb1e5f-8add-49e5-b1b7-a9','82c31719-0df0-4083-8e64-3a4d1512a0e1','Katarina.jpeg','2017-11-15 22:55:09');
/*!40000 ALTER TABLE `FileKeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FromEmail`
--

DROP TABLE IF EXISTS `FromEmail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FromEmail` (
  `EmailNbr` int(11) NOT NULL,
  `FromAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `ToAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime NOT NULL,
  `Processed` bit(1) NOT NULL,
  `TblCode` int(11) NOT NULL,
  `SID` int(11) NOT NULL,
  PRIMARY KEY (`FromAddr`,`ToAddr`,`EmailGuid`,`EmailNbr`,`SID`),
  KEY `RefMember551` (`ToAddr`),
  CONSTRAINT `RefMember541` FOREIGN KEY (`FromAddr`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE,
  CONSTRAINT `RefMember551` FOREIGN KEY (`ToAddr`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `FromEmail`
--

LOCK TABLES `FromEmail` WRITE;
/*!40000 ALTER TABLE `FromEmail` DISABLE KEYS */;
/*!40000 ALTER TABLE `FromEmail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GeoLoc`
--

DROP TABLE IF EXISTS `GeoLoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GeoLoc` (
  `GeoID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `country_code` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `country_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `region_code` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `region_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `zip_code` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `time_zone` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `longitude` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `metro_code` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `MachineID` varchar(50) CHARACTER SET utf8 NOT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`GeoID`,`MachineID`,`FromEmail`),
  UNIQUE KEY `PK_GeoLoc` (`ip`,`country_code`,`city`,`latitude`,`longitude`),
  KEY `idx_IP` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GeoLoc`
--

LOCK TABLES `GeoLoc` WRITE;
/*!40000 ALTER TABLE `GeoLoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `GeoLoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GroupMember`
--

DROP TABLE IF EXISTS `GroupMember`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GroupMember` (
  `GroupName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`GroupName`,`FromEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GroupMember`
--

LOCK TABLES `GroupMember` WRITE;
/*!40000 ALTER TABLE `GroupMember` DISABLE KEYS */;
INSERT INTO `GroupMember` VALUES ('CapGroup','Dean'),('CapGroup','MJ'),('CapGroup','MR'),('CapGroup','WDM'),('CapGroup','wmiller'),('China','Dale'),('China','DALEM'),('China','Dean'),('China','EDS'),('China','MJ'),('China','MR'),('Company','Charles'),('Company','Dale'),('Company','DALEM'),('Company','Dean'),('Company','EDS'),('Company','MJ'),('Company','MR'),('Company','MRWILEY'),('Company','WDM'),('Company','wmiller'),('HKG Project','Dale'),('HKG Project','Dean'),('HKG Project','EDS'),('HKG Project','MJ'),('HKG Project','MR'),('Management','Dale'),('Management','Dean'),('Management','MJ'),('Management','MR'),('Management','MRWILEY'),('Management','wmiller'),('Maxwell','Dean'),('Maxwell','wmiller'),('MRWILEY','MRWILEY'),('NewMember90','A1_Group'),('Tech','Dale'),('Tech','DALEM'),('Tech','Dean'),('Tech','MJ'),('tech','wmiller'),('US','Dale'),('US','DALEM'),('US','Dean'),('US','EDS'),('US','MR');
/*!40000 ALTER TABLE `GroupMember` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Groups` (
  `GroupName` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`GroupName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Groups`
--

LOCK TABLES `Groups` WRITE;
/*!40000 ALTER TABLE `Groups` DISABLE KEYS */;
INSERT INTO `Groups` VALUES ('*NONE Selected'),('CapGroup'),('China'),('Company'),('Dean00'),('Management'),('Maxwell'),('Tech'),('US');
/*!40000 ALTER TABLE `Groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Machine`
--

DROP TABLE IF EXISTS `Machine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Machine` (
  `MachineID` varchar(50) CHARACTER SET utf8 NOT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`MachineID`,`FromEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Machine`
--

LOCK TABLES `Machine` WRITE;
/*!40000 ALTER TABLE `Machine` DISABLE KEYS */;
/*!40000 ALTER TABLE `Machine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Member`
--

DROP TABLE IF EXISTS `Member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Member` (
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  `MemberPassWord` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'welcome1!',
  `MemberPassWordHash` varchar(50) CHARACTER SET utf8 NOT NULL,
  `JoinDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ExpireDate` datetime DEFAULT NULL,
  `RenewDate` datetime DEFAULT NULL,
  `HashMemberEmail` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `IV` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `SecretKey` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `LoginRevoked` tinyint(1) DEFAULT '0',
  `BadLoginCnt` int(11) DEFAULT '0',
  `isAdmin` int(11) DEFAULT '0',
  PRIMARY KEY (`FromEmail`),
  KEY `idxMemberPwHash` (`MemberPassWordHash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Member`
--

LOCK TABLES `Member` WRITE;
/*!40000 ALTER TABLE `Member` DISABLE KEYS */;
INSERT INTO `Member` VALUES ('Charles','welcome1!','7ee73d7ca2ef77ea6c5abe99a716e2b2ff4b770d','2017-11-13 16:36:20','2018-02-11 16:36:20','2018-02-01 16:36:20','8681d2aa20f41c6c3492e6c5dec83e94134bc705','f2fdd9e77306a9d0c3cf1970f49ba328c70c3e8f','bb6e2e5858f6d1cd1fa11ecd8b99c2c571f8ec77',0,0,0),('chongshan','Welcome1!','5f80211ccb43cd491c4e2ffbbda4c7f6ba0ff604','2017-11-13 16:35:20','2018-02-11 16:35:20','2018-02-01 16:35:20','5f96de294a1add6b090d424cc6bc29cd33001415','7649362d527c2432382697a4d20b242b44f580ee','5102e0b6ef800b2eb8a5473e0361941d29aa8eaf',0,0,0),('dale','Junebug@01','2ea241cf5871ef6693b247fac1152c4499ca3a3b','2017-11-13 16:35:20','2018-02-11 16:35:20','2018-02-01 16:35:20','80b24d51bbcd1ba9a2ce6b5fe97e28c44f9ff374','7b3b5e1a280d49e0e047e6ba1e405bf07aaceeb0','89d1cb63bb33866a5b364bda1191d6269d8049f3',0,0,0),('DALEM','Welcome1!','5f80211ccb43cd491c4e2ffbbda4c7f6ba0ff604','2017-11-29 23:02:56','2018-02-27 23:02:56','2018-02-17 23:02:56','e6865fc169c5584a134c3ee030113807e31ea73c','66d8c1ff9262cf369aeb91db105380e75cc271ab','a6988260cb1678e185dbe677a154eae898cd6206',0,0,0),('dean','Copper@01','5f80211ccb43cd491c4e2ffbbda4c7f6ba0ff604','2017-11-13 16:35:20','2018-11-15 00:00:00','2017-12-15 22:53:24','ba6f564cd70a52d664c374158c4dbcf519ddf158','5ea0bc2360de47886615498e036aa1b427991ca3','bc8b36daf8ae8b0cabef12f45f7eb03568ded563',0,0,1),('mj','Welcome1!','5f80211ccb43cd491c4e2ffbbda4c7f6ba0ff604','2017-11-13 16:35:20','2018-02-11 16:35:20','2018-02-01 16:35:20','df2fa7c39d141dc5dc5589fed74180e757ed6c9a','6d9fa1b600ba74fa0639effd36bc0ad1bb146a36','85e2a6e4a53cbbcf28ad9121eb7b2246674de182',0,0,0),('mr','Welcome1!','5f80211ccb43cd491c4e2ffbbda4c7f6ba0ff604','2017-11-13 16:35:20','2018-02-11 16:35:20','2018-02-01 16:35:20','8e7be411ad89ade93d144531f3925d0bb4011004','1751e65b6f8b2ad1d2b15431e237fd6aab2306a8','bd705b88634d41992779466e6cd1c123ef7187e8',0,0,0),('MRWILEY','Welcome1!','5f80211ccb43cd491c4e2ffbbda4c7f6ba0ff604','2017-11-17 03:12:32','2018-02-15 03:12:32','2018-02-05 03:12:32','d1733cb937802fa53e2b12769b823db52b4a19a5','0ec881582c928e9a28c3860a97dd1e95d24649a5','431845475ef949e086ef61da31f0e9282097165e',0,0,0),('wmiller','Junebug@01','2ea241cf5871ef6693b247fac1152c4499ca3a3b','2017-11-13 16:35:20','2018-02-11 16:35:20','2018-02-01 16:35:20','c7ebb5e5a59124ba373f65da46526fb39a430ca5','8895c1b72eb419d55c44f4314f6ff76b3ea9964c','f19c18fc489d4603498037c7d9f7bd82c96e020f',0,0,1);
/*!40000 ALTER TABLE `Member` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER before_insert_Member
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
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `MemberFiles`
--

DROP TABLE IF EXISTS `MemberFiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MemberFiles` (
  `RowNbr` int(11) NOT NULL AUTO_INCREMENT,
  `FileID` int(11) DEFAULT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `ToEmail` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `DownloadedFlg` tinyint(1) DEFAULT NULL,
  `SentDate` datetime DEFAULT NULL,
  `ExpireDate` datetime DEFAULT NULL,
  `segmentNbr` int(11) DEFAULT NULL,
  `SegmentCnt` int(11) DEFAULT NULL,
  `pw` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `iv` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `FileHash` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `FromFQN` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `ToFQN` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `bEncrypted` bit(10) DEFAULT b'0',
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RowNbr`),
  UNIQUE KEY `PI_MemberFiles_ToEmail` (`ToEmail`,`segmentNbr`,`FileID`),
  KEY `FileHash_idx` (`FileHash`),
  KEY `RefMember401` (`FromEmail`),
  CONSTRAINT `RefMember401` FOREIGN KEY (`FromEmail`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MemberFiles`
--

LOCK TABLES `MemberFiles` WRITE;
/*!40000 ALTER TABLE `MemberFiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `MemberFiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MemberParm`
--

DROP TABLE IF EXISTS `MemberParm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MemberParm` (
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  `ParmName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `MemberVal` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`FromEmail`,`ParmName`),
  CONSTRAINT `RefMember461` FOREIGN KEY (`FromEmail`) REFERENCES `Member` (`FromEmail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MemberParm`
--

LOCK TABLES `MemberParm` WRITE;
/*!40000 ALTER TABLE `MemberParm` DISABLE KEYS */;
/*!40000 ALTER TABLE `MemberParm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PgmTrace`
--

DROP TABLE IF EXISTS `PgmTrace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PgmTrace` (
  `RowNbr` int(11) NOT NULL AUTO_INCREMENT,
  `StmtID` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `PgmName` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Stmt` varchar(4000) CHARACTER SET utf8 DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `RowIdentifier` binary(16) DEFAULT NULL,
  `IDGUID` binary(16) DEFAULT NULL,
  `LastModDate` datetime NOT NULL,
  PRIMARY KEY (`RowNbr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PgmTrace`
--

LOCK TABLES `PgmTrace` WRITE;
/*!40000 ALTER TABLE `PgmTrace` DISABLE KEYS */;
/*!40000 ALTER TABLE `PgmTrace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RunTimeParm`
--

DROP TABLE IF EXISTS `RunTimeParm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RunTimeParm` (
  `ParmName` varchar(50) NOT NULL,
  `ParmVal` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ParmName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RunTimeParm`
--

LOCK TABLES `RunTimeParm` WRITE;
/*!40000 ALTER TABLE `RunTimeParm` DISABLE KEYS */;
INSERT INTO `RunTimeParm` VALUES ('global_FileExpirationDays','7'),('global_UserDownloadExpirationDays','1'),('global_UserFileExpirationDays','3');
/*!40000 ALTER TABLE `RunTimeParm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SendTO`
--

DROP TABLE IF EXISTS `SendTO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SendTO` (
  `FromAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `ToAddr` varchar(80) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Processed` bit(1) DEFAULT b'0',
  `TblCode` int(11) DEFAULT '3',
  `ExpireDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EmailGuid` varchar(75) CHARACTER SET utf8 NOT NULL,
  `EmailNbr` int(11) NOT NULL,
  `SID` int(11) NOT NULL,
  PRIMARY KEY (`EmailGuid`,`EmailNbr`,`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SendTO`
--

LOCK TABLES `SendTO` WRITE;
/*!40000 ALTER TABLE `SendTO` DISABLE KEYS */;
/*!40000 ALTER TABLE `SendTO` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SessionKey`
--

DROP TABLE IF EXISTS `SessionKey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionKey` (
  `SessionID` varchar(75) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EmailAddr` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `GuidID` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `IV` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `SecretKey` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `LastAcquisitionDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SessionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SessionKey`
--

LOCK TABLES `SessionKey` WRITE;
/*!40000 ALTER TABLE `SessionKey` DISABLE KEYS */;
INSERT INTO `SessionKey` VALUES ('885bc0a6-d71c-11e7-864a-56000083047d','2017-12-02 04:52:00','dean','007871c6-b486-4d55-b313-d2bb13785b8e','f7e6795-c587-427','e96a709-080d-45d','2017-12-02 04:52:00'),('9de40e04-d56b-11e7-864a-56000083047d','2017-11-30 01:13:04','wmiller','4a8c3e8e-c3fe-4b52-a21a-cbfd3795634a','4b41269-bc8c-494','b62b03e-859b-4c5','2017-11-30 01:13:04');
/*!40000 ALTER TABLE `SessionKey` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER SessionKeyGuidID
    BEFORE insert ON SessionKey
    for each row
    begin
   SET NEW.CreateDate = NOW();
        SET NEW.SessionID = uuid();
    end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `SessionKeyAdmin`
--

DROP TABLE IF EXISTS `SessionKeyAdmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionKeyAdmin` (
  `SessionID` varchar(75) CHARACTER SET utf8 NOT NULL,
  `FromEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `GuidID` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `IV` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `SecretKey` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `LastAcquisitionDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SessionID`),
  KEY `RefMember65` (`FromEmail`),
  CONSTRAINT `RefMember65` FOREIGN KEY (`FromEmail`) REFERENCES `Member` (`FromEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SessionKeyAdmin`
--

LOCK TABLES `SessionKeyAdmin` WRITE;
/*!40000 ALTER TABLE `SessionKeyAdmin` DISABLE KEYS */;
INSERT INTO `SessionKeyAdmin` VALUES ('1512004384.5192','wmiller','2017-11-30 01:13:04',NULL,NULL,NULL,'2017-11-30 01:13:04'),('1512190320.467','dean','2017-12-02 04:52:00',NULL,NULL,NULL,'2017-12-02 04:52:00');
/*!40000 ALTER TABLE `SessionKeyAdmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SysParm`
--

DROP TABLE IF EXISTS `SysParm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SysParm` (
  `ParmName` varchar(50) CHARACTER SET utf8 NOT NULL,
  `ParmVal` varchar(50) CHARACTER SET utf8 NOT NULL,
  `Description` varchar(2000) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ParmName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SysParm`
--

LOCK TABLES `SysParm` WRITE;
/*!40000 ALTER TABLE `SysParm` DISABLE KEYS */;
/*!40000 ALTER TABLE `SysParm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ToEmail`
--

DROP TABLE IF EXISTS `ToEmail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ToEmail` (
  `ToEmail` varchar(80) CHARACTER SET utf8 NOT NULL,
  `EmailGuid` varchar(50) CHARACTER SET utf8 NOT NULL,
  `CreateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `ExpireDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`EmailGuid`,`ToEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ToEmail`
--

LOCK TABLES `ToEmail` WRITE;
/*!40000 ALTER TABLE `ToEmail` DISABLE KEYS */;
/*!40000 ALTER TABLE `ToEmail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tracking`
--

DROP TABLE IF EXISTS `Tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tracking` (
  `RowNbr` int(11) NOT NULL AUTO_INCREMENT,
  `SystemCode` varchar(15) CHARACTER SET utf8 NOT NULL,
  `EventID` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `identifier` varchar(50) CHARACTER SET utf8 NOT NULL,
  `DT` varchar(50) NOT NULL,
  `amt` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `qty` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `EntryDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RowNbr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Tracking`
--

LOCK TABLES `Tracking` WRITE;
/*!40000 ALTER TABLE `Tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UploadedFiles`
--

DROP TABLE IF EXISTS `UploadedFiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UploadedFiles` (
  `FileID` int(11) NOT NULL AUTO_INCREMENT,
  `FileName` varchar(254) CHARACTER SET utf8 NOT NULL,
  `segmentCount` int(11) NOT NULL,
  `segmentNbr` int(11) NOT NULL,
  `segmentSize` int(11) DEFAULT NULL,
  `directory` varchar(254) CHARACTER SET utf8 DEFAULT NULL,
  `filehash` binary(75) DEFAULT NULL,
  `SecureName` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `PendingDownloadCount` int(11) DEFAULT '0',
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `commguid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`FileID`),
  UNIQUE KEY `idxUploadFile_FN` (`FileName`),
  UNIQUE KEY `IDX_UF_SecureName` (`SecureName`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UploadedFiles`
--

LOCK TABLES `UploadedFiles` WRITE;
/*!40000 ALTER TABLE `UploadedFiles` DISABLE KEYS */;
INSERT INTO `UploadedFiles` VALUES (5,'Illisa.jpeg',1,0,6573,'/var/www/html/SLupload/uploads/','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',NULL,0,'2017-11-15 22:54:57','3561cd4d-7d1b-4f6e-b8c7-dba6df97a403'),(6,'Lana.jpeg',1,0,5998,'/var/www/html/SLupload/uploads/','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',NULL,0,'2017-11-15 22:54:58','eb96e43a-ce91-4cb9-ba8c-8de0b2cabe10'),(7,'Katarina.jpeg',1,0,74085,'/var/www/html/SLupload/uploads/','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',NULL,0,'2017-11-15 22:55:09','0bdff379-ef36-4bed-b2e4-88bc64cb5788');
/*!40000 ALTER TABLE `UploadedFiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dblog`
--

DROP TABLE IF EXISTS `dblog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dblog` (
  `LogDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `logEntry` varchar(4000) DEFAULT NULL,
  `RowNbr` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`RowNbr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dblog`
--

LOCK TABLES `dblog` WRITE;
/*!40000 ALTER TABLE `dblog` DISABLE KEYS */;
/*!40000 ALTER TABLE `dblog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enckey`
--

DROP TABLE IF EXISTS `enckey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enckey` (
  `FileName` varchar(250) NOT NULL,
  `skey` varchar(80) NOT NULL,
  `CreatedDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FileName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enckey`
--

LOCK TABLES `enckey` WRITE;
/*!40000 ALTER TABLE `enckey` DISABLE KEYS */;
INSERT INTO `enckey` VALUES ('20160530_141544.jpg','54c198005f8e720394a29a21ec085530859c94949f1aa0deff6106be7fa1654f','2017-11-15 12:48:01'),('AndreaLegs.jpeg','efc82c0a74d6c1a26f3471d0a36169dab64373bd696af9ed2040eecac72a9d80','2017-11-15 13:33:28'),('COPPER_2006_0317_092813AA.JPG','6acd7c9fa2978687ebc88a0e2a9b19f822135d8dffb2215f8250a3caca4ac055','2017-11-15 01:48:24'),('DSCF0305.JPG','f6521a3963db5ffb8cfdb9eac16c71b89602e0b94d64e5cf9fc30f1264bd93b0','2017-11-15 01:48:57'),('Illisa.jpeg','70d4bf9f43aa0b665837b1b444cffdc31d10646416dad2c6f0050071d88d5213','2017-11-15 22:54:59'),('IMG_5437.jpg','50633258b8b4f5d48e6177ac4dc23e2351eef2622cc8846b7721a09d53cd9efb','2017-11-15 01:34:02'),('Katarina.jpeg','17fd05182b16fca1b40d2de6ccc6f3f4cf69edbffd5e8857ed15b2976b24f8ea','2017-11-15 22:55:09'),('Lana.jpeg','8fabee1c228dc6073c4ee9a3b6ba7106abc651f4649419237a6389c0d08764df','2017-11-15 22:54:59'),('LingSauLegs.jpeg','39b9e2cc010c493c538ba1ee0d23aba6648cc964a5d4b84409e424ea7bc654ad','2017-11-15 13:06:11'),('LisaLegsAirport.jpeg','8ce0e703bdea33efe29f105d39fe41738da577a17c817381091c4b6b4e05dc25','2017-11-14 18:04:34');
/*!40000 ALTER TABLE `enckey` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-02 13:58:05
