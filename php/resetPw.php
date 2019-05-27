<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    header("Access-Control-Allow-Origin: *");
    
    include_once 'dbClass.php';
    include_once 'dbFuncs.php';
    include_once 'Encrypt.php';
    include_once 'global.php';
    
    $cpwDebug = 0;
    
    $userid = '';
    $pw = '';
    $MemberID = '';
    $sid = '';
    $aResult = array();
    
    $newpw = "Welcome1!";
    
    if ( defined('STDIN') ) {
        if ( $cpwDebug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
        }
        
        $userid = $argv[1];
        $pw = $argv[2];
        $pwhash = $argv[3];
        $MemberID = $argv[4];
        $sid = $argv[5];
        
    } else {
        if ( $cpwDebug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
		$MemberID= $_POST['MemberID'];
        $userid = $_POST['userid'];
        $pw = $_POST['pw'];
        $oldpw = $_POST['MemberID'];
        $sid = $_POST['sessionid'];
        $pwhash = $_POST['pwhash'];
        
        if ( $cpwDebug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pw Parameter $pw" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - MemberID Parameter $MemberID" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
        }
    }
    
    $DB = new dbClass();
    $DBF = new dbFuncs();
    
    if ( $cpwDebug == 1 ) {
        print "DB: CLASS ->" . get_class($DB) . "\n";
        print "DB: DBFunc ->" . get_class($DBF) . "\n";
    }
    
    $failed = 0;
    if ( strlen($userid) == 0 ) {
        print "01 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR: '] = '1 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    if ( strlen($pw) == 0 ) {
        print "02 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR: '] = '2 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    
    if ( strlen($sid) == 0 ) {
        print "05 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR: '] = '5 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    
    if ( $failed == 1 ) {
        return;
    }
    
    $DB->dbuser = $userid;
    $DB->dbpass = $pw;
    $DB->dbSid = $sid;
    
    if ( $cpwDebug == 1 ) print $global_host . ' @ ' . $global_user . ' @ ' . $global_pass . ' @ ' . $global_dbname . PHP_EOL;
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    if ( !$conn ) {
        if ( $cpwDebug == 1 ) {
            print "LOGIN ERROR " . mysqli_error();
            //$DBF->LogIt("LOGIN ERROR " . mysqli_error());
        }
        $aResult['ERROR: '] = mysqli_error();
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        
        return;
    } else {
        if ( $cpwDebug == 1 ) {
            print "LOGIN Successful" . PHP_EOL;
        }
    }
    
    if ( $cpwDebug == 1 ) {
        print $userid . ' / ' . $pw . PHP_EOL;
        print 'DB->dbuser:' . $DB->dbuser . PHP_EOL;
    }
    
    $rc = checkCreds($userid, $pw, $conn);
    
    if ( $rc == 1 ) {
        if ( $cpwDebug == 1 ) {
            $DBF->LogIt('chgPw CREDS PASSED.......');
            print "checkCreds: rc = {" . $rc . '}' . PHP_EOL;
            print "checkCreds: {" . $userid . '/' . $pw . '}' . PHP_EOL;
        }
    } else {
        $DBF->LogIt('chgPw CREDS failed.......');
        print ('chgPw CREDS failed.......');
        print "checkCreds: {" . $userid . '/' . $pw . '}' . PHP_EOL;
    }
    
    if ( $rc == 0 ) {
        mysqli_close($conn);
        if ( $cpwDebug == 1 ) print 'Member validation failed.' . PHP_EOL;
        //$DBF->LogIt('chgPw Member validation failed.');
        $_SESSION['ERROR'] = 'Member vaildation failed.';
        $_SESSION['SUCCESS'] = '0';
        $Obj = json_encode($_SESSION);
        if ( $cpwDebug == 1 ) print  "jsonStr = " . $Obj . PHP_EOL;
        echo $Obj;
        
        return;
    } else {
        
        $rc = isAdmin($userid, $pwhash, $conn);
        if ( $rc == 0 ) {
            if ( $cpwDebug == 1 ) echo "*** LOGIN ERROR resetPw ... Not Admin <$rc>" . PHP_EOL;
            LOGX("resetPw LOGIN ERROR... Not Admin <$rc> @ <$userid>");
            $aResult['ERROR'] = "No ADMIN";
            $str = json_encode($aResult);
            echo $str;
            
            return;
        } else {
            if ( $cpwDebug == 1 ) echo "*** isAdmin CHECK..." . PHP_EOL;
            LOGX("isAdmin CHECK @ <$userid>");
        }
        
        //********************************************
        $_SESSION['SUCCESS'] = '-1';
        $_SESSION['pwhash'] = sha1("Welcome1!");
        $_SESSION['memberhash'] = sha1($userid);
        //********************************************
        
        $_SESSION['userid'] = $userid;
        $_SESSION['pw'] = $pw;
        $_SESSION['MemberID'] = $MemberID;
        
        
        $sql = "Update `Member` set `MemberPassWord` = '$newpw' where `FromEmail` = '$MemberID' ";
        if ( $cpwDebug == 1 ) print $sql . PHP_EOL;
        
        $_SESSION['MYSQL'] = $sql;
        
        //$DBF->LogIt('==> chgPw SQL: ' . $sql);
        
        if ( $conn->query($sql) === true ) {
            //$DBF->LogIt('==> chgPw SUCCESS');
            $_SESSION['SUCCESS'] = '1';
        } else {
            $_SESSION['ERROR'] = '54Q : password RESET failed -> ' . mysqli_error($conn);
            $_SESSION['SUCCESS'] = '-2';
            //$DBF->LogIt('==> chgPw Failed: ' . mysqli_error($conn));
            echo mysqli_errno($conn) . ": " . mysqli_error($conn) . "\n";
        }
        
        if ( is_object($conn) && get_class($conn) == 'mysqli' ) {
            if ( $conn_thread = mysqli_thread_id($conn) ) {
                $conn->kill($conn_thread);
            }
            $conn->close();
        }
        
        $jsonStr = json_encode($_SESSION);
        if ( $cpwDebug == 1 ) print  "jsonStr = " . $jsonStr . PHP_EOL;
        
        echo $jsonStr;
    }