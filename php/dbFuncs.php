<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $included_files = get_included_files();
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    } else
        print 'global.php NOT already indluded...';
    if ( !in_array("sessions.php", $included_files) ) {
        include_once 'sessions.php';
    }
    if ( !in_array("crypto.php", $included_files) ) {
        include_once 'crypto.php';
    }
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    class dbFuncs
    {
        var $debug = 0;
        //var $dbservername = $global_host;
        var $dbservername = 'localhost';
        var $dbusername = "username";
        var $dbpassword = "password";
        var $dbname = "k3";
        var $CurrUser = "xxxx";
        var $CurrPw = "xxxx";
        var $CurrSessionid = "xxxx";
        var $conn = null;
        
        function getConn ()
        {
            $getConn_debug = 1;
            if ( $getConn_debug == 1 ) {
                print("setConn dbservername: $this->dbservername, $this->dbusername, $this->dbpassword, $this->dbname" . PHP_EOL);
            }
            /* Make sure the connection is still alive, if not, try to reconnect */
            if ( $this->conn ) {
                //Connection is live;
                if ( $getConn_debug == 1 ) {
                    print('setConn Connection LIVE' . PHP_EOL);
                }
                if ( $getConn_debug == 1 ) {
                    print 'setConn already set...' . PHP_EOL;
                }
                //return 1;
            }
            try {
                $this->conn = mysqli_connect($this->dbservername, $this->dbusername, $this->dbpassword, $this->dbname);
                if ( $getConn_debug == 1 ) {
                    print 'setConn successful ...';
                }
            }
            catch ( Exception $e ) {
                print 'Could not connect: ' . mysqli_error() . PHP_EOL;
                print 'Caught exception: ' . $e->getMessage() . PHP_EOL;
                echo 'setConn exception: ' . $e->getMessage() . PHP_EOL;
            }
            if ( $this->conn->connect_error ) {
                return null;
            }
            
            return $this->conn;
        }
        
        public function setConn ()
        {
            $setConn_debug = 1;
            
            //if ($this->dbservername = 'localhost') $this->dbservername = $global_SvrName ;
            //if ($this->dbusername = 'username') $this->dbusername = $global_user ;
            //if ($this->dbname = 'k3') $this->dbname = global_dbname ;
            //if ($this->dbpassword = 'password') $this->dbpassword = $global_pass ;
            
            if ( $setConn_debug == 1 ) {
                print("BEGIN setConn ================================" . PHP_EOL);
                print("setConn dbservername: $this->dbservername" . PHP_EOL);
                print("setConn dbusername: $this->dbusername" . PHP_EOL);
                print("setConn dbpassword: $this->dbpassword" . PHP_EOL);
                print("setConn dbname: $this->dbname" . PHP_EOL);
            }
            /* Make sure the connection is still alive, if not, try to reconnect */
            if ( $this->conn ) {
                //Connection is live;
                if ( $setConn_debug == 1 ) {
                    print('setConn Connection LIVE' . PHP_EOL);
                }
                if ( $setConn_debug == 1 ) {
                    print 'setConn already set...' . PHP_EOL;
                }
                //return 1;
            }
            try {
                $this->conn = mysqli_connect($this->dbservername, $this->dbusername, $this->dbpassword, $this->dbname);
                if ( $setConn_debug == 1 ) {
                    print '### setConn successful ...' . PHP_EOL;
                }
            }
            catch ( Exception $e ) {
                //print 'XXXX Could not connect: ' . mysqli_error() . PHP_EOL;
                print 'XXXX ERROR setConn Caught exception: ' . $e->getMessage() . PHP_EOL;
                echo 'ERROR setConn exception: ' . $e->getMessage() . PHP_EOL;
                
                return -1;
            }
            if ( $this->conn->connect_error ) {
                return -1;
            }
            if ( $setConn_debug == 1 ) {
                print("END setConn ================================" . PHP_EOL);
            }
            
            return 1;
        }
        
        function checkCreds ($userid, $MemberPassWord)
        {
            $credDebug = 0;
            
            if ( $credDebug == 1 ) {
                echo '**** dbFunc->checkCreds' . PHP_EOL;
                echo "userid-> " . $userid . PHP_EOL;
                echo "dbFuncs->checkCreds MemberPassWord-> " . $MemberPassWord . PHP_EOL;
            }
            
            $this->setConn();
            
            $count = 0;
            $sql = "SELECT count(*) as CNTX FROM Member where FromEmail = '$userid' and MemberPassWord = '$MemberPassWord' ";
            
            if ( $credDebug == 1 ) {
                echo "SQL: " . $sql . PHP_EOL;
            }
            if ( $this->conn == null ) {
                echo "001 NULL CONN " . PHP_EOL;
            }
            if ( $result = mysqli_query($this->conn, $sql) ) {
                if ( mysqli_num_rows($result) == 0 ) {
                    echo '@@@ Cred Failure';
                }
            }
            $QryResults = mysqli_query($this->conn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $count = $row["CNTX"];
                    if ( $credDebug == 1 ) echo "EXTRACTED COUNT: " . $count . PHP_EOL;
                }
            } else {
                $count = 0;
            }
            
            /* Not sure why, but could not use echo here - HAD TO USE return. */
            
            return $count;
        }
        
        function authCreds ($userid, $pwhash, $SessionID)
        {
            $count = 0;
            $debug = 0;
            $this->setConn();
            
            if ( $debug == 1 ) {
                echo '**** global -> authCreds' . PHP_EOL;
                echo "userid-> " . $userid . PHP_EOL;
                echo "pwhash-> " . $pwhash . PHP_EOL;
                echo "SessionID-> " . $SessionID . PHP_EOL;
            }
            
            $rc = $this->ckSession($userid, $SessionID);
            
            if ( $rc <= 0 ) {
                print 'ERROR authCreds: failed ckSession';
                
                return $count;
            }
            
            $sql = "SELECT count(*) as CNTX FROM Member where FromEmail = '" . $userid . "' and MemberPassWord = '" . $MemberPassWord . "'";
            if ( $debug == 1 ) echo "SQL: " . $sql . PHP_EOL;
            $QryResults = mysqli_query($this->conn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $count = $row["CNTX"];
                    if ( $debug == 1 ) echo "EXTRACTED COUNT: " . $count . PHP_EOL;
                }
            } else {
                $count = 0;
            }
            if ( $debug == 1 ) echo "FINAL COUNT: " . $count . PHP_EOL;
            
            return $count;
        }
        
        function ckSession ($UserID, $sessionid)
        {
            if ( !$this->conn ) $this->setConn();
            $rc = -1;
            
            $MySql = "select count(*) from SessionKey where SessionID = '$sessionid' and EmailAddr = '$UserID' ";
            
            $QryResults = mysqli_query($this->conn, $MySql) or die("Error in ckSession: " . mysqli_error($this->conn) . PHP_EOL . $MySql . PHP_EOL);
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $rc = $row[0];
            }
            $this->conn->close();
            
            return $rc;
        }
        
        public function zeroizeTempPwCnt ($userid, $pwhash, $sid, $conn)
        {
            //if (!mysql_ping($conn)) {
            //    print 'Lost connection, exiting after query #1';
            //    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
            //}
            $bcnt = -1;
            $sql = "update Member set BadLoginCnt = 0 where FromEmail = '$userid' and MemberPassWord = '$pwhash' ";
            
            if ( $conn->query($sql) === true ) {
                $bcnt = 1;
            } else {
                $bcnt = -1;
                echo mysqli_errno($conn) . ": " . mysqli_error($conn) . "\n";
            }
            
            return $bcnt;
        }
        
        public function incrementTempPwCnt ($userid, $pwhash, $sid, $conn)
        {
            if ( !$conn ) {
                print 'Lost connection, exiting after query #1';
                $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
            }
            $success = -1;
            $sql = "update Member set BadLoginCnt = BadLoginCnt + 1 where FromEmail = '$userid' and MemberPassWord = '$pwhash' ";
            
            if ( $conn->query($sql) === true ) {
                $success = 1;
            } else {
                $success = -1;
                echo mysqli_errno($conn) . ": " . mysqli_error($conn) . "\n";
            }
            
            return $success;
        }
        
        public function ckTempCnt ($userid, $pwhash, $sid, $conn)
        {
            $ckTempCnt_debug = 0;
            //if (!mysql_ping($conn)) {
            //    print 'Lost connection, exiting after query #1';
            //    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
            //}
            $bcnt = -1;
            $sql = "select BadLoginCnt from Member where FromEmail = '$userid' and MemberPassWord = '$pwhash' ";
            
            if ( $ckTempCnt_debug = 1 ) LOGX('X1 -> SQL: ' . $sql);
            
            $QryResults = mysqli_query($conn, $sql);
            if ( mysqli_num_rows($QryResults) == 1 ) {
                while ( $array = mysqli_fetch_row($QryResults) ) {
                    $bcnt = $array[0];
                    if ( $ckTempCnt_debug = 1 ) LOGX('X2 -> $bcnt = ' . $bcnt);
                    //print 'X2 -> BCNT  = <' . $bcnt . '>' . PHP_EOL;
                }
            } else {
                $bcnt = 99;
            }
            
            return $bcnt;
        }
        
        function insertGroupMember ($GroupName, $NewMemberID)
        {
            if ( !mysqli_ping($this->conn) ) {
                $isset = setConn();
                if ( $isset < 0 ) return $isset;
            }
            
            try {
                $stmt = $this->conn->prepare("INSERT INTO Groups (GroupName, Owner_FromEmail, FromEmail) VALUES (?,?,?)");
                $stmt->bind_param("sss", $GroupName, $this->CurrUser, $NewMemberID);
                $stmt->execute();
                
                $stmt->close();
                $this->conn->close();
                
                return 1;
            }
            catch ( Exception $exception ) {
                print 'ERROR: ' . $exception . PHP_EOL;
                
                return -1;
            }
        }
        
        function insertFileUpload ($FileName, $segmentCount, $segmentNbr, $segmentSize, $directory, $filehash)
        {
            $xbug = 0;
            if ( !$this->conn ) {
                if ( $xbug == 1 ) print 'insertFileUpload NOTICE: conn is missing' . PHP_EOL;
                $isset = $this->setConn();
                if ( $isset < 0 ) {
                    if ( $xbug == 1 ) print 'ERROR - insertFileUpload: conn FAILED' . PHP_EOL;
                    
                    return $isset;
                }
                if ( $xbug == 1 ) print 'insertFileUpload NOTICE: conn CREATED' . PHP_EOL;
            }
            
            //This function binds the parameters to the SQL query and tells the database what the parameters are.
            //The "sss" argument lists the types of data that the parameters are. The s character tells
            //mysql that the parameter is a string.
            //The argument may be one of four types:
            //i - integer
            //d - double
            //s - string
            //b - BLOB
            $FileID = 0;
            try {
                if ( $xbug == 1 ) {
                    LOGX('insertFileUpload 01');
                    LOGX('insertFileUpload FileName: ' . $FileName);
                    LOGX('insertFileUpload segmentCount: ' . $segmentCount);
                    LOGX('insertFileUpload segmentNbr: ' . $segmentNbr);
                    LOGX('insertFileUpload segmentSize: ' . $segmentSize);
                    LOGX('insertFileUpload directory: ' . $directory);
                    LOGX('insertFileUpload FileID: ' . $FileID);
                    LOGX('insertFileUpload filehash: ' . $filehash);
                }
                
                $executionType = 1;
                $cnt = $this->ckUploadedFileExists($FileName, $this->conn);
                
                if ( $cnt > 0 ) {
                    
                    $recid = $this->getFileIDByFname($FileName, $this->conn);
                    
                    $MySql = "update UploadedFiles set ";
                    $MySql .= "segmentCount = segmentCount+1, ";
                    $MySql .= "segmentNbr = $segmentNbr ";
                    $MySql .= " where FileName = '$FileName'";
                    
                    if ( $xbug == 1 ) LOGX($MySql);
                    
                    if ( $this->conn->query($MySql) === true ) {
                        return $recid;
                    } else {
                        echo "Error UploadedFiles: " . $MySql . " ; " . PHP_EOL . $this->conn->error . PHP_EOL;
                        LOGX("Error UploadedFiles: " . $MySql . " ; " . PHP_EOL . $this->conn->error);
                        $recid = -1;
                    }
                } else {
                    $v = 0;
                    $commguid = guidv4();
                    //$cnt = $this->ckMemberFilesExist ($FileID, $FromEmail, $ToEmailArray);
                    $cnt = 0;
                    if ( $cnt == 0 ) {
                        $sql = "INSERT INTO UploadedFiles (FileName, segmentCount, segmentNbr,segmentSize, directory, FileID, filehash, commguid) VALUES ";
                        $sql .= "('" . $FileName . "'," . $segmentCount . "," . $segmentNbr . "," . $segmentSize . ",'" . $directory . "'," . $FileID . ",'" . $filehash . "','" . $commguid . "')";
                        if ( $xbug == 1 ) LOGX($sql);
                    } else if ( $cnt > 0 ) {
                        $sql = "update UploadedFiles set segmentNbr = segmentNbr + 1 WHERE FileID = xx and ";
                        $sql .= "('" . $FileName . "'," . $segmentCount . "," . $segmentNbr . "," . $segmentSize . ",'" . $directory . "'," . $FileID . ",'" . $filehash . "','" . $commguid . "')";
                        if ( $xbug == 1 ) LOGX($sql);
                    } else {
                        echo 'ERROR: ckMemberFilesExist failed...';
                        LOGX('ERROR: ckMemberFilesExist failed...');
                        $v = 1;
                    }
                    $b = false;
                    if ( $this->conn->query($sql) === true && $v == 0 ) {
                        //print "New record created successfully" . PHP_EOL;
                        $b = true;
                    } else {
                        $b = false;
                        print "Error: " . $sql . "<br>" . $this->conn->error . PHP_EOL;
                        LOGX("Error: " . $sql . " / " . $this->conn->error);
                    }
                    
                }
                
                if ( $xbug == 1 ) print 'insertFileUpload 04' . PHP_EOL;
                $recid = mysqli_insert_id($this->conn);
                
                if ( $xbug == 1 ) print 'insertFileUpload 05 -> recid: ' . $recid . PHP_EOL;
                $this->conn->close();
                
                if ( $xbug == 1 ) print 'insertFileUpload 06 - success' . PHP_EOL;
                
                return $recid;
            }
            catch ( Exception $exception ) {
                echo 'ERROR insertFileUpload : ' . $exception . PHP_EOL;
                $recid = -1;
            }
            echo $recid;
        }
        
        private function ckUploadedFileExists ($FileName, $pconn)
        {
            if ( !$this->conn ) $this->setConn();
            $rc = 0;
            
            $MySql = "select count(*) from UploadedFiles where FileName= '$FileName' ";
            $QryResults = mysqli_query($pconn, $MySql) or die("Error in ckSession: " . mysqli_error($pconn) . PHP_EOL . $MySql . PHP_EOL);
            
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $rc = $row[0];
            }
            
            return $rc;
        }
        
        private function getFileIDByFname ($FileName, $pconn)
        {
            $FileID = -1;
            
            $MySql = "select FileID from UploadedFiles where FileName = '$FileName'";
            
            $QryResults = mysqli_query($pconn, $MySql) or die("APQ1 - Error in Selecting UploadedFiles FileID: " . mysqli_error($pconn) . PHP_EOL . $MySql . PHP_EOL);
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $FileID = $row[0];
            }
            
            return $FileID;
        }
        
        function saveFileKeys ($IV, $SecretKey, $RowNbr)
        {
            $debug = 0;
            if ( $debug == 1 ) print 'insertFileKeys 01';
            $rc = 0;
            
            if ( !$this->conn ) $this->setConn();
            
            if ( $debug == 1 ) print '01 insertFileKeys' . PHP_EOL;
            $MySql = "SELECT RowNbr FROM FileKeys WHERE RowNbr = '$RowNbr' ";
            
            if ( $debug == 1 ) print 'MySql @ ' . $MySql . PHP_EOL;
            $xcnt = 0;
            if ( $check = $this->conn->query($MySql) ) {
                $xcnt = $check->num_rows;
            }
            
            if ( $this->debug == 1 ) print '02 $xcnt: ' . $xcnt . PHP_EOL;
            
            if ( $xcnt > 0 ) {
                if ( $this->debug == 1 ) print '03 EXITING insertFileKeys 03 @ $xcnt: ' . $xcnt . PHP_EOL;
                
                return -1;
            }
            
            if ( $debug == 1 ) print '04 insertFileKeys' . PHP_EOL;
            try {
                if ( $debug == 1 ) print '05 insertFileKeys' . PHP_EOL;
                
                $sql = "INSERT INTO FileKeys (IV, SecretKey, RowNbr) VALUES ";
                $sql .= "('" . $IV . "','" . $SecretKey . "'," . $RowNbr . ")";
                if ( $this->conn->query($sql) === true ) {
                    print "FileKeys Saved..." . PHP_EOL;
                } else {
                    print "FileKeys Error: " . $sql . "<br>" . $this->conn->error . PHP_EOL;
                }
                $this->conn->close();
                $rc = 1;
            }
            catch ( Exception $exception ) {
                echo 'ERROR insertFileKeys: ' . $exception . PHP_EOL;
                $rc = -1;
            }
            if ( $debug == 1 ) print '09 insertFileKeys - RC = $rc' . PHP_EOL;
            
            return $rc;
        }
        
        function deleteMemberFilesByRowID ($RowNbr)
        {
            if ( !$this->conn ) $this->setConn();
            $MySql = 'SELECT FromEmail as total FROM MemberFiles WHERE RowNbr = ' . $RowNbr;
            $QryResults = mysqli_query($this->conn, $MySql) or die("Error deleting MemberFiles: " . mysqli_error($this->conn));
            $this->conn->close();
            
            return $QryResults;
        }
        
        function getFileID ($UserID, $pwhash, $sessionid, $FileName)
        {
            if ( !$this->conn ) $this->setConn();
            $FileID = -1;
            
            $MySql = "select UF.FileID from UploadedFiles UF where FileName = '$FileName' and UF.segmentNbr = 0 ";
            
            $QryResults = mysqli_query($this->conn, $MySql) or die("1 Error in Selecting UploadedFiles FileID: " . mysqli_error($this->conn) . PHP_EOL . $MySql . PHP_EOL);
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $FileID = $row['FileID'];
            }
            $this->conn->close();
            
            return $FileID;
        }
        
        function deleteMemberFiles ($FileID, $FromEmail, $ToEmailArray)
        {
            if ( !$this->conn ) $this->setConn();
            $RowNbr = -1;
            $MySql = 'SELECT FromEmail as total FROM MemberFiles WHERE FileID = ' . $FileID;
            $MySql .= ' and FromEmail = "' . $FromEmail . '" ';
            $MySql .= ' and ToEmail = "' . $ToEmail . '" ';
            
            $QryResults = mysqli_query($this->conn, $MySql) or die("Error deleting MemberFiles: " . mysqli_error($this->conn));
            $this->conn->close();
            
            return $QryResults;
        }
        
        function getCommguid ($filename)
        {
            //select IV,SecretKey from FileKeys where RowNbr = 213;
            if ( !$this->conn ) {
                $this->setConn();
            } else {
                try {
                    $this->conn->close();
                }
                catch ( Exception $exception ) {
                    print 'Resetting connection: ' . $exception . PHP_EOL;
                }
                $this->setConn();
            }
            $k = '';
            
            $MySql = "";
            $MySql = "select commguid from UploadedFiles where FileName = '$filename' ";
            
            $QryResults = mysqli_query($this->conn, $MySql);
            //var_dump($QryResults) . PHP_EOL;
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $k = $row["skey"];
                }
            } else {
                $k = '';
            }
            $this->conn->close();
            
            $k = str_replace(' ', '', $k);
            
            return $k;
        }
        
        function getMemberFilesRowID ($FileName)
        {
            if ( !$this->conn ) {
                $this->setConn();
            } else {
                try {
                    $this->conn->close();
                }
                catch ( Exception $exception ) {
                    print 'Resetting connection: ' . $exception . PHP_EOL;
                }
                $this->setConn();
            }
            $RowNbr = -1;
            $row = null;
            
            $MySql = "select MF.RowNbr from UploadedFiles UF " . PHP_EOL;
            $MySql = $MySql . " join MemberFiles MF " . PHP_EOL;
            $MySql = $MySql . "     on MF.FileID = UF.FileID and MF.segmentNbr = 0 " . PHP_EOL;
            $MySql = $MySql . " where FileName='$FileName' ";
            
            $QryResults = mysqli_query($this->conn, $MySql) or die("1 Error getMemberFilesRowID: " . mysqli_error($this->conn) . PHP_EOL . $MySql . PHP_EOL);
            //$QryResults = mysqli_query($this->conn, $MySql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $RowNbr = $row["RowNbr"];
                }
            } else {
                $RowNbr = -1;
            }
            $this->conn->close();
            
            return $RowNbr;
        }
        
        function getEncKey ($RowNbr, $keytype)
        {
            //select IV,SecretKey from FileKeys where RowNbr = 213;
            if ( !$this->conn ) {
                $this->setConn();
            } else {
                try {
                    $this->conn->close();
                }
                catch ( Exception $exception ) {
                    print 'Resetting connection: ' . $exception . PHP_EOL;
                }
                $this->setConn();
            }
            $k = '';
            $MySql = "";
            
            if ( $keytype == 'iv' ) {
                $MySql = "select IV as skey from FileKeys where RowNbr = $RowNbr ";
            } else {
                $MySql = "select SecretKey as skey from FileKeys where RowNbr = $RowNbr ";
            }
            
            $QryResults = mysqli_query($this->conn, $MySql);
            //var_dump($QryResults) . PHP_EOL;
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $k = $row["skey"];
                }
            } else {
                $k = '';
            }
            $this->conn->close();
            
            return $k;
        }
        
        function ckMemberFilesExist ($FileID, $FromEmail, $ToEmail)
        {
            if ( !$this->conn ) $this->setConn();
            $RowNbr = -1;
            $MySql = 'SELECT count(*) FROM MemberFiles WHERE FileID = ' . $FileID;
            $MySql .= ' and FromEmail = "' . $FromEmail . '" ';
            $MySql .= ' and ToEmail = "' . $ToEmail . '" ';
            $MySql .= '" LIMIT 1';
            
            // echo 'ckMemberFilesExist: ' . PHP_EOL . $MySql . PHP_EOL ;
            
            $QryResults = mysqli_query($this->conn, $MySql) or die("1 Error in Selecting MemberFiles RowNbr: " . mysqli_error($this->conn) . PHP_EOL . $MySql . PHP_EOL);
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $RowNbr = $row[0];
            }
            $this->conn->close();
            
            return $RowNbr;
        }
        
        function saveKey ()
        {
            $skey = '';
            $MySql = 'select SHA2(UUID(), 256) as skey';
            
            if ( !$this->conn ) $this->setConn();
            
            $QryResults = mysqli_query($this->conn, $MySql) or die("3 Error in Selecting MemberFiles RowNbr: " . mysqli_error($this->conn));
            while ( $row = mysqli_fetch_row($QryResults) ) {
                $skey = $row[0];
            }
            $this->conn->close();
            echo $skey;
        }
        
        function insertMemberFiles ($filename, $FileID, $FromEmail, $arrayOfEmails, $segmentNbr)
        {
            $logThis = 1;
            $imfDebug = 0;
            $RowNbr = -1;
            if ( $imfDebug == 1 ) print 'IMF @  00' . PHP_EOL;
            $rc = null;
            $minrc = 100;
            $this->setConn();
            if ( $imfDebug == 1 ) print 'IMF @  00A ToEmailArray: ' . var_dump($arrayOfEmails) . PHP_EOL;
            foreach ( $arrayOfEmails as $ToEmail ) {
                if ( $logThis == 1 ) $this->LogIt('IMF @  01 processing: ' . $ToEmail);
                if ( $imfDebug == 1 ) print 'IMF @  01 processing: ' . $ToEmail . PHP_EOL;
                if ( $imfDebug == 1 ) LOGX('IMF @  01 processing: ' . $ToEmail);
                $MySql = 'SELECT FromEmail as total FROM MemberFiles WHERE FileID = ' . $FileID;
                $MySql .= ' and FromEmail = "' . $FromEmail . '" ';
                $MySql .= ' and ToEmail = "' . $ToEmail . '" ';
                //$MySql .= '" LIMIT 1';
                
                if ( $imfDebug == 1 ) print PHP_EOL . 'SQL -> ' . PHP_EOL . $MySql . PHP_EOL;
                if ( $imfDebug == 1 ) $this->LogIt('IMF @  SQL -> ' . $MySql);
                
                $xcnt = 0;
                if ( $check = $this->conn->query($MySql) ) {
                    if ( $imfDebug == 1 ) print PHP_EOL . 'IMF @  RECORD DOES NOT EXIST..' . PHP_EOL;
                    if ( $imfDebug == 1 ) $this->LogIt('IMF @  RECORD DOES NOT EXIST');
                    $xcnt = $check->num_rows;
                }
                if ( $xcnt > 0 ) {
                    if ( $imfDebug == 1 ) print PHP_EOL . 'IMF @  RECORD ALREADY EXISTS..' . PHP_EOL;
                    if ( $imfDebug == 1 ) $this->LogIt('IMF @  RECORD ALREADY EXISTS..');
                    
                    return 1;
                }
                
                $SecretKey = $this->genKey();
                $IV = $this->genKey();
                $IV = substr($IV, 1, 25);
                
                if ( $imfDebug == 1 ) echo PHP_EOL . '*** SecretKey: ' . $SecretKey . PHP_EOL;
                if ( $imfDebug == 1 ) echo PHP_EOL . '*** IV: ' . $IV . PHP_EOL;
                
                try {
                    $RowNbr = -1;
                    //*****************************************************************************************
                    $MySql = "INSERT INTO MemberFiles (FileID, FromEmail, ToEmail, segmentNbr) VALUES ('$FileID','$FromEmail','$ToEmail','$segmentNbr')";
                    if ( $logThis == 1 ) $this->LogIt('XX01 MemberFiles SQL:' . PHP_EOL . $MySql . PHP_EOL);
                    if ( $this->conn->query($MySql) === true ) {
                        $RowNbr = $this->conn->insert_id;
                        if ( $imfDebug == 1 ) print '@@@ LASTID: ' . $RowNbr . PHP_EOL;
                        $rc = 1;
                        if ( $rc < $minrc ) $minrc = $rc;
                        if ( $imfDebug == 1 ) print 'IMF @  01 Inserted: ' . $FromEmail . ' TO ' . $ToEmail . PHP_EOL;
                        if ( $imfDebug == 1 ) print 'IMF @  01a Inserted minrc: ' . $minrc . PHP_EOL;
                        $this->initMemberFiles($FromEmail);
                        if ( $logThis == 1 ) $this->LogIt("Successfully inserted memberfile: " . $FromEmail . ' TO ' . $ToEmail . PHP_EOL);
                    } else {
                        echo "Error: " . $MySql . " ; " . PHP_EOL . PHP_EOL;
                        echo "Error: Failed to insert -> " . $FromEmail . ' TO ' . $ToEmail . PHP_EOL;
                        if ( $logThis == 1 ) $this->LogIt("Error: " . $MySql . " ; " . PHP_EOL);
                        if ( $logThis == 1 ) $this->LogIt("Error: Failed to insert -> " . $FromEmail . ' TO ' . $ToEmail . PHP_EOL);
                        $rc = -1;
                        if ( $rc < $minrc ) $minrc = $rc;
                        if ( $imfDebug == 1 ) print 'IMF @  01b Inserted minrc: ' . $minrc . PHP_EOL;
                    }
                    //*****************************************************************************************
                    //$MySql = "select RowNbr from MemberFiles where FileID = '$FileID' and FromEmail = '$FromEmail' and ToEmail = '$ToEmail'";
                    ////$MySql = "select LAST_INSERT_ID()";
                    //if ($imfDebug ==1 ) echo PHP_EOL . 'GET ROW NBR: ' . PHP_EOL . $MySql  ;
                    
                    //if ( $this->conn->query($MySql) === true )
                    //{
                    //    if ($imfDebug ==1 ) echo PHP_EOL . '-- PRIOR TO FETCH ' . PHP_EOL ;
                    //    $QryResults = mysqli_query($this->conn, $MySql) or die("4 Error in Selecting MemberFiles RowNbr: " . mysqli_error($this->conn));
                    //    if ($imfDebug ==1 ) echo PHP_EOL . '-- AFTER TO FETCH ' . PHP_EOL ;
                    //    while ( $row = mysqli_fetch_row($QryResults) ) {
                    //        $RowNbr = $row['RowNbr'];
                    //    }
                    //} else {
                    //    echo PHP_EOL . "Error: Failed to fetch RowNbr -> " . PHP_EOL .  $this->conn->error . PHP_EOL;
                    //    $rc = -1;
                    //}
                    //*****************************************************************************************
                    $this->setConn();
                    $MySql = "INSERT INTO FileKeys (FileName, SecretKey, IV, RowNbr) VALUES ('$filename','$SecretKey','$IV',$RowNbr)";
                    if ( $imfDebug == 1 ) echo PHP_EOL . '*** FileKeys: ' . PHP_EOL . $MySql;
                    if ( $this->conn->query($MySql) === true ) {
                        if ( $logThis == 1 ) $this->LogIt('INSERTED INTO FileKeys' . PHP_EOL);
                        $rc = 1;
                    } else {
                        echo "Error FileKeys: " . $MySql . " ; " . PHP_EOL . $this->conn->error . PHP_EOL;
                        if ( $logThis == 1 ) $this->LogIt("Error FileKeys: " . $MySql . " ; " . PHP_EOL . $this->conn->error . PHP_EOL);
                        $rc = -1;
                    }
                    //*****************************************************************************************
                }
                catch ( Exception $exception ) {
                    echo 'ERROR insertMemberFiles: ' . $exception . PHP_EOL;
                    $rc = -1;
                    if ( $rc < $minrc ) $minrc = $rc;
                    if ( $imfDebug == 1 ) print 'IMF @  01c Inserted minrc: ' . $minrc . PHP_EOL;
                }
            }
            $this->conn->close();
            if ( $imfDebug == 1 ) print 'IMF @  Returning minrc: ' . $minrc . PHP_EOL;
            
            return $minrc;
        }
        
        function LogIt ($txt)
        {
            $d = date('Y-m-d');
            $logfqn = '../uploads/logx/Log.' . $d . '.txt';
            $myfile = file_put_contents($logfqn, $txt . PHP_EOL, FILE_APPEND);
        }
        
        function genKey ()
        {
            $skey = guidv4();
            
            return $skey;
        }
        
        public function initMemberFiles ($CurrUser)
        {
            //Update MemberFiles SET ExpireDate = ADDDATE(now(), 14) where DownloadedFlg is null ;
            $this->setConn();
            
            $sql = "Update `MemberFiles` set `DownloadedFlg` = 0, `SentDate` = now(), `ExpireDate` = ADDDATE(now(), 14) ";
            $sql .= " where `DownloadedFlg` is null ";
            $sql .= "and `FromEmail` = '$CurrUser' ";
            
            if ( mysqli_query($this->conn, $sql) ) {
                $this->conn->close();
                
                return 1;
            } else {
                echo 'ERROR 34Q : initMemberFiles failed' . PHP_EOL;
                //echo 'ERROR  34Q sql: ' . $sql . PHP_EOL;
                echo mysqli_errno($this->conn) . ": " . mysqli_error($this->conn) . "\n";
                
                return -1;
            }
        }
        
        public function setSessionLastAcquisition ($UserID, $pwhash, $SessionID)
        {
            $this->setConn();
            
            $sql = "Update SessionKey set LastAcquisitionDate = NOW() where SessionID = '$SessionID' ";
            
            LOGX('00 setSessionLastAcquisition: ' . $sql);
            
            if ( mysqli_query($this->conn, $sql) ) {
                $this->conn->close();
                
                return 1;
            } else {
                LOGX('00 setSessionLastAcquisition: ' . $sql);
                echo 'ERROR 34W : setLastAcquisition failed' . PHP_EOL . $sql . PHP_EOL;
                echo mysqli_errno($this->conn) . ": " . mysqli_error($this->conn) . "\n";
                
                return -1;
            }
        }
        
        public function getPendingFileCount ($FileName, $conn)
        {
            $count = 0;
            $sql = "select PendingDownLoadCount from UploadedFiles where FileName = '" + $FileName + "' ;";
            if ( $result = mysqli_query($conn, $sql) ) {
                if ( mysqli_num_rows($QryResults) > 0 ) {
                    while ( $row = mysqli_fetch_assoc($QryResults) ) {
                        $count = $row["PendingDownLoadCount"];
                        print "   -> getPendingFileCount EXTRACTED COUNT: " . $count . PHP_EOL;
                    }
                } else {
                    $count = 0;
                }
            }
            
            return $count;
        }
        
        public function deleteUploadedFileByName ($FileName, $conn)
        {
            $rc = 1;
            $MySql = "delete from enckey where FileName = '$FileName' " . PHP_EOL;
            if ( mysqli_query($conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                print "Deleted enckey : $rcnt rows" . PHP_EOL;
            } else {
                $rc = -1;
                echo "ERROR 0A1: Could not execute: " . $MySql . mysqli_error($conn);
            }
            
            return $rc;
        }
        
        public function deleteFileKeysByName ($FileName, $conn)
        {
            $rc = 1;
            $MySql = "delete from FileKeys where FileName = '$FileName' " . PHP_EOL;
            if ( mysqli_query($conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                print "Deleted FileKeys : $rcnt rows" . PHP_EOL;
            } else {
                $rc = -1;
                echo "ERROR 0A2: Could not execute: " . $MySql . mysqli_error($conn);
            }
            
            return $rc;
        }
        
        public function deleteFromMemberFilesByFileID ($FileName, $conn)
        {
            $rc = 1;
            $MySql = "delete from MemberFiles where FileID in (select FileID from UploadedFiles where FileName = '$FileName') " . PHP_EOL;
            if ( mysqli_query($conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                print "Deleted MemberFiles : $rcnt rows" . PHP_EOL;
            } else {
                $rc = -1;
                echo "ERROR 0A3: Could not execute: " . $MySql . mysqli_error($conn);
            }
            
            return $rc;
        }
        
        public function deleteUploadedFilesByFileID ($FileName, $conn)
        {
            $rc = 1;
            $MySql = "delete from UploadedFiles where FileName = '$FileName' " . PHP_EOL;
            if ( mysqli_query($conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                print "Deleted UploadedFiles : $rcnt rows" . PHP_EOL;
            } else {
                $rc = -1;
                echo "ERROR 0A4: Could not execute: " . $MySql . mysqli_error($conn);
            }
            
            return $rc;
        }
        
        public function decrementFileCnt ($FileName, $conn)
        {
            $xbug = 0;
            $rc = 1;
            $MySql = "update UploadedFiles set PendingDownLoadCount = PendingDownLoadCount-1 where FileName = '$FileName'";
            if ( mysqli_query($this->conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                if ( $xbug == 1 ) print "Decremented UploadedFiles : $rcnt rows" . PHP_EOL;
            } else {
                echo "ERROR 0A5: Could not execute: " . $MySql . mysqli_error($conn);
            }
            
            return $rc;
        }
        
        public function incrementFileCnt ($FileName, $conn)
        {
            $xbug = 0;
            $rc = 1;
            $MySql = "update UploadedFiles set PendingDownLoadCount = PendingDownLoadCount+1 where FileName = '$FileName'";
            if ( mysqli_query($this->conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                if ( $xbug == 1 ) print "Decremented UploadedFiles : $rcnt rows" . PHP_EOL;
            } else {
                echo "ERROR 0A5: Could not execute: " . $MySql . mysqli_error($conn);
            }
            
            return $rc;
        }
        
        public function removeReferencesUserFiles ($FileName, $UserID, $conn)
        {
            $xbug = 0;
            $rc = 1;
            $MySql = "delete from MemberFiles where FileID in (select FileID from UploadedFiles where FileName = '$FileName' and ToEmail = '$UserID') ";
            print '@001: ' . $MySql . PHP_EOL;
            if ( mysqli_query($this->conn, $MySql) ) {
                $rcnt = mysqli_affected_rows($conn);
                if ( $xbug == 1 ) print "Deleted UploadedFiles : $rcnt rows for User = $UserID." . PHP_EOL;
            } else {
                echo "ERROR 0A6: Could not execute: " . $MySql . mysqli_error($conn) . PHP_EOL;
                $rc = -1;
            }
            
            return $rc;
        }
        
        public function setDbConn ()
        {
            $this->conn = mysqli_connect($this->dbservername, $this->dbusername, $this->dbpassword, $this->dbname);
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
         * @param string $CurrUser
         */
        public function setCurrUser ($CurrUser)
        {
            $this->CurrUser = $CurrUser;
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
        
    }
    
    //$DB = new dbFuncs();
    //$DB->dbservername = $global_SvrName;
    //$DB->dbusername = $global_user;
    //$DB->dbpassword = $global_pass;
    //$DB->dbname = $global_dbname;
    //$DB->setDbname($global_dbname);