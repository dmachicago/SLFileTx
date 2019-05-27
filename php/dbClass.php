<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $included_files = get_included_files();
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("sessions.php", $included_files) ) {
        include_once 'sessions.php';
    }
    if ( !in_array("crypto.php", $included_files) ) {
        include_once 'crypto.php';
    }
    
    class dbClass
    {
        public $CONSTANT = null;
        private $C = null;
        private $gDBHost = null;
        private $gDBUser = null;
        private $gDBPass = null;
        private $gDBName = null;
        private $gDBRoomName = null;
        private $gDBResults = array();
        private $gDBConn = null;
        private $gDBSid = null;
        
        private $dbservername = "";
        private $dbusername = "";
        private $dbpassword = "";
        private $dbname = "";
        private $CurrUser = "";
        private $CurrPw = "";
        private $CurrSessionid = "";
        private $currUser = null;
        private $currPw = null;
        private $currSessionID = null;
        private $validUser = 0;
        private $RC = 0;
        
        function __construct ()
        {
            $this->CONSTANT = 'DB Class loaded...';
        }
        
        public function setDbservername ($dbservername)
        {
            $this->dbservername = $dbservername;
        }
        
        public function setDbusername ($dbusername)
        {
            $this->dbusername = $dbusername;
        }
        
        /**
         * @param string $dbpassword
         */
        public function setDbpassword ($dbpassword)
        {
            $this->dbpassword = $dbpassword;
        }
        
        /**
         * @param string $dbname
         */
        public function setDbname ($dbname)
        {
            $this->dbname = $dbname;
        }
        
        /**
         * @param string $CurrPw
         */
        public function setCurrPw ($CurrPw)
        {
            $this->CurrPw = $CurrPw;
        }
        
        /**
         * @param string $CurrSessionid
         */
        public function setCurrSessionid ($CurrSessionid)
        {
            $this->CurrSessionid = $CurrSessionid;
        }
        
        public function getCurrUser ()
        {
            return $this->CurrUser;
        }
        
        /**
         * @param string $CurrUser
         */
        public function setCurrUser ($CurrUser)
        {
            $this->CurrUser = $CurrUser;
        }
        
        public function dbAddGuidFilename ($fqn, $newfqn)
        {
            if ( !copy($fqn, $newfile) ) {
                echo 0;
            } else
                echo 1;
        }
        
        public function dbGetGuidFilename ($fqn)
        {
            if ( !copy($fqn, $newfile) ) {
                echo 0;
            } else
                echo 1;
        }
        
        public function CopyFile ($fqn, $newfqn)
        {
            if ( !copy($fqn, $newfile) ) {
                echo 0;
            } else
                echo 1;
        }
        
        public function dbCopyFile ($fqn)
        {
            $b = false;
            $rc = 0;
            $guid = com_create_guid(void);
            $newfqn = $guid . 'ENC';
            
            //Add new name to Database
            //Cross reference new name to file name in DB
            $rc = dbAddGuidFilename($fqn, $newfqn);
            
            if ( $rc == 1 ) {
                if ( !copy($fqn, $newfile) ) {
                    echo -10;
                }
            } else
                echo -15;
            
            //Encrypt new file
            $b = encrypt_file($fqn, $newfqn);
            if ( $b == false ) echo -20; else
                echo 1;
        }
        
        public function dbGetFile ($Originalfqn)
        {
            //Get GUID file name from DB
            //$guidfqn = getGuidFileName ($Originalfqn);
            $guidfqn = '';
            
            if ( !copy($guidfqn, $Originalfqn) ) {
                echo "failed to copy";
            }
            
            //Decrypt GUID file
            
            //Delete Original File
            
            //Return 1
            //Else return 0
        }
        
        public function dbGuidFile ($Originalfqn)
        {
            //Get GUID file name from DB
            //$guidfqn = getGuidFileName ($Originalfqn);
            $guidfqn = '';
            
            if ( !unlink($guidfqn) ) {
                echo 0;
            } else
                echo 1;
            
        }
        
        /**
         * @return null
         */
        public function getGDBSid ()
        {
            return $this->gDBSid;
        }
        
        public function setGDBSid ($gDBSid)
        {
            $this->gDBSid = $gDBSid;
        }
        
        public function DBInit ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            if ( ckConn() < 0 ) return -1;
            
            ckSessionID();
        }
        
        function ckVars ()
        {
            $debug = 0;
            if ( $debug == 1 ) {
                echo "ckVars 00" . PHP_EOL;
                echo "gDBUser <" . $this->gDBUser . '>' . PHP_EOL;
            }
            if ( $this->gDBUser == null ) return -10;
            if ( $this->gDBPass == null ) return -15;
            if ( $this->gDBConn == null ) return -20;
            if ( $this->gDBSid == null ) return -25;
            if ( $this->currUser == null ) return -30;
            if ( $this->currPw == null ) return -35;
            if ( $this->currSessionID == null ) return -40;
            
            return 1;
        }
        
        function ckSID ($sid, $currUser, $conn)
        {
            if ( session_id() == '' or session_id() == null ) //if ( !is_resource($this->currSessionID) )
            {
                try {
                    $this->currSessionID = setSessionID($currUser, $conn);
                    
                    return 1;
                }
                catch ( Exception $e ) {
                    echo 'Caught SESSION exception: ', $e->getMessage(), "\n";
                    
                    return -1;
                }
            } else
                return 1;
        }
        
        // SELECT AES_ENCRYPT('mytext', 'mykeystring') ;
        // SELECT AES_DECRYPT('mytext', 'mykeystring');
        // SELECT SHA1('Junebug@01');
        
        function genSha1 ($str)
        {
            return sha1($str);
        }
        
        function genSha256 ($str)
        {
            return $r = hash("sha256", $str, false);
        }
        
        function genSha512 ($str)
        {
            return hash('sha512', $str);
        }
        
        function genCrc32 ($str)
        {
            $checksum = crc32($str);
            
            return $checksum;
        }
        
        function dbAesDecrypt ($str, $keystring)
        {
            
            $encstr = null;
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            
            if ( ckConn() < 0 ) return -1;
            
            $row = null;
            $rc = null;
            
            $sql = "select AES_DECRYPT('" . $str . "', '" . $keystring . "')as enc)";
            $QryResults = mysqli_query($this->gDBConn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                $encstr = $row["CNT"];
                $this->ckSessionID();
            } else {
                $encstr = null;
            }
            
            return $encstr;
        }
        
        function ckSessionID ()
        {
            if ( session_id() == '' or session_id() == null ) //if ( !is_resource($this->currSessionID) )
            {
                try {
                    $this->currSessionID = setSessionID($this->currUser, $this->gDBConn);
                    
                    return 1;
                }
                catch ( Exception $e ) {
                    echo 'Caught SESSION exception: ', $e->getMessage(), "\n";
                    
                    return -1;
                }
            } else
                return 1;
        }
        
        function dbAesEncrypt ($str, $keystring)
        {
            
            $encstr = null;
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            
            if ( ckConn() < 0 ) return -1;
            
            $row = null;
            $rc = null;
            
            $sql = "select AES_DECRYPT('" . $str . "', '" . $keystring . "')as enc)";
            $QryResults = mysqli_query($this->gDBConn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                $encstr = $row["CNT"];
                $this->ckSessionID();
            } else {
                $encstr = null;
            }
            
            return $encstr;
        }
        
        function SetC ()
        {
            $this->C = 'C IS SET!' . "\n";
            $this->RC = 1;
            
            return RC;
        }
        
        function ShowC ()
        {
            echo $this->C . "\n";
        }
        
        function showConstant ()
        {
            echo $this->CONSTANT . "\n";
        }
        
        function getSession ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return null;
            }
            
            if ( session_id() == '' or session_id() == null ) {
                $this->gDBSid = session_id();
                
                return;
            }
            
            $this->gDBSid = setSessionID($this->currUser, $this->currPw, $this->gDBConn);
            
            if ( !is_resource($this->currSessionID) ) $this->currSessionID = $this->gDBSid;
            
            return $this->gDBSid;
        }
        
        function checkCreds ()
        
        {
            $bUsehash = 1;
            $DebugCheckCreds = 1;
            
            if ( $DebugCheckCreds == 1 ) echo "checkCreds 00" . PHP_EOL;
            if ( $DebugCheckCreds == 1 ) LOGX("checkCreds 00");
            
            $rc = $this->ckVars();
            if ( $DebugCheckCreds == 1 ) print "checkCreds ckVars: <" . $rc . '>' . PHP_EOL;;
            if ( $rc < 1 ) {
                if ( $DebugCheckCreds == 1 ) print "checkCreds 01 : failed ckVars: " . $rc . "..." . PHP_EOL;;
                
                return $rc;
            } else {
                if ( $DebugCheckCreds == 1 ) print "checkCreds 02 : PASSED ckVars: " . $rc . "..." . PHP_EOL;
            }
            
            if ( $this->ckConn() < 0 ) {
                if ( $DebugCheckCreds == 1 ) print "checkCreds ckConn 02 : FAILED : " . PHP_EOL;
                
                return -1;
            }
            
            $row = null;
            $rc = null;
            
            if ( $bUsehash == 1 ) {
                $sql = "SELECT count(*) as CNT FROM Member where FromEmail = '" . $this->currUser . "' and MemberPassWord = '" . $this->currPw . "'";
            } else {
                $sql = "SELECT count(*) as CNT FROM Member where  MemberPassWordHash = '" . $this->MemberPassWordHash . "'";
            }
            
            if ( $DebugCheckCreds == 1 ) echo 'SQL: ' . $sql . PHP_EOL;
            
            $QryResults = mysqli_query($this->gDBConn, $sql);
            if ( !$QryResults ) {
                if ( $DebugCheckCreds == 1 ) print "QryResults FAILED! " . PHP_EOL;
                $rc = -1;
                //$rc = $row["CNT"];
            } else {
                //$row = mysqli_fetch_row($QryResults);
                $rc = mysqli_num_rows($QryResults);
                if ( $DebugCheckCreds == 1 ) {
                    // Return the number of rows in result set
                    $rowcount = mysqli_num_rows($QryResults);
                    print "Result set has $rowcount rows: " . $rowcount . PHP_EOL;
                    print "QryResults PASSED rc =  <$rc> " . PHP_EOL;
                    print "QryResults row[0] = " . $rowcount . PHP_EOL;
                }
            }
            if ( $DebugCheckCreds == 1 ) print "RET COUNT: <$rc>" . PHP_EOL;
            
            echo $rc;
        }
        
        function ckConn ()
        {
            if ( is_resource($this->gDBConn) && get_resource_type($this->gDBConn) === 'mysql link' ) {
                return 1;
            } else {
                return $this->setConnection();
            }
        }
        
        function setConnection ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            if ( is_resource($this->gDBConn) && get_resource_type($this->gDBConn) === 'mysql link' ) {
                return 1;
            }
            TRY {
                $this->gDBConn = mysqli_connect($this->gDBHost, $this->gDBUser, $this->gDBPass, $this->gDBName);
                $rc = $this->ckSessionID();
            }
            CATCH ( Exception $ex ) {
                $this->gDBResults['ERROR'] = mysqli_error();
                $this->gDBConn = null;
                $rc = -1;
            }
            
            return $rc;
        }
        
        function getMemberFiles ()
        {
            $debug = 0;
            $rc = null;
            $json = null;
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                $json = '{"ERROR":"01 connection Failed"}';
                $jsonData = json_encode($json);
                
                return $jsonData;
            }
            
            if ( ckConn() < 0 ) {
                $json = '{"ERROR":"02 connection Failed"}';
                $jsonData = json_encode($json);
                
                return $jsonData;
            }
            
            $sql = "select F.FileID, F.FromEmail, F.ToEmail " . PHP_EOL;
            $sql .= "from MemberFiles F " . PHP_EOL;
            $sql .= "where ToEmail = '" . $this->currUser . "'";
            
            //$QryResults = mysqli_query($this->gDBConn, $sql);
            if ( !$QryResults = mysqli_query($this->gDBConn, $sql) ) {
                $json = '{"ERROR":"03 connection Failed"}';
                $json = '{"MYSQL":$sql}';
                $jsonData = json_encode($json);
                
                return $jsonData;
            }
            
            $results = array();
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $results[] = $row;
            }
            if ( $debug == 1 ) {
                var_dump(json_decode($results));
            }
            $jsonData = json_encode($results);
            
            return $jsonData;
        }
        
        function getMemberFilesMetadata ()
        {
            $debug = 0;
            $rc = null;
            $json = null;
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                $json = '{"ERROR":"01 connection Failed"}';
                $jsonData = json_encode($json);
                
                return $jsonData;
            }
            
            if ( ckConn() < 0 ) {
                $json = '{"ERROR":"02 connection Failed"}';
                $jsonData = json_encode($json);
                
                return $jsonData;
            }
            
            $sql = "select U.FileName, U.directory, F.FromEmail, F.FileID, U.segmentCount, U.segmentNbr, U.segmentSize, K.IV, K.SecretKey " . PHP_EOL;
            $sql .= "from MemberFiles F " . PHP_EOL;
            $sql .= "join FileKeys K " . PHP_EOL;
            $sql .= "	on K.FromEmail = F.FromEmail " . PHP_EOL;
            $sql .= "	and F.FileID = K.FileID " . PHP_EOL;
            $sql .= "join UploadedFiles U " . PHP_EOL;
            $sql .= "	on U.FileID = F.FileID " . PHP_EOL;
            $sql .= "join Member M " . PHP_EOL;
            $sql .= "	on M.FromEmail = F.FromEmail " . PHP_EOL;
            $sql .= "where FromEmail = '" . $this->currUser . "' and MemberPassWord = '" . $this->currPw . "'";
            
            //$QryResults = mysqli_query($this->gDBConn, $sql);
            if ( !$QryResults = mysqli_query($this->gDBConn, $sql) ) {
                $json = '{"ERROR":"03 connection Failed"}';
                $json = '{"MYSQL":$sql}';
                $jsonData = json_encode($json);
                
                return $jsonData;
            }
            
            $results = array();
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $results[] = $row;
            }
            if ( $debug == 1 ) {
                var_dump(json_decode($results));
            }
            $jsonData = json_encode($results);
            
            return $jsonData;
        }
        
        function downloadMemberFiles ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            
            if ( ckConn() < 0 ) return -1;
            
            $row = null;
            $rc = null;
            
            $sql = "select U.FileName, F.FromEmail, F.FileID, U.segmentCount, U.segmentNbr, U.segmentSize, K.IV, K.SecretKey /n";
            $sql .= "from MemberFiles F  /n";
            $sql .= "join FileKeys K  /n";
            $sql .= "	on K.FromEmail = F.FromEmail  /n";
            $sql .= "	and F.FileID = K.FileID  /n";
            $sql .= "join UploadedFiles U  /n";
            $sql .= "	on U.FileID = F.FileID  /n";
            $sql .= "join Member M  /n";
            $sql .= "	on M.FromEmail = F.FromEmail  /n";
            $sql .= "where FromEmail = '" . $this->currUser . "' and MemberPassWord = '" . $this->currPw . "'";
            $QryResults = mysqli_query($this->gDBConn, $sql);
            
            $jsonData = array();
            while ( $array = mysqli_fetch_row($QryResults) ) {
                $jsonData[] = $array;
            }
            $results = json_encode($jsonData);
            
            return $results;
        }
        
        function getGroupMembersAdmin ($OwnerEmail)
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            
            if ( ckConn() < 0 ) return -1;
            
            $row = null;
            $rc = null;
            
            $sql = "select distinct FromEmail ";
            $sql .= "from GroupMember ";
            $sql .= "where Owner_FromEmail = '" . $OwnerEmail . "' ";
            $sql .= "union ";
            $sql .= "select FromEmail from CompanyGroup where FromEmail != '" . $OwnerEmail . "' ";
            
            $jsonData = array();
            $QryResults = mysqli_query($this->gDBConn, $sql) or die("Error in Selecting " . mysqli_error($this->gDBConn));
            while ( $array = mysqli_fetch_row($QryResults) ) {
                $jsonData[] = $array;
            }
            $results = json_encode($jsonData);
            
            return $results;
        }
        
        function isValidSession ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            
            if ( ckConn() < 0 ) return -1;
            
            $this->ckSessionID();
            
            $row = null;
            $count = null;
            $sql = "SELECT count(*) as CNT  /n";
            $sql .= "FROM SessionKey  /n";
            $sql .= "where EmailAddr = '" . $this->currUser . "' and SessionNBR = '" . $this->gDBSid . "'  /n";
            $QryResults = mysqli_query($this->gDBConn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                $count = $QryResults["CNT"];
            } else {
                $count = 0;
            }
            
            return $count;
        }
        
        public function ckMember ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            if ( ckConn() < 0 ) return -1;
            
            $rc = checkCreds();
            if ( $rc <= 0 ) return $rc; else {
                $this->validUser = 1;
                
                return $rc;
            }
        }
        
        public function testDbAttach ()
        {
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                return $rc;
            }
            
            if ( ckConn() < 0 ) {
                $this->gDBResults['testDbAttach ckConn Failed'] = -1;
                $results = json_encode($this->gDBResults);
                
                return $results;
            }
            
            $this->ckSessionID();
            
            $this->gDBResults['testDbAttach $this->gDBHost'] = $this->gDBHost;
            $this->gDBResults['testDbAttach $this->gDBUser'] = $this->gDBUser;
            $this->gDBResults['testDbAttach $this->gDBPass'] = $this->gDBPass;
            $this->gDBResults['testDbAttach $this->gDBName'] = $this->gDBName;
            $this->gDBResults['testDbAttach $gDBUser'] = $this->gDBUser;
            $this->gDBResults['testDbAttach $gDBpass'] = $this->gDBPass;
            $this->gDBResults['testDbAttach $currSessionID'] = $this->currSessionID;
            
            $this->gDBResults['testDbAttach $currUser'] = $this->currUser;
            $this->gDBResults['testDbAttach $currPw'] = $this->currPw;
            
            $DbConn = mysqli_connect($this->gDBHost, $this->gDBUser, $this->gDBPass, $this->gDBName);
            if ( !$DbConn ) {
                $this->gDBResults['*** ERROR'] = mysqli_error();
                mysqli_close($DbConn);
            } else {
                //$gDBResults['*** $DbConn'] = $DbConn;
                $this->gDBResults['*** SUCCESS'] = mysqli_error();
                mysqli_close($DbConn);
            }
            
            $results = json_encode($this->gDBResults);
            
            return $results;
        }
        
        function getFileNameByID ($FileID)
        {
            
            $debug = 0;
            
            if ( $debug == 1 ) print 'getFileNameByID 00 for FileID :' . $FileID . PHP_EOL;
            
            $fname = '';
            $rc = $this->ckVars();
            if ( $rc < 1 ) {
                if ( $debug == 1 ) print 'getFileNameByID ckVars failed :' . PHP_EOL;
                
                return $rc;
            }
            
            if ( $this->ckConn() < 0 ) {
                if ( $debug == 1 ) print 'getFileNameByID CONN failed :' . PHP_EOL;
                
                return "ERROR FINDING FILE";
            }
            
            //$this->ckSessionID();
            
            $row = null;
            $count = null;
            $sql = "SELECT distinct FileName ";
            $sql .= "FROM UploadedFiles ";
            $sql .= "where FileID = '" . $FileID . "'" . PHP_EOL;
            if ( $debug == 1 ) print 'getFileNameByID SQL' . PHP_EOL . $sql . PHP_EOL;
            $QryResults = mysqli_query($this->gDBConn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = $QryResults->fetch_assoc() ) {
                    $fname = $row["FileName"];
                }
                if ( $debug == 1 ) print 'getFileNameByID Found Filename ' . $fname . PHP_EOL;
            } else {
                $fname = '';
                if ( $debug == 1 ) print 'getFileNameByID DID NOT FIND Filename ' . PHP_EOL;
            }
            if ( $debug == 1 ) print 'getFileNameByID fname: ' . $fname . PHP_EOL;
            
            return $fname;
        }
        
        public function setGDBHost ($gDBHost)
        {
            $this->gDBHost = $gDBHost;
        }
        
        public function setGDBUser ($User)
        {
            $debug = 0;
            $this->gDBUser = $User;
            if ( $debug == 1 ) {
                echo 'setGDBUser: <' . $User . '>' . PHP_EOL;
                echo 'setGDBUser: this->gDBUser: <' . $this->gDBUser . '>' . PHP_EOL;
            }
        }
        
        public function setGDBPass ($gDBPass)
        {
            $this->gDBPass = $gDBPass;
        }
        
        public function setGDBName ($gDBName)
        {
            $this->gDBName = $gDBName;
        }
        
        public function setGDBRoomName ($gDBRoomName)
        {
            $this->gDBRoomName = $gDBRoomName;
        }
        
        public function setGDBResults ($gDBResults)
        {
            $this->gDBResults = $gDBResults;
        }
        
        public function setGDBConn ($gDBConn)
        {
            $this->gDBConn = $gDBConn;
        }
        
        //public function setCurrUser ($currUser)
        //{
        //    $this->currUser = $currUser;
        //}
        
        //public function setCurrPw ($currPw)
        //{
        //    $this->currPw = $currPw;
        //}
        
        //public function setCurrSessionID ($currSessionID)
        //{
        //    $this->currSessionID = $currSessionID;
        //}
    }

?>