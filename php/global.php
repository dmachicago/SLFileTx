<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */

//--------------------------------------------------------------------------
// php script for fetching data from mysql database
//--------------------------------------------------------------------------
    $global_host = 'localhost';
    $global_ServerIP = '45.32.129.86';
    $global_full_url = 'https://45.32.129.86/SLupload';
    
    $global_SvrName = 'localhost';
    $global_conn = null;
    $global_SessionEndMinutes = 60;
    $global_RetainMinutes = 7 * 24 * 60;   //10,080
    $global_RetainDays = 7;
    $global_sessionID = null;
    $global_user = "k3all";
    $global_pass = "Copper@01";
    $global_dbname = "k3";
    
    $global_TempUserExpirationDays = 30;
    $global_PermUserExpirationDays = 30;
    $global_PWExpirationDays = 30;
    $global_FileExpirationDays = 7;
    $global_UserFileExpirationDays = 3;
    $global_UserDownloadExpirationDays = 1;
    
    $global_uploaddir = '/var/www/html/SLupload/uploads/';
    $global_uploadencrypteddir = '/var/www/html/SLupload/uploads/Encrypted';
    $global_uploadsharddir = '/var/www/html/SLupload/uploads/shard';
    $global_uploaddecrypteddir = '/var/www/html/SLupload/uploads/Decrypted';
    $global_logdir = '/var/www/html/SLupload/uploads/logx';
    
    function DeleteLOG ()
    {
        if ( file_exists($global_logdir) ) {
            foreach ( new DirectoryIterator($global_logdir) as $fileInfo ) {
                if ( $fileInfo->isDot() ) {
                    continue;
                }
                if ( $fileInfo->isFile() && time() - $fileInfo->getCTime() >= 2 * 24 * 60 * 60 ) {
                    unlink($fileInfo->getRealPath());
                }
            }
        }
    }
    
    function LOGX ($msg)
    {
        $doNotLog = 0;
        
        if ( $doNotLog == 1 ) return;
        
        $d = date("Y-m-d");
        $t = date("h:i:sa");
        $dt = $d . '@' . $t;
        $fqn = 'LogFile' . $d . '.txt';
        $fh = fopen($fqn, 'a') or die("LOGX error cannnot open LOG file");
        $stringData = $dt . ': ' . $msg . PHP_EOL;
        fwrite($fh, $stringData);
        fclose($fh);
    }
    
    function setGlobalConn ($global_SvrName, $global_user, $global_pass, $global_dbname)
    {
        $DebugsetGlobalConn = 0;
        if ( $DebugsetGlobalConn ) {
            print ('setGlobalConn: global_SvrName: ' . $global_SvrName);
            print ('setGlobalConn: global_user: ' . $global_user);
            print ('setGlobalConn: global_pass: ' . $global_pass);
            print ('setGlobalConn: global_dbname: ' . $global_dbname);
        }
        
        $global_conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
        
        if ( $DebugsetGlobalConn ) {
            print ('setGlobalConn: global_conn: ' . $global_conn);
        }
    }
    
    function removeTrailingComma ($word)
    {
        return rtrim($word, ',');
    }
    
    function checkCreds ($userid, $MemberPassWord, $conn)
    {
        $debug = 0;
        
        if ( $debug == 1 ) {
            echo '**** global -> checkCreds' . PHP_EOL;
            echo "userid-> " . $userid . PHP_EOL;
            echo "checkCreds MemberPassWord-> " . $MemberPassWord . PHP_EOL;
        }
        
        $count = 0;
        $sql = "SELECT count(*) as CNTX FROM Member where FromEmail = '" . $userid . "' and MemberPassWord = '" . $MemberPassWord . "'";
        if ( $debug == 1 ) echo "SQL: " . $sql . PHP_EOL;
        $QryResults = mysqli_query($conn, $sql);
        if ( mysqli_num_rows($QryResults) > 0 ) {
            while ( $row = mysqli_fetch_assoc($QryResults) ) {
                $count = $row["CNTX"];
                if ( $debug == 1 ) echo "EXTRACTED COUNT: " . $count . PHP_EOL;
            }
        } else {
            $count = 0;
        }
        if ( $debug == 1 ) echo "FINAL COUNT: " . $count . PHP_EOL;
        
        if ( $count > 0 ) {
			$myfile = fopen("k3log.txt", "a") or die("Unable to open file!");
			$txt = date('Y-m-d H:i:s') . " @ login: " . $userid;
			fwrite($myfile, "\n". $txt);
			fclose($myfile);
		}
        else 
		{
			$myfile = fopen("k3log.txt", "a") or die("Unable to open file!");
			$txt = date('Y-m-d H:i:s') . " @ Failed login: " . $userid;
			fwrite($myfile, "\n". $txt);
			fclose($myfile);		
		}
        return $count;
    }
    
    function isAdmin ($userid, $MemberPassWord, $conn)
    {
        $debug = 0;
        
        if ( $debug == 1 ) {
            echo '**** global -> isAdmin' . PHP_EOL;
            echo "isAdmin userid-> " . $userid . PHP_EOL;
            echo "isAdmin MemberPassWord-> " . $MemberPassWord . PHP_EOL;
        }
        
        $count = 0;
        $sql = "SELECT count(*) as CNTX FROM Member where FromEmail = '" . $userid . "' and MemberPassWord = '" . $MemberPassWord . "' and isAdmin = 1";
        if ( $debug == 1 ) echo "SQL: " . $sql . PHP_EOL;
        $QryResults = mysqli_query($conn, $sql);
        if ( mysqli_num_rows($QryResults) > 0 ) {
            while ( $row = mysqli_fetch_assoc($QryResults) ) {
                $count = $row["CNTX"];
                if ( $debug == 1 ) echo "isAdmin EXTRACTED COUNT: " . $count . PHP_EOL;
            }
        } else {
            $count = 0;
        }
        if ( $debug == 1 ) echo "FINAL COUNT: " . $count . PHP_EOL;
        
        /* Not sure why, but could not use echo here - HAD TO USE return. */
        
        return $count;
    }
    
    function cleanString ($var)
    {
        //remove unwanted slashes
        $var = stripslashes($var);
        //remove html from string
        $var = htmlentities($var);
        //remove html entirely
        //$var = strip_tags($var);
        return $var;
    }
    
    function cleanMySql ($var)
    {
        //Remove escape characters (must be used with open connection)
        $var = mysqli_real_escape_string($var);
        
        cleanString($var);
        
        return $var;
    }
    
    function guidv4 ()
    {
        if ( function_exists('com_create_guid') === true ) return trim(com_create_guid(), '{}');
        
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    function getGUID ()
    {
        if ( function_exists('com_create_guid') ) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                . substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12) . chr(125);// "}"
            return $uuid;
        }
    }
    
    function cleanMember ($tbl, $ColName, $MemberID, $conn)
    {
        $deleteMember_debug = 0;
        
        $sql = "delete from $tbl where $ColName = '$MemberID' ";
        if ( $deleteMember_debug == 1 ) print("cleanMember SQL: $sql." . PHP_EOL);
        
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $deleteMember_debug ) print ("SUCCESS cascade delete for $MemberID" . PHP_EOL);
                $rc = 1;
                //deleteMember ($MemberID, $conn);
            } else {
                if ( $deleteMember_debug ) print ("Error: " . $sql . " - " . $conn->error . PHP_EOL);
                $rc = -10;
            }
        }
        catch ( Exception $exception ) {
            print 'ERROR: deleteMember' . $exception . PHP_EOL;
            $rc = -20;
        }
        if ( $deleteMember_debug ) print ("deleteMember_debug rc = " . $rc . PHP_EOL);
        
        return $rc;
    }
    
    function purgeGroup ($GroupID, $conn)
    {
        $PG_Debug = 0;
        
        $sql = "delete from GroupMember where GroupName = '$GroupID' ";
        if ( $PG_Debug == 1 ) print("deleteGroupMember SQL: $sql." . PHP_EOL);
        
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $PG_Debug ) print ("SUCCESS delete for $GroupID " . PHP_EOL);
                $rc = 1;
                //deleteMember ($MemberID, $conn);
            } else {
                if ( $PG_Debug ) print ("Error: " . $sql . " - " . $conn->error . PHP_EOL);
                $rc = -10;
            }
        }
        catch ( Exception $exception ) {
            print 'ERROR: purgeGroup' . $exception . PHP_EOL;
            $rc = -20;
        }
        if ( $PG_Debug ) print ("purgeGroup rc = " . $rc . PHP_EOL);
        
        return $rc;
    }
    
    function deleteGroupMember ($GroupID, $MemberID, $conn)
    {
        $deleteMember_debug = 0;
        
        $sql = "delete from GroupMember where GroupName = '$GroupID' and FromEmail = '$MemberID'";
        if ( $deleteMember_debug == 1 ) print("deleteGroupMember SQL: $sql." . PHP_EOL);
        
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $deleteMember_debug ) print ("SUCCESS delete for $GroupID / $MemberID" . PHP_EOL);
                $rc = 1;
                //deleteMember ($MemberID, $conn);
            } else {
                if ( $deleteMember_debug ) print ("Error: " . $sql . " - " . $conn->error . PHP_EOL);
                $rc = -10;
            }
        }
        catch ( Exception $exception ) {
            print 'ERROR: deleteMember' . $exception . PHP_EOL;
            $rc = -20;
        }
        if ( $deleteMember_debug ) print ("deleteGroupMember rc = " . $rc . PHP_EOL);
        
        return $rc;
    }
    
    function deleteMember ($MemberID, $conn)
    {
        $deleteMember_debug = 0;
        //$this->setConn();
        
        if ( $deleteMember_debug == 1 ) print("Starting to delete $MemberID." . PHP_EOL);
        
        if ( $conn == null ) {
            echo "deleteMember: 001 NULL CONN " . PHP_EOL;
        }
        
        $cnt = getMemberCnt($MemberID, $conn);
        if ( $cnt == 0 ) {
            if ( $deleteMember_debug == 1 ) print("$MemberID not found in table, proceeding." . PHP_EOL);
            
            return 1;
        }
        
        //$sql = "delete from Member where FromEmail = '$MemberID' ";
        $rc = cleanMember('Member', 'FromEmail', $MemberID, $conn);
        $rc = cleanMember('MemberParm', 'FromEmail', $MemberID, $conn);
        $rc = cleanMember('EmailSentCnt', 'FromEmail', $MemberID, $conn);
        $rc = cleanMember('CompanyGroup', 'FromEmail', $MemberID, $conn);
        $rc = cleanMember('GroupMember', 'FromEmail', $MemberID, $conn);
        $rc = cleanMember('FromEmail', 'FromAddr', $MemberID, $conn);
        $rc = cleanMember('Email', 'FromEmail', $MemberID, $conn);
        $rc = cleanMember('Member', 'FromEmail', $MemberID, $conn);
        
        return $rc;
    }
    
    function zeroizeGroupMember ($MemberID, $conn)
    {
        $zeroize_debug = 1;
        if ( $zeroize_debug ) print (">>> zeroizeGroupMember MemberID: $MemberID " . PHP_EOL);
        
        if ( $MemberID == '' ) {
            print ("XXXXX zeroizeGroupMember: MemberID passed in is NULL, returning " . PHP_EOL);
            
            return 0;
        }
        
        if ( $conn == null ) {
            if ( $zeroize_debug ) print ("zeroizeGroupMember resetting connection" . PHP_EOL);
            //$this->setConn();
            print ("FAILURE: zeroizeGroupMember: 001 NULL CONN " . PHP_EOL);
        }
        
        //GroupName   | FromEmail
        $sql = "";
        $sql = "delete from GroupMember where FromEmail = '" . $MemberID . "'";
        if ( $zeroize_debug ) print ("==> zeroizeGroupMember SQL: $sql " . PHP_EOL);
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                $rc = 1;
                if ( $zeroize_debug ) print ("$MemberID successfully removed from all groups" . PHP_EOL);
            } else {
                if ( $zeroize_debug ) echo "Error: " . $sql . ' / ' . $conn->error;
                $rc = -10;
            }
            //$this->conn->close();
        }
        catch ( Exception $exception ) {
            print ('ERROR: zeroizeGroupMember: ' . $exception . PHP_EOL);
            $rc = -20;
        }
        
        return $rc;
    }
    
    function insertGroupMember ($Group, $MemberID, $conn)
    {
        $insertMember_debug = 0;
        if ( $insertMember_debug ) print ("insertMember Passed-in MemberID: $MemberID" . PHP_EOL);
        
        if ( $MemberID == '' ) {
            print ("XXXXX insertMember: MemberID passed in is NULL, returning " . PHP_EOL);
            
            return 0;
        }
        
        if ( $conn == null ) {
            if ( $insertMember_debug ) print ("insertMember resetting connection" . PHP_EOL);
            //$this->setConn();
            print ("FAILURE: insertMember: 001 NULL CONN " . PHP_EOL);
        }
        
        if ( $insertMember_debug ) print ("getGroupMemberCht PARMS: <$Group>, <$MemberID> " . PHP_EOL);
        
        $cnt = getGroupMemberCnt($Group, $MemberID, $conn);
        if ( $cnt > 0 ) {
            if ( $insertMember_debug ) print ("insertGroupMember Already exists, returning." . PHP_EOL);
            
            return $cnt;
        }
        
        if ( $insertMember_debug ) print ("==> insertMember '$MemberID' NOT found, continuing." . PHP_EOL);
        
        $sql = "insert into GroupMember (GroupName, FromEmail) values ('$Group','$MemberID') ";
        if ( $insertMember_debug ) print ("==> insertMember SQL: $sql " . PHP_EOL);
        
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $insertMember_debug ) print ("New record created successfully" . PHP_EOL);
                $rc = 1;
            } else {
                if ( $insertMember_debug ) echo "Error: " . $sql . " @ " . $conn->error;
                $rc = -10;
            }
            //$this->conn->close();
        }
        catch ( Exception $exception ) {
            print ('ERROR: insertMember' . $exception . PHP_EOL);
            $rc = -20;
        }
        
        return $rc;
    }
    
    function insertMember ($MemberID, $conn)
    {
        $insertMember_debug = 1;
        if ( $insertMember_debug ) print ("insertMember Passed-in MemberID: $MemberID" . PHP_EOL);
        
        if ( $MemberID == '' ) {
            print ("XXXXX insertMember: MemberID passed in is NULL, returning " . PHP_EOL);
            
            return 0;
        }
        
        if ( $conn == null ) {
            if ( $insertMember_debug ) print ("insertMember resetting connection" . PHP_EOL);
            //$this->setConn();
            print ("FAILURE: insertMember: 001 NULL CONN " . PHP_EOL);
        }
        
        $cnt = getMemberCnt($MemberID, $conn);
        if ( $cnt > 0 ) {
            if ( $insertMember_debug ) print ("insertMember Already exists, returning." . PHP_EOL);
            
            return $cnt;
        }
        
        if ( $insertMember_debug ) print ("==> insertMember '$MemberID' NOT found, continuing." . PHP_EOL);
        
        $sql = "insert into Member (FromEmail,MemberPassWord) values ('$MemberID','Welcome1!') ";
        if ( $insertMember_debug ) print ("==> insertMember SQL: $sql " . PHP_EOL);
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $insertMember_debug ) print ("New record created successfully" . PHP_EOL);
                $rc = 1;
            } else {
                if ( $insertMember_debug ) echo "Error: " . $sql . "<br>" . $conn->error;
                $rc = -10;
            }
            //$this->conn->close();
        }
        catch ( Exception $exception ) {
            print ('ERROR: insertMember' . $exception . PHP_EOL);
            $rc = -20;
        }
        
        return $rc;
    }
    
    function getMemberCnt ($MemberID, $conn)
    {
        $getMemberCnt_debug = 0;
        $bcnt = -1;
        
        $sql = "select count(*) cnt from Member where fromEmail = '$MemberID' ";
        if ( $getMemberCnt_debug == 1 ) {
            //print ('getMemberCnt SQL: ' . $sql . PHP_EOL);
            LOGX('X1 -> SQL: ' . $sql);
        }
        
        $QryResults = mysqli_query($conn, $sql);
        if ( mysqli_num_rows($QryResults) == 1 ) {
            while ( $array = mysqli_fetch_row($QryResults) ) {
                $bcnt = $array[0];
                if ( $getMemberCnt_debug = 1 ) {
                    LOGX('getMemberCnt -> bcnt = ' . $bcnt);
                    //print 'getMemberCnt -> BCNT  = <' . $bcnt . '>' . PHP_EOL;
                }
            }
        } else {
            $bcnt = -10;
            LOGX('NOTICE getMemberCnt -> bcnt = ' . $bcnt);
        }
        
        return $bcnt;
    }
    
    function getGroupMemberCnt ($Group, $MemberID, $conn)
    {
        
        $GC_debug = 0;
        $bcnt = -1;
        
        if ( $GC_debug == 1 ) {
            print 'getGroupMemberCnt -> Group  = <' . $Group . '>' . PHP_EOL;
            print 'getGroupMemberCnt -> MemberID  = <' . $MemberID . '>' . PHP_EOL;
        }
        
        $sql = "select count(*) cnt from GroupMember where fromEmail = '$MemberID' and GroupName = '$Group' ";
        
        if ( $GC_debug == 1 ) {
            print ('getGroupMemberCnt SQL: ' . $sql . PHP_EOL);
            LOGX('X1 -> SQL: ' . $sql);
        }
        
        $QryResults = mysqli_query($conn, $sql);
        if ( mysqli_num_rows($QryResults) == 1 ) {
            while ( $array = mysqli_fetch_row($QryResults) ) {
                $bcnt = $array[0];
                if ( $GC_debug = 1 ) {
                    LOGX('getGroupMemberCnt -> bcnt = ' . $bcnt);
                    //print 'getGroupMemberCnt -> BCNT  = <' . $bcnt . '>' . PHP_EOL;
                }
            }
        } else {
            $bcnt = -10;
            LOGX('NOTICE getGroupMemberCnt -> bcnt = ' . $bcnt);
            //if ($GC_debug == 1) print ('NOTICE getGroupMemberCnt -> bcnt = ' . $bcnt . PHP_EOL);
        }
        
        return $bcnt;
    }
    
    function deleteGroup ($GroupName, $conn)
    {
        $deleteGroup_debug = 0;
        //$this->setConn();
        
        if ( $conn == null ) {
            echo "deleteGroup: 001 NULL CONN " . PHP_EOL;
            rturn - 10;
        }
        
        $cnt = getGroupCnt($GroupName, $conn);
        if ( $cnt == 0 ) {
            return 1;
        }
        
        $sql = "delete from Groups where GroupName = '$GroupName' ";
        $rc = 0;
        try {
            if ( $conn->query($sql) === true ) {
                if ( $deleteGroup_debug ) print ("Record deleted successfully" . PHP_EOL);
                $rc = 1;
                deleteGroupMembers($GroupName, $conn);
            } else {
                if ( $deleteGroup_debug ) print ("Error: " . $sql . " - " . $conn->error . PHP_EOL);
                $rc = -10;
            }
            
        }
        catch ( Exception $exception ) {
            print 'ERROR: deleteGroup' . $exception . PHP_EOL;
            echo 'ERROR: deleteGroup' . $exception;
            $rc = -20;
        }
        
        return $rc;
    }
    
    function deleteGroupMembers ($GroupName, $conn)
    {
        $deleteGroupMembers_debug = 0;
        
        if ( $conn == null ) {
            echo "deleteGroupMembers: 001 NULL CONN " . PHP_EOL;
            
            return -30;
        }
        
        $cnt = getGroupCnt($GroupName, $conn);
        if ( $cnt == 0 ) {
            return 1;
        }
        
        $sql = "delete from GroupMember where GroupName = '$GroupName' ";
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $deleteGroupMembers_debug ) print ("Group Members deleted successfully" . PHP_EOL);
                $rc = 1;
            } else {
                if ( $deleteGroupMembers_debug ) print ("Error: " . $sql . " - " . $conn->error . PHP_EOL);
                LOGX("Error: " . $sql . " - " . $conn->error);
                $rc = -10;
            }
        }
        catch ( Exception $exception ) {
            print 'ERROR: deleteGroupMembers' . $exception . PHP_EOL;
            LOGX('ERROR: deleteGroupMembers' . $exception);
            $rc = -20;
        }
        
        return $rc;
    }
    
    function insertGroup ($GroupName, $conn)
    {
        $insertGroup_debug = 0;
        if ( $insertGroup_debug ) print ("Passed-in GroupName: $GroupName" . PHP_EOL);
        
        if ( $GroupName == '' ) {
            print ("XXXXX insertGroup: GroupName passed in is NULL, returning " . PHP_EOL);
            
            return 0;
        }
        
        if ( $conn == null ) {
            if ( $insertGroup_debug ) print ("insertGroup resetting connection" . PHP_EOL);
            //$this->setConn();
            print ("FAILURE: insertGroup: 001 NULL CONN " . PHP_EOL);
        }
        
        $cnt = getGroupCnt($GroupName, $conn);
        if ( $cnt > 0 ) {
            if ( $insertGroup_debug ) print ("insertGroup Group Already exists, returning." . PHP_EOL);
            
            return $cnt;
        }
        
        if ( $insertGroup_debug ) print ("==> insertGroup Group '$GroupName' NOT found, continuing." . PHP_EOL);
        
        $sql = "INSERT INTO Groups (GroupName) VALUES ('$GroupName')";
        $rc = 0;
        
        try {
            if ( $conn->query($sql) === true ) {
                if ( $insertGroup_debug ) print ("New record created successfully" . PHP_EOL);
                $rc = 1;
            } else {
                if ( $insertGroup_debug ) echo "Error: " . $sql . "<br>" . $conn->error;
                $rc = -10;
            }
            //$this->conn->close();
        }
        catch ( Exception $exception ) {
            print ('ERROR: insertGroup' . $exception . PHP_EOL);
            $rc = -20;
        }
        
        return $rc;
    }
    
    function getGroupCnt ($group, $conn)
    {
        $getGroupCnt_debug = 0;
        $bcnt = -1;
        
        $sql = "select count(*) cnt from Groups where GroupName = '$group' ";
        if ( $getGroupCnt_debug == 1 ) {
            //print ('getGroupCnt SQL: ' . $sql . PHP_EOL);
            LOGX('X1 -> SQL: ' . $sql);
        }
        
        $QryResults = mysqli_query($conn, $sql);
        if ( mysqli_num_rows($QryResults) == 1 ) {
            while ( $array = mysqli_fetch_row($QryResults) ) {
                $bcnt = $array[0];
                if ( $getGroupCnt_debug = 1 ) {
                    LOGX('getGroupCnt -> bcnt = ' . $bcnt);
                    //print 'getGroupCnt -> BCNT  = <' . $bcnt . '>' . PHP_EOL;
                }
            }
        } else {
            $bcnt = -10;
            LOGX('NOTICE getGroupCnt -> bcnt = ' . $bcnt);
        }
        
        return $bcnt;
    }
