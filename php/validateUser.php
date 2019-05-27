<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    header("Access-Control-Allow-Origin: *");
//--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    
    include_once 'dbClass.php';
    include_once 'dbFuncs.php';
    include_once 'Encrypt.php';
    include_once 'global.php';
    
    $validateUser_debug = 0;
    $isadmin = 0;
    $userid = '';
    $pwhash = '';
    $pw = '';
    $sid = '';
    $temppw = '0';
    $aResult = array();
    
    $DB = new dbClass();
    $DBFunc = new dbFuncs();
    
    //var_dump($argv);
    //        var_dump(isset($argv));
    
    if ( $validateUser_debug == 1 ) {
        print "** ValidateUser.php DEBUG ON..." . PHP_EOL;
    }
    
    if ( defined('STDIN') ) {
        if ( $validateUser_debug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
        }
        
        $userid = $argv[1];
        $pw = $argv[2];
        $pwhash = $argv[3];
        $sid = $argv[4];
        $temppw = $argv[5];
        
        if ( $validateUser_debug == 1 ) {
            print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
            print "Arg2x - pw Parameter $argv[2]" . PHP_EOL;
            print "Arg3x - pwhash Parameter $argv[3]" . PHP_EOL;
            print "Arg4x - SID Parameter $argv[4]" . PHP_EOL;
            print "Arg4x - temppw Parameter $argv[5]" . PHP_EOL;
            print "1x - User Parameter $userid" . PHP_EOL;
            print "1x - pwhash Parameter $pwhash" . PHP_EOL;
            print "1x - pwhash Parameter $pwhash" . PHP_EOL;
            print "1x - Session ID Parameter $sid" . PHP_EOL;
            print "1x - temppw Parameter $temppw" . PHP_EOL;
        }
        
        if ( $validateUser_debug == 1 ) {
            print "1a - User Parameter $userid" . PHP_EOL;
            print "1a - pwhash Parameter $pwhash" . PHP_EOL;
            print "1a - Session ID Parameter $sid" . PHP_EOL;
        }
    } else {
        if ( $validateUser_debug == 1 ) {
            print "ValidateUser Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $temppw = $_POST['temppw'];
        
        if ( $sid == null ) $sid == 'abcdef';
        
        if ( $validateUser_debug == 1 ) {
            print "validateUser - User Parameter $userid" . PHP_EOL;
            print "validateUser - pwhash Parameter $pwhash" . PHP_EOL;
            print "validateUser - Session ID Parameter $sid" . PHP_EOL;
            print "validateUser - temppw Parameter $temppw" . PHP_EOL;
        }
    }
    
    $failed = 0;
    
    if ( strlen($userid) == 0 ) {
        print "01 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR'] = '1 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    if ( strlen($pwhash) == 0 ) {
        print "02 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR'] = '2 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    if ( strlen($sid) == 0 ) {
        print "03 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR'] = '3 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    
    if ( $failed == 1 ) return;
    
    $DB->dbuser = $userid;
    $DB->dbpass = $pwhash;
    //$DB->$dbSid = $sid;
    
    if ( $validateUser_debug == 1 ) {
        print 'global_host: ' . $global_host . ' @ global_user: ' . $global_user . ' @global_pass: ' . $global_pass . ' @global_dbname: ' . $global_dbname . PHP_EOL;
    }
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    $conn2 = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    if ( !$conn ) {
        if ( $validateUser_debug == 1 ) {
            print "LOGIN ERROR " . mysqli_error();
            print 'FAILED LOGIN FOR -> global_host: ' . $global_host . ' @ global_user: ' . $global_user . ' @global_pass: ' . $global_pass . ' @global_dbname: ' . $global_dbname . PHP_EOL;
            if ( mysqli_connect_errno() ) { // creation of the connection object has some other error
                print ("Connect failed: " . mysqli_connect_errno() . " : " . mysqli_connect_error());
            }
        }
        $aResult['ERROR'] = mysqli_error();
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        
        return;
    } else {
        if ( $validateUser_debug == 1 ) {
            print "LOGIN Successful" . PHP_EOL;
        }
    }
    
    if ( $temppw == 1 ) {
        $badcnt = $DBFunc->ckTempCnt($userid, $pwhash, $sid, $conn2);
        if ( $badcnt > 3 ) {
            $aResult['ERROR'] = 'Your account is expired, please contact an administrator for resolution.';
            $aResult['TEMPPW'] = 'Your account is expired, please contact an administrator for resolution.';
            $jsonStr = json_encode($aResult);
            echo $jsonStr;
            
            return;
        }
    }
    
    if ( $validateUser_debug == 1 ) {
        print $userid . $pwhash . PHP_EOL;
        print 'DB->dbuser:' . $DB->dbuser . PHP_EOL;
        $_SESSION['DB->dbuser'] = $DB->dbuser;
    }
    
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $validateUser_debug == 1 ) {
        print "checkCreds: rc = {" . $rc . '}' . PHP_EOL;
    }
    
    if ( $rc == 0 ) {
        mysqli_close($conn);
        if ( $validateUser_debug == 1 ) print 'Member validation failed.' . PHP_EOL;
        
        $_SESSION['ERROR'] = 'Member vaildation failed.';
        $_SESSION['SUCCESS'] = 0;
        $Obj = json_encode($_SESSION);
        if ( $validateUser_debug == 1 ) print  "jsonStr = " . $Obj . PHP_EOL;
        echo $Obj;
        
        return;
    } else {
        
        $isadmin = isAdmin($userid, $pwhash, $conn);
        $_SESSION['isadmin'] = $isadmin;
        
        if ( $isadmin == 1 ) {
            if ( $validateUser_debug == 1 ) print  "user is an admin..." . PHP_EOL;
            $global_sessionID = setAdminSessionID($userid, $conn);
        }
        
        //********************************************
        $sessionid = setSessionID($userid, $conn);
        //********************************************
        $_SESSION['SUCCESS'] = 1;
        $_SESSION['sessionid'] = $sessionid;
        $_SESSION['time'] = time();
        $_SESSION['memberid'] = $userid;
        $_SESSION['pwhash'] = sha1($pwhash);
        $_SESSION['memberhash'] = sha1($userid);
        $_SESSION['AdminSID'] = $global_sessionID;
        $_SESSION['global_sessionID'] = $global_sessionID;
        $_SESSION['isadmin'] = $isadmin;
        
        if ( is_object($conn) && get_class($conn) == 'mysqli' ) {
            if ( $conn_thread = mysqli_thread_id($conn) ) {
                $conn->kill($conn_thread);
            }
            $conn->close();
        }
        
        if ( $temppw == 1 ) {
        } else {
        }
        if ( $temppw == 1 ) {
            $DBFunc->incrementTempPwCnt($userid, $pwhash, $sid, $conn2);
        } else {
            $DBFunc->zeroizeTempPwCnt($userid, $pwhash, $sid, $conn2);
        }
    }
    
    //$Obj = json_encode($aResult);
    $jsonStr = json_encode($_SESSION);
    if ( $validateUser_debug == 1 ) print  "jsonStr = " . $jsonStr . PHP_EOL;
    
    try {
        mysqli_close($conn2);
        if ( $validateUser_debug == 1 ) print  'Closed connection 2' . PHP_EOL;
    }
    catch ( Exception $e ) {
        print '@2 Could close Database connection: ' . mysqli_error() . PHP_EOL;
    }

	try {
        mysqli_close($conn);
        if ( $validateUser_debug == 1 ) print  'Closed connection 1' . PHP_EOL;
    }
    catch ( Exception $e ) {
        print '@2Could close Database connection: ' . mysqli_error() . PHP_EOL;
    }
    
    echo $jsonStr;