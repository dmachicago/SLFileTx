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
    $SelectedGroup = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        
        if ( $debug == 1 ) {
            print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
            print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
            print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
            print "Arg4x - Session ID Parameter $argv[4]" . PHP_EOL;
            $SelectedGroup = $argv[4];
            print "1a - User Parameter $userid" . PHP_EOL;
            print "1a - pwhash Parameter $pwhash" . PHP_EOL;
            print "1a - Session ID Parameter $sid" . PHP_EOL;
            print "1a - SelectedGroup ID Parameter $SelectedGroup" . PHP_EOL;
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
        if ( isset($_POST['SelectedGroup']) ) $sessionid = cleanString($_POST['$SelectedGroup']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $SelectedGroup = $_POST['SelectedGroup'];
        
        if ( $debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
            print "1z - SelectedGroup $SelectedGroup" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    if ( $debug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
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
    if ( strlen($sid) == 0 ) {
        $aResult['ERROR: '] = "03 missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
//$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $debug == 1 ) print 'currsid: ' . $currsid . PHP_EOL;
    
    $conn = mysqli_connect('localhost', $global_user, $global_pass, $global_dbname);
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
    $DB->setGDBSid($sid);
    $DB->setCurrUser($userid);
    $DB->setCurrPw($pwhash);
    $DB->setCurrSessionID($sid);
    
    //$rc = $DB->checkCreds($userid, $pwhash, $conn);
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        $aResult['ERROR'] = "LOGIN ERROR... No CREDS <$rc>";
        echo json_encode($aResult);
        
        return;
    } else {
        if ( $debug == 1 ) print "CRED CHECK GOOD..." . PHP_EOL;
    }
    
    $results = array();
    
    $sql = "select distinct FromEmail from GroupMember where GroupName = '$SelectedGroup' and FromEmail != '$userid' ";
    
    if ( $debug == 1 ) print 'getGroupsMembers SQL: sql = ' . PHP_EOL . $sql . PHP_EOL;
    
    $emailid = null;
    $qryResult = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    while ( $row = mysqli_fetch_assoc($qryResult) ) {
        $results[] = $row;
        if ( $debug == 1 ) print 'row data: ' . $row['FromEmail'] . PHP_EOL;
    }
    mysqli_close($conn);
    
    $retstr = json_encode($results);
    echo $retstr;
    //DONE