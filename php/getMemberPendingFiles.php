<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $debug = 0;
    
    $included_files = get_included_files();
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("sessions.php", $included_files) ) {
        include_once 'sessions.php';
    }
    /*if (!in_array("crypto.php", $included_files)) {
        include_once 'crypto.php';
    }*/
    if ( !in_array("cryptoFunctions.php", $included_files) ) {
        include_once 'cryptoFunctions.php';
    }
    
    header("Access-Control-Allow-Origin: *");
    //--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    include_once 'dbClass.php';
    include_once 'dbFuncs.php';
    
    $userid = '';
    $pw = '';
    $pwhash = '';
    $sid = '';
    $aResult = array();
    
    //var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pw = $argv[2];
        $pwhash = $argv[3];
        $sid = $argv[4];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                print "Arg2x - pw Parameter $argv[2]" . PHP_EOL;
                print "Arg3x - pwhash Parameter $argv[3]" . PHP_EOL;
                print "Arg4x - Session ID Parameter $argv[4]" . PHP_EOL;
                print "1a - User Parameter $userid" . PHP_EOL;
                print "1a - pw Parameter $pw" . PHP_EOL;
                print "1a - pwhash Parameter $pwhash" . PHP_EOL;
                print "1a - Session ID Parameter $sid" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
    } else {
        if ( $debug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        if ( $debug == 1 ) {
            var_dump($_POST);
        }
        
        //if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        //if ( isset($_POST['pw']) ) $pwhash = cleanString($_POST['$pw']);
        //if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        //if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        
        $userid = $_POST['userid'];
        $pw = $_POST['pw'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        
        print "1z - User Parameter <$userid>" . PHP_EOL;
        print "1z - pw Parameter <$pw>" . PHP_EOL;
        print "1z - pwhash Parameter <$pwhash>" . PHP_EOL;
        print "1z - Session ID Parameter <$sid>" . PHP_EOL;
        
        if ( $debug == 1 ) {
            print "1z - User Parameter <$userid>" . PHP_EOL;
            print "1z - pw Parameter <$pw>" . PHP_EOL;
            print "1z - pwhash Parameter <$pwhash>" . PHP_EOL;
            print "1z - Session ID Parameter <$sid>" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    $DBFunc = new dbFuncs();
    
    if ( $debug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        print "1 - ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        print "2 - ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        print "3 - ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    if ( mysqli_connect_errno() ) {
        print "Failed to connect to DB: " . mysqli_connect_error();
        
        return -1;
    }
    if ( !$conn ) {
        if ( $debug == 1 ) print "LOGIN ERROR 001: " . mysqli_connect_error();
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        return -1;
    }
    
    if ( $debug == 1 ) print 'userid = ' . $userid . PHP_EOL;
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    $DB->setGDBSid($sid);
    $DB->setCurrUser($userid);
    $DB->setCurrPw($pwhash);
    $DB->setCurrSessionID($sid);
    
    //$rc = $DB->checkCreds($userid, $pwhash, $conn);
    $rc = checkCreds($userid, $pwhash, $conn);
    if ( $debug == 1 ) print "CREDS RC = <" . $rc . ">" . PHP_EOL;
    
    if ( $rc == 0 ) {
        if ( $debug == 1 ) print "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        if ( $debug == 1 ) $DBFunc->LogIt("LOGIN ERROR... No CREDS <" . $rc . ">");
        
        return -100;
    } else {
        if ( $debug == 1 ) print "CREDS CHECK SUCCESS..." . PHP_EOL;
    }
    
    $results = array();
    
    $sql = "select distinct UF.FileName, MF.FromEmail, max(MF.FileID) as FileID, Max(SentDate) as SentDate, Max(ExpireDate) as ExpireDate, Max(MF.segmentNbr) as SegNbr " . PHP_EOL;
    $sql .= " from MemberFiles MF " . PHP_EOL;
    $sql .= " join UploadedFiles UF " . PHP_EOL;
    $sql .= " on UF.FileID = MF.FileID" . PHP_EOL;
    $sql .= " where ToEmail = '$userid' " . PHP_EOL;
    //$sql .= " and (MF.segmentNbr = 0) " . PHP_EOL;
    $sql .= " and (DownloadedFlg is null or DownLoadedFlg = false) " . PHP_EOL;
    $sql .= " group by UF.FileName, MF.FromEmail ";
    
    if ( $debug == 1 ) $DBFunc->LogIt('From getMemberPendingFile.php');
    if ( $debug == 1 ) $DBFunc->LogIt($sql);
    
    if ( $debug == 1 ) $DBFunc->LogIt('@ sql = ' . $sql . PHP_EOL);
    
    $FileDir = "/var/www/html/SLupload/uploads/";
    $EncryptedDir = $global_uploadencrypteddir;
    ckdir($EncryptedDir);
    $DecryptedDir = $global_uploaddecrypteddir;
    ckdir($DecryptedDir);
    
    print "EncryptedDir-> " . $EncryptedDir . PHP_EOL;
    print "DecryptedDir-> " . $DecryptedDir . PHP_EOL;
    $i = 0;
    $qryResult = mysqli_query($conn, $sql) or die("02Q->Error in Selecting " . mysqli_error($conn) . PHP_EOL . $sql) . PHP_EOL;
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
        $fn = $row['FileName'];
        $fnenc = $fn . '.ENC';
        
        if ( $debug == 1 ) print "FN-> " . $fn . PHP_EOL;
        if ( $debug == 1 ) print "FNENC-> " . $fnenc . PHP_EOL;
        
        $source = $EncryptedDir . '/' . $fnenc;
        $dest = $DecryptedDir . '/' . $fn;
        
        if ( $debug == 1 ) print "source-> " . $source . PHP_EOL;
        if ( $debug == 1 ) print "dest-> " . $dest . PHP_EOL;
        
        //****************************************************
        if ( $debug == 1 ) print 'FileName: ' . $fn . PHP_EOL;
        $iOrigExists = ckFileExists($FileDir, $fnenc);
        $iExists = ckFileExists($EncryptedDir, $fn);
        if ( $debug == 1 ) echo('iExists : ' . $iExists . PHP_EOL);
        if ( !file_exists($source) ) {
            print('source NOT FOUND : ' . $source . PHP_EOL);
            $DBFunc->LogIt('source NOT FOUND : ' . $source);
            //Bare in mid - this could cause REAL ISSUES - so be prepared to remove it.
            //unset($results[$i]);
        } else {
            if ( $debug == 1 ) print('!! source FOUND : ' . $source . PHP_EOL);
            if ( $debug == 1 ) $DBFunc->LogIt('!! source FOUND : ' . $source);
        }
        
        if ( !file_exists($dest) ) {
            if ( $debug == 1 ) print('dest NOT FOUND : ' . $dest . PHP_EOL);
        } else {
            unlink($dest);
        }
        
        if ( $debug == 1 ) echo(' - - -dest FOUND : ' . $dest . PHP_EOL);
        
        if ( file_exists($source) ) {
            //-----------------------------------------------------------------------------------------
            $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
            $MySql = "select skey from enckey where FileName = '$fn' ";
            $QryResults = mysqli_query($conn, $MySql);
            
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $k = $row["skey"];
                    if ( $debug == 1 ) $DBFunc->LogIt("retrieve key: " . $k);
                }
            } else {
                $k = '';
                echo "ERROR->Action D, failed to retrieve key: " . $MySql . PHP_EOL;
                $DBFunc->LogIt("ERROR->Action D, failed to retrieve key: " . $MySql);
            }
            mysqli_close($conn);
            //-----------------------------------------------------------------------------------------
            if ( $debug == 1 ) print "DECRYPTION KEY <" . $k . '>' . PHP_EOL;
            if ( $debug == 1 ) LOGX('Decrypting ' . $source . ' TO ' . $dest);
            
            $b = decryptFile($source, $dest, $k);
            
            if ( $b == true ) {
                print '**** Decrypt SUCCESS ' . $fnenc . ' into ' . $fn . PHP_EOL;
                if ( $debug == 1 ) LOGX('**** Decrypt SUCCESS ' . $fnenc . ' into ' . $fn);
            } else {
                print '**** DECRYPT FAILED ' . $fnenc . '<' . $k . '>' . PHP_EOL;
                $DBFunc->LogIt('**** DECRYPT FAILED ' . $fnenc . '<' . $k . '>');
                LOGX('**** DECRYPT FAILED ' . $fnenc . '<' . $k . '>');
            }
        }
        //****************************************************
        $i = $i + 1;
    }
    mysqli_close($conn);
    if ( $debug == 1 ) {
        //print var_dump(isset($argv)) . PHP_EOL;
        //print var_dump($results) . PHP_EOL;
        echo json_encode($results) . PHP_EOL;
    } else
        echo json_encode($results);
    
    function ckdir ($dir)
    {
        $ddbug = 0;
        if ( $ddbug == 1 ) print ('**** ckdir: ' . $dir . PHP_EOL);
        if ( !is_dir($dir) ) {
            if ( $ddbug == 1 ) print ('ckdir Created Dir: ' . $dir . PHP_EOL);
            mkdir($dir, 0777, true);
        } else {
            if ( $ddbug == 1 ) print ('FOUND ckdir: ' . $dir . PHP_EOL);
        }
    }
    
    function ckFileExists ($dir, $fname)
    {
        $ddbug = 0;
        $fqn = $dir . $fname;
        if ( $ddbug == 1 ) echo('**** ckFileExists: ' . $fqn . PHP_EOL);
        if ( !file_exists($fqn) ) {
            if ( $ddbug == 1 ) echo('ckFileExists NOT FOUND : ' . $fqn . PHP_EOL);
            
            return -1;
        } else {
            if ( $ddbug == 1 ) echo('ckFileExists FOUND : ' . $fqn . PHP_EOL);
            
            return 0;
        }
    }