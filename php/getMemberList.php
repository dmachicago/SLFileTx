<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $included_files = get_included_files();
    
    $debug = 1;
    
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("sessions.php", $included_files) ) {
        include_once 'sessions.php';
    }
    /*if (!in_array("crypto.php", $included_files)) {
        include_once 'crypto.php';
    }*/
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    header("Access-Control-Allow-Origin: *");
//--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    include_once 'dbClass.php';
    
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $adminsid = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) echo "Executing via STDIN." . PHP_EOL;
        LOGX("Executing via STDIN.");
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $adminsid = $argv[4];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                echo "1a - User Parameter $userid" . PHP_EOL;
                echo "1a - pwhash Parameter $pwhash" . PHP_EOL;
                echo "1a - Session ID Parameter $sid" . PHP_EOL;
                echo "1a - ADMIN Session ID $adminsid" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
    } else {
        if ( $debug == 1 ) {
            echo "Executing via URL - debug = 1:" . PHP_EOL;
            LOGX("Executing via URL - debug = 1:");
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        if ( isset($_POST['adminsid']) ) $sessionid = cleanString($_POST['$adminsid']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $adminsid = $_POST['adminsid'];
        
        if ( $debug == 1 ) {
            LOGX("1z - User Parameter $userid");
            LOGX("1z - pwhash Parameter $pwhash");
            LOGX("1z - Session ID Parameter $sid");
            LOGX("1z - Admin Session ID Parameter $adminsid");
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    if ( $debug == 1 ) echo "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        echo "1 - ERROR: missing authentication information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        echo "2 - ERROR: missing authentication information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        echo "3 - ERROR: missing authentication information." . PHP_EOL;
        
        return;
    }
    if ( strlen($adminsid) == 0 ) {
        echo "4 - ERROR: missing authentication information." . PHP_EOL;
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
    LOGX($DB->showConstant());
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    if ( mysqli_connect_errno() ) {
        echo "Failed to connect to DB: " . mysqli_connect_error();
        
        return -1;
    }
    if ( !$conn ) {
        if ( $debug == 1 ) echo "LOGIN ERROR 001: " . mysqli_connect_error();
        LOGX("LOGIN ERROR 001: " . mysqli_connect_error());
        
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        return -1;
    }
    
    if ( $debug == 1 ) echo 'userid = ' . $userid . PHP_EOL;
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    $DB->setGDBSid($sid);
    $DB->setCurrUser($userid);
    $DB->setCurrPw($pwhash);
    $DB->setCurrSessionID($sid);
    
    //$rc = $DB->checkCreds($userid, $pwhash, $conn);
    LOGX("MemberList... CREDS <$userid, $pwhash>");
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $debug == 1 ) echo "001 LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        LOGX("001 LOGIN ERROR... No CREDS <$rc>");
        
        return -100;
    } else {
        if ( $debug == 1 ) echo "CREDS CHECK...";
        LOGX("CREDS CHECK...");
    }
    
    $rc = isAdmin($userid, $pwhash, $conn);
    if ( $rc == 0 ) {
        if ( $debug == 1 ) echo "LOGIN ERROR... Not Admin <$rc>" . PHP_EOL;
        LOGX("LOGIN ERROR... Not Admin <$rc>");
        
        return -200;
    } else {
        if ( $debug == 1 ) echo "isAdmin CHECK...";
        LOGX("isAdmin CHECK...");
    }
    
    
    $results = array();
    $sql = " select FromEmail from Member order by FromEmail";
    
    if ( $debug == 1 ) print 'getGroupMembersAdmin SQL: sql = ' . PHP_EOL . $sql . PHP_EOL;
    LOGX('getGroupMembersAdmin SQL: sql = ' . PHP_EOL . $sql);
    
    $emailid = null;
    $qryResult = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
        if ( $debug == 1 ) print 'row data: ' . $row['FromEmail'] . PHP_EOL;
        LOGX('row data: ' . $row['FromEmail']);
    }
    mysqli_close($conn);
    
    $retstr = json_encode($results);
    LOGX('retstr: $retstr');
    echo $retstr;
    //DONE