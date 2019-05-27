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
    $oldpw = '';
    $newpw = '';
    $sid = '';
    $aResult = array();
    
    if ( defined('STDIN') ) {
        if ( $cpwDebug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
        }
        
        $userid = $argv[1];
        $pw = $argv[2];
        $oldpw = $argv[3];
        $newpw = $argv[4];
        $sid = $argv[5];
        
    } else {
        if ( $cpwDebug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        //if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        //if ( isset($_POST['pw']) ) $pwhash = cleanString($_POST['$pw']);
        //if ( isset($_POST['oldpw']) ) $pwhash = cleanString($_POST['$oldpw']);
        //if ( isset($_POST['newpw']) ) $pwhash = cleanString($_POST['$newpw']);
        //if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        
        $userid = $_POST['userid'];
        $pw = $_POST['pw'];
        $oldpw = $_POST['oldpw'];
        $newpw = $_POST['newpw'];
        $sid = $_POST['sessionid'];
        
        if ( $cpwDebug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pw Parameter $pw" . PHP_EOL;
            print "1z - oldpw Parameter $oldpw" . PHP_EOL;
            print "1z - newpw Parameter $newpw" . PHP_EOL;
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
    if ( strlen($oldpw) == 0 ) {
        print "03 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR: '] = '3 - missing login information';
        $jsonStr = json_encode($aResult);
        echo $jsonStr;
        $failed = 1;
    }
    if ( strlen($newpw) == 0 ) {
        print "04 ERROR: missing login information." . PHP_EOL;
        $aResult['ERROR: '] = '4 - missing login information';
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
        
        //********************************************
        $_SESSION['SUCCESS'] = '-1';
        $_SESSION['pwhash'] = sha1($newpw);
        $_SESSION['memberhash'] = sha1($userid);
        //********************************************
        
        $_SESSION['userid'] = $userid;
        $_SESSION['newpw'] = $newpw;
        $_SESSION['pw'] = $pw;
        $_SESSION['oldpw'] = $oldpw;
        
        $sql = "Update `Member` set `MemberPassWord` = '$newpw' where `FromEmail` = '$userid' ";
        $_SESSION['MYSQL'] = $sql;
        
        if ( $cpwDebug == 1 ) {
            print ">>MYSQL: " . PHP_EOL . $sql . PHP_EOL;
        }
        
        //$DBF->LogIt('==> chgPw SQL: ' . $sql);
        
        if ( $conn->query($sql) === true ) {
            //$DBF->LogIt('==> chgPw SUCCESS');
            $_SESSION['SUCCESS'] = '1';
        } else {
            $_SESSION['ERROR'] = '54Q : password change failed -> ' . mysqli_error($conn);
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