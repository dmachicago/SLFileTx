<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    include_once 'global.php';
    include_once 'dbClass.php';
    include_once 'dbFuncs.php';
    
    $DB = new dbClass();
    $DBFunc = new dbFuncs();
    
    $UserID = 'dale';
    
    $DB->setDbservername($global_SvrName);
    $DB->setDbname($global_dbname);
    $DB->setDbpassword($global_pass);
    $DB->setDbusername($global_user);
    $DB->setCurrUser($UserID);
    
    echo 'global_SvrName: ' . $global_SvrName . PHP_EOL;
    echo 'databaseName: ' . $global_dbname . PHP_EOL;
    echo 'global_user: ' . $global_user . PHP_EOL;
    echo 'global_pass: ' . $global_pass . PHP_EOL;
    echo 'UserID: ' . $UserID . PHP_EOL;
    
    $DB->debug = 0;
    $DB->dbusername = "wmiller";
    $DB->dbpassword = "Junebug@01";
    $DB->conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    $userid = 'dean';
    $pwhash = 'welcome1!';
    $sid = 'abcdef';
    
    $bCnt = $DBFunc->ckTempCnt($userid, $pwhash, $sid, $conn);
    echo '@@ ckTempCnt bCnt: ' . $bCnt . PHP_EOL;
    
    $bCnt = $DBFunc->incrementTempPwCnt($userid, $pwhash, $sid, $conn);
    echo '@@ incrementTempPwCnt bCnt: ' . $bCnt . PHP_EOL;
    
    $bCnt = $DBFunc->zeroizeTempPwCnt($userid, $pwhash, $sid, $conn);
    echo '@@ zeroizeTempPwCnt bCnt: ' . $bCnt . PHP_EOL;