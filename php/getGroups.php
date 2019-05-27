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
        if ( $debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
                print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
                print "1a - User Parameter $userid" . PHP_EOL;
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
            var_dump($_POST) . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        
        if ( $debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    if ( $debug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        print "ERROR: 01 missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        print "ERROR: 02 missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        print "ERROR: 03e missing login information." . PHP_EOL;
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
//$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $debug == 1 ) {
        print 'currsid: ' . $currsid . PHP_EOL;
        print 'global_user: ' . $global_user . PHP_EOL;
        print 'global_pass: ' . $$global_pass . PHP_EOL;
        print 'global_dbname: ' . $global_dbname . PHP_EOL;
    }
    
    $conn = mysqli_connect('localhost', $global_user, $global_pass, $global_dbname);
    
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
    
    //$rc = $DB->checkCreds($userid, $pwhash);
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $debug == 1 ) print "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        
        return -100;
    } else {
        if ( $debug == 1 ) print "CREDS CHECK...";
    }
    
    $results = array();
    
    $sql = "select G.GroupName from Groups G " . PHP_EOL;
    $sql .= "join GroupMember GM  " . PHP_EOL;
    $sql .= "on G.GroupName = GM.GroupName  " . PHP_EOL;
    $sql .= "and GM.FromEmail = '$userid'  " . PHP_EOL;
    
    if ( $debug == 1 ) print 'getGroups SQL: sql = ' . $sql . PHP_EOL;
    
    $emailid = null;
    $qryResult = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
        if ( $debug == 1 ) print 'row data: ' . $row['GroupName'] . PHP_EOL;
    }
    mysqli_close($conn);
    if ( $debug == 1 ) print 'getGroups: Returned JASON = ' . PHP_EOL . json_encode($results) . PHP_EOL;
    $str = json_encode($results);
    echo $str;
    if ( $debug == 1 ) print PHP_EOL;