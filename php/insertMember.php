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
    
    $IM_debug = 0;
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $MemberID = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $IM_debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $MemberID = $argv[4];
        $Groups = $argv[5];
        if ( $IM_debug == 1 ) {
            if ( $IM_debug == 1 ) {
                print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
                print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
                print "Arg4x - Members Parameter $argv[4]" . PHP_EOL;
                
                print "1a - User Parameter $userid" . PHP_EOL;
                print "1a - pwhash Parameter $pwhash" . PHP_EOL;
                print "1a - Session ID Parameter $sid" . PHP_EOL;
                print "1a - Members Parameter $MemberID" . PHP_EOL;
                print "1a - Groups Parameter $Groups" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
    } else {
        if ( $IM_debug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
            var_dump($_POST) . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        if ( isset($_POST['Members']) ) $sessionid = cleanString($_POST['$MemberID']);
        if ( isset($_POST['Groups']) ) $sessionid = cleanString($_POST['$Groups']);
        
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $MemberID = $_POST['Members'];
        $Groups = $_POST['Groups'];
        
        if ( $IM_debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
            print "1z - Members $MemberID" . PHP_EOL;
            print "1z - Groups $Groups" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $DB = new dbClass();
    $DbFunc = new dbFuncs();
    
    if ( $IM_debug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        print "ERROR: 01 missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        print "ERROR: 02 missing login information." . PHP_EOL;
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        print "ERROR: 03k missing login information." . PHP_EOL;
        
        return;
    }
    
    if ( $IM_debug == 1 ) $DB->showConstant();
//$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $IM_debug == 1 ) {
        print 'currsid: ' . $currsid . PHP_EOL;
        print 'global_user: ' . $global_user . PHP_EOL;
        print 'global_pass: ' . $global_pass . PHP_EOL;
        print 'global_dbname: ' . $global_dbname . PHP_EOL;
    }
    
    $conn = mysqli_connect('localhost', $global_user, $global_pass, $global_dbname);
    
    if ( mysqli_connect_errno() ) {
        LOGX("Failed to connect to DB: " . mysqli_connect_error());
        $aResult['ERROR'] = "Failed to connect to DB";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    }
    if ( !$conn ) {
        LOGX("LOGIN ERROR 001: " . mysqli_connect_error());
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        $aResult['ERROR'] = "LOGIN ERROR 001";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    }
    
    if ( $IM_debug == 1 ) {
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
        if ( $IM_debug == 1 ) print "LOGIN ERROR... No CREDS <$rc> @ <$userid>" . PHP_EOL;
        $aResult['ERROR'] = "No CREDS";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    } else {
        if ( $IM_debug == 1 ) print "*** CREDS CHECK GOOD..." . PHP_EOL;
    }
    
    $rc = isAdmin($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $IM_debug == 1 ) echo "*** LOGIN ERROR insertMember ... Not Admin <$rc>" . PHP_EOL;
        LOGX("insertMember LOGIN ERROR... Not Admin <$rc> @ <$userid>");
        $aResult['ERROR'] = "No ADMIN";
        $str = json_encode($aResult);
        echo $str;
        
        return;
    } else {
        if ( $IM_debug == 1 ) echo "*** isAdmin CHECK..." . PHP_EOL;
        LOGX("isAdmin CHECK @ <$userid>");
    }
    
    $i = 0;
    $GroupID = "";
    
    //*****************************************************************
    //ADD THE MEMBER whether new member or not
    $rc = insertMember($MemberID, $conn);
    //remove member from ALL associated groups
    if ( $IM_debug == 1 ) print 'Groups: $Groups, MemberID: $MemberID' . PHP_EOL;
    $rc = zeroizeGroupMember($MemberID, $conn);
    //*****************************************************************
    
    if ( strpos($Groups, '|') !== false ) {
        $aGroups = explode("|", $Groups);
        if ( $IM_debug == 1 ) echo "- Groups EXPLODED into array" . PHP_EOL;
        
        foreach ( $aGroups as $GroupID ) {
            $i += 1;
            
            if ( $IM_debug == 1 ) echo "00 GroupID: '$GroupID' " . PHP_EOL;
            if ( $IM_debug == 1 ) echo "00 MemberID: '$MemberID' " . PHP_EOL;
            
            if ( $GroupID != '' ) {
                $rc = insertGroupMember($GroupID, $MemberID, $conn);
                if ( $rc >= 0 ) {
                    $aResult['_Group'] = $GroupID;
                    $aResult['_Member'] = $MemberID;
                    $aResult['success'] = $rc;
                } else {
                    $aResult['ERROR'] = $rc;
                    $aResult['ERROR'] = "ERROR Failed to insert >$MemberID<";
                }
            }
        }
    } else {
        
        if ( $IM_debug == 1 ) echo "01 GroupID: '$GroupID' " . PHP_EOL;
        if ( $IM_debug == 1 ) echo "01 MemberID: '$MemberID' " . PHP_EOL;
        
        if ( $MemberID != '' ) {
            $rc = insertGroupMember($Groups, $MemberID, $conn);
            $i = $rc;
            if ( $rc >= 0 ) {
                $aResult['_Group'] = $GroupID;
                $aResult['_Member'] = $MemberID;
                $aResult['success'] = $rc;
            } else {
                $aResult['ERROR'] = $rc;
                $aResult['ERROR'] = "ERROR Failed to insert $MemberID";
            }
        }
    }
    
    $str = json_encode($aResult);
    echo $str;
    
    if ( $IM_debug == 1 ) print PHP_EOL;