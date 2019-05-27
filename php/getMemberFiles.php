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
    
    $debug = 0;
    $userid = '';
    $pwhash = '';
    $sid = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) echo "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                echo "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                echo "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
                echo "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
                echo "1a - User Parameter $userid" . PHP_EOL;
                echo "1a - pwhash Parameter $pwhash" . PHP_EOL;
                echo "1a - Session ID Parameter $sid" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
    } else {
        if ( $debug == 1 ) {
            echo "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        
        if ( $debug == 1 ) {
            echo "1z - User Parameter $userid" . PHP_EOL;
            echo "1z - pwhash Parameter $pwhash" . PHP_EOL;
            echo "1z - Session ID Parameter $sid" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    if ( $debug == 1 ) echo "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        echo "1 - ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        echo "2 - ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        echo "3 - ERROR: missing login information." . PHP_EOL;
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
    
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
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $debug == 1 ) echo "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        
        return -100;
    } else {
        if ( $debug == 1 ) echo "CREDS CHECK...";
    }
    
    $results = array();
    //$sql = "select FromEmail from Member where FromEmail != '" . $userid . "' order by FromEmail";
    
    $sql = "select distinct FromEmail ";
    $sql .= "from GroupMember ";
    $sql .= "where Owner_FromEmail = '" . $userid . "' ";
    $sql .= "union ";
    $sql .= "select FromEmail from CompanyGroup where FromEmail != '" . $userid . "' ";
    
    if ( $debug == 1 ) echo 'sql = ' . $sql . PHP_EOL;
    
    $emailid = null;
    $qryResult = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
    }
    mysqli_close($conn);
    echo json_encode($results);