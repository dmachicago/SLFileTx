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
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $conn = $argv[3];
        
        if ( $debug == 1 ) {
            print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
            print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
            print "1a - User Parameter $userid" . PHP_EOL;
            print "1a - pwhash Parameter $pwhash" . PHP_EOL;
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
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $conn = $_POST['conn'];
        
        if ( $debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
        }
    }
    
    $DB = new dbClass();
    
    if ( strlen($userid) == 0 ) {
        $aResult['ERROR: '] = "01 missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        $aResult['ERROR: '] = "02 missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    
    //$conn = mysqli_connect('localhost', $global_user, $global_pass, $global_dbname);
    if ( mysqli_connect_errno() ) {
        $aResult['ERROR: '] = "Failed to connect to DB: " . mysqli_connect_error();
        echo json_encode($aResult);
        
        return;
    }
    if ( !$conn ) {
        if ( $debug == 1 ) print "LOGIN ERROR 001: " . mysqli_connect_error();
        $aResult['ERROR: '] = "LOGIN ERROR 001: " . mysqli_connect_error();
        echo json_encode($aResult);
        
        return;
    }
    
    if ( $debug == 1 ) print 'userid = ' . $userid . PHP_EOL;
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    //$DB->setGDBSid($sid);
    $DB->setCurrUser($userid);
    $DB->setCurrPw($pwhash);
    //$DB->setCurrSessionID($sid);
    
    $results = array();
    
    $sql = "Select count(*) as cnt from Member where fromEmail = '$userid' and MemberPassWord = '$pwhash';";
    if ( $debug == 1 ) print 'checkCreds SQL: sql = ' . PHP_EOL . $sql . PHP_EOL;
    
    $qryResult = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
        if ( $debug == 1 ) print 'row data: ' . $row['cnt'] . PHP_EOL;
    }
    mysqli_close($conn);
    
    $retstr = json_encode($results);
    echo $retstr;
    //DONE