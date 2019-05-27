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
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $Groups = '';
    $aResult = array();
    $aGroup = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $Groups = $argv[4];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
                print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
                print "Arg4x - Groups Parameter $argv[4]" . PHP_EOL;
                
                print "1a - User Parameter $userid" . PHP_EOL;
                print "1a - pwhash Parameter $pwhash" . PHP_EOL;
                print "1a - Session ID Parameter $sid" . PHP_EOL;
                print "1a - Groups Parameter $Groups" . PHP_EOL;
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
        if ( isset($_POST['groups']) ) $sessionid = cleanString($_POST['$Groups']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $Groups = $_POST['groups'];
        
        if ( $debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
            print "1z - groups $Groups" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    $DbFunc = new dbFuncs();
    
    if ( $debug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        print "ERROR: 01 missing login information." . PHP_EOL;
        $aResult['ERROR'] = "01 missing login information.";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        print "ERROR: 02 missing login information." . PHP_EOL;
        $aResult['ERROR'] = "02 missing login information.";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        print "ERROR: 03b missing login information." . PHP_EOL;
        $aResult['ERROR'] = "03 missing login information.";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    }
    
    if ( $debug == 1 ) $DB->showConstant();
//$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $debug == 1 ) {
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
        if ( $debug == 1 ) print "LOGIN ERROR 001: " . mysqli_connect_error();
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        $aResult['ERROR'] = "LOGIN ERROR 001";
        $str = json_encode($results);
        echo $str;
        
        return;
    }
    
    if ( $debug == 1 ) {
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
        if ( $debug == 1 ) print "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        $aResult['ERROR'] = "No CREDS";
        $str = json_encode($results);
        echo $str;
        
        return;
    } else {
        if ( $debug == 1 ) print "*** CREDS CHECK GOOD..." . PHP_EOL;
    }
    
    $rc = isAdmin($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $debug == 1 ) echo "*** LOGIN ERROR deleteGroup ... Not Admin <$rc>" . PHP_EOL;
        LOGX("deleteGroup LOGIN ERROR... Not Admin <$rc>");
        $aResult['ERROR'] = "No ADMIN";
        $str = json_encode($results);
        echo $str;
        
        return;
    } else {
        if ( $debug == 1 ) echo "*** isAdmin CHECK..." . PHP_EOL;
        LOGX("isAdmin CHECK...");
    }
    
    $i = 0;
    if ( strpos($Groups, '|') !== false ) {
        $aGroups = explode("|", $Groups);
        if ( $debug == 1 ) echo "- Groups EXPLODED into array" . PHP_EOL;
        
        foreach ( $aGroups as $GroupName ) {
            $i += 1;
            if ( $debug == 1 ) echo "GroupName: '$GroupName' " . PHP_EOL;
            if ( $GroupName != '' ) {
                $rc = deleteGroup($GroupName, $conn);
                if ( $rc > 0 ) $aResult['SUCCESS'] = $rc; else
                    $aResult['ERROR'] = '00 deleteGroup: ' . $rc;
            }
        }
    } else {
        if ( $debug == 1 ) echo "- 1 Group INSERTED into array" . PHP_EOL;
        if ( $Groups != '' ) {
            $rc = deleteGroup($Groups, $conn);
            $i = $rc;
            if ( $rc > 0 ) $aResult['SUCCESS'] = $rc; else
                $aResult['ERROR'] = '01 deleteGroup: ' . $rc;
        }
    }
    
    $str = json_encode($aResult);
    echo $str;
    
    if ( $debug == 1 ) print PHP_EOL;