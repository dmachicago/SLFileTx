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
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    header("Access-Control-Allow-Origin: *");
//--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    include_once 'dbClass.php';
    
    $debug = 0;
    $ToEmail = '';
    $pwhash = '';
    $sid = '';
    $ToEmail = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) echo "Executing via STDIN." . PHP_EOL;
        $ToEmail = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $FromEmail = $argv[3];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                echo "Arg1 - ToEmail $argv[1]" . PHP_EOL;
                echo "Arg2 - pwhash Parameter $argv[2]" . PHP_EOL;
                echo "Arg3 - Session ID Parameter $argv[3]" . PHP_EOL;
                echo "Arg4 - FromEmail $argv[4]" . PHP_EOL;
                echo "1a - ToEmail $ToEmail" . PHP_EOL;
                echo "1a - pwhash Parameter $pwhash" . PHP_EOL;
                echo "1a - Session ID  $sid" . PHP_EOL;
                echo "1a - Session ID Parameter $sid" . PHP_EOL;
            }
        }
        //$ToEmail = cleanString($ToEmail);
        //$ToEmail = cleanString($ToEmail);
        //$ToEmail = cleanString($ToEmail);
    } else {
        if ( $debug == 1 ) {
            echo "Executing via URL - debug = 1:" . PHP_EOL;
            var_dump($_POST) . PHP_EOL;
        }
        
        if ( isset($_POST['ToEmail']) ) $ToEmail = cleanString($_POST['$ToEmail']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        if ( isset($_POST['FromEmail']) ) $FromEmail = cleanString($_POST['$sessionid']);
        
        $ToEmail = $_POST['ToEmail'];
        $pwhash = $_POST['pwhash'];
        $FromEmail = $_POST['FromEmail'];
        
        if ( $debug == 1 ) {
            echo "1z - ToEmail $ToEmail" . PHP_EOL;
            echo "1z - PW Hash $pwhash" . PHP_EOL;
            echo "1z - Session ID Parameter $sid" . PHP_EOL;
            echo "1z - FromEmail $FromEmail" . PHP_EOL;
        }
        //$ToEmail = cleanString($_POST['ToEmail']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
        //$FromEmail = cleanString($_POST['$FromEmail']);
    }
    
    $DB = new dbClass();
    if ( $debug == 1 ) echo "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($ToEmail) == 0 ) {
        echo "00 ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        echo "01 ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        echo "02 ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($FromEmail) == 0 ) {
        echo "03 ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
//$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $debug == 1 ) echo 'currsid: ' . $currsid . PHP_EOL;
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    if ( mysqli_connect_errno() ) {
        echo "Failed to connect to DB: " . mysqli_connect_error();
        
        return -1;
    }
    if ( !$conn ) {
        if ( $debug == 1 ) echo "LOGIN ERROR 001: " . mysqli_connect_error();
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        return -1;
    }
    
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    $DB->setGDBSid($sid);
    $DB->setCurrUser($ToEmail);
    $DB->setCurrPw($pwhash);
    $DB->setCurrSessionID($sid);
    
    //$rc = $DB->checkCreds($ToEmail, $pwhash, $conn);
    $rc = checkCreds($ToEmail, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $debug == 1 ) echo "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        
        return -100;
    } else {
        if ( $debug == 1 ) echo "CREDS CHECK...";
    }
    
    $results = array();
    
    $sql = " select distinct max(UPLOAD.FileID), UPLOAD.FileName, UPLOAD.directory " . PHP_EOL;
    $sql .= " from UploadedFiles UPLOAD " . PHP_EOL;
    $sql .= " join MemberFiles MEMBER " . PHP_EOL;
    $sql .= " on MEMBER.FileID = UPLOAD.FileID " . PHP_EOL;
    $sql .= " and MEMBER.ToEmail = '$ToEmail' " . PHP_EOL;
    $sql .= " and MEMBER.DownloadedFlg = 0 " . PHP_EOL;
    $sql .= " group by UPLOAD.FileName, UPLOAD.directory " . PHP_EOL;
    
    if ( $debug == 1 ) echo 'DownLoadUserFiles SQL: sql = ' . $sql . PHP_EOL;
    
    $emailid = null;
    $qryResult = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
    }
    mysqli_close($conn);
    if ( $debug == 1 ) echo 'DownLoadUserFiles: Returned aResult = ' . PHP_EOL . json_encode($aResult) . PHP_EOL . PHP_EOL;
    if ( $debug == 1 ) echo 'DownLoadUserFiles: Returned JASON = ' . PHP_EOL . json_encode($results) . PHP_EOL;
    echo json_encode($results);