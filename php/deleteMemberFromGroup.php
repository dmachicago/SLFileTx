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
    if ( !in_array("dbFuncs.php", $included_files) ) {
        include_once 'dbFuncs.php';
    }
    
    
    header("Access-Control-Allow-Origin: *");
//--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    include_once 'dbClass.php';
    include_once 'dbFuncs.php';
    
    $dmem_debug = 0;
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $MemberID = '';
    $aResult = array();
    $aMember = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $dmem_debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $MemberID = $argv[4];
        $GroupID = $argv[5];
        if ( $dmem_debug == 1 ) {
            if ( $dmem_debug == 1 ) {
                print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
                print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
                print "Arg4x - MemberID Parameter $argv[4]" . PHP_EOL;
                
                print "1a - User Parameter $userid" . PHP_EOL;
                print "1a - pwhash Parameter $pwhash" . PHP_EOL;
                print "1a - Session ID Parameter $sid" . PHP_EOL;
                print "1a - MemberID Parameter $MemberID" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
    } else {
        if ( $dmem_debug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        if ( isset($_POST['MemberID']) ) $sessionid = cleanString($_POST['$MemberID']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $MemberID = $_POST['MemberID'];
        $GroupID = $_POST['GroupID'];
        
        if ( $dmem_debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
            print "1z - MemberID $MemberID" . PHP_EOL;
        }
    }
    
    $DB = new dbClass();
    $DbFunc = new dbFuncs();
    
    if ( $dmem_debug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        print "ERROR: 01 missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        print "ERROR: 02 missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        print "ERROR: 03d missing login information." . PHP_EOL;
        
        return;
    }
    
    if ( $dmem_debug == 1 ) $DB->showConstant();
//$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $dmem_debug == 1 ) {
        print 'currsid: ' . $currsid . PHP_EOL;
        print 'global_user: ' . $global_user . PHP_EOL;
        print 'global_pass: ' . $global_pass . PHP_EOL;
        print 'global_dbname: ' . $global_dbname . PHP_EOL;
    }
    
    $conn = mysqli_connect('localhost', $global_user, $global_pass, $global_dbname);
    
    if ( mysqli_connect_errno() ) {
        print "Failed to connect to DB: " . mysqli_connect_error();
        $aResult['ERROR'] = "Failed to connect to DB";
        $str = json_encode($results);
        echo $str;
        
        return;
    }
    if ( !$conn ) {
        if ( $dmem_debug == 1 ) print "LOGIN ERROR 001: " . mysqli_connect_error();
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        $aResult['ERROR'] = "LOGIN ERROR 001";
        $str = json_encode($results);
        echo $str;
        
        return;
    }
    
    if ( $dmem_debug == 1 ) {
        print 'userid = ' . $userid . PHP_EOL;
        print 'currsid: ' . $currsid . PHP_EOL;
        print 'global_user: ' . $global_user . PHP_EOL;
        print 'global_pass: ' . $global_pass . PHP_EOL;
        print 'global_dbname: ' . $global_dbname . PHP_EOL;
        print 'pwhash: ' . $pwhash . PHP_EOL;
        print 'sid: ' . $sid . PHP_EOL;
    }
    
    //***********************************************
    $DB->setDbservername($global_ServerIP);
    //***********************************************
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    $DB->setGDBSid($sid);
    $DB->setCurrUser($userid);
    $DB->setCurrPw($pwhash);
    $DB->setCurrSessionID($sid);
    
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $dmem_debug == 1 ) print "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        $aResult['ERROR'] = "No CREDS";
        $str = json_encode($results);
        echo $str;
        
        return;
    } else {
        if ( $dmem_debug == 1 ) print "*** CREDS CHECK GOOD..." . PHP_EOL;
    }
    
    $rc = isAdmin($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $dmem_debug == 1 ) echo "*** LOGIN ERROR deleteGroupMember ... Not Admin <$rc>" . PHP_EOL;
        LOGX("deleteGroupMember LOGIN ERROR... Not Admin <$rc> / <$userid>");
        $aResult['ERROR'] = "No ADMIN";
        $str = json_encode($results);
        echo $str;
        
        return;
    } else {
        if ( $dmem_debug == 1 ) echo "*** isAdmin CHECK..." . PHP_EOL;
        LOGX("isAdmin CHECK for: <$userid> ");
    }
    
    
    if ( $dmem_debug == 1 ) echo "- 1 Single Member to Process" . PHP_EOL;
    
    $rc = deleteGroupMember($GroupID, $MemberID, $conn);
    if ( $dmem_debug == 1 ) echo "** RETURNED RC = >$rc<" . PHP_EOL;
    
    if ( $rc > 0 ) {
        $aResult['SUCCESS'] = $rc;
    } else {
        $aResult['ERROR'] = $rc;
    }
    
    $str = json_encode($aResult);
    echo $str;
    
    if ( $dmem_debug == 1 ) print PHP_EOL;