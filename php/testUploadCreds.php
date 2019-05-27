<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    include_once 'dbFuncs.php';
    $DB = new dbFuncs();
    print "@DB: FUNCS ->" . get_class($DB) . "\n";
    
    $DB->debug = 0;
    $DB->dbusername = "wmiller";
    $DB->dbpassword = "Junebug@01";
    $DB->conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    $DB->gDBConn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    $currUserID = 'dale';
    $currPw = 'Junebug@01';
    $sessionid = 'Junebug@01';
    
    $rc = $DB->checkCreds($currUserID, $currPw);
    echo('checkCreds RC = ' . $rc);
    
    $rc = $DB->checkCreds($currUserID, $currPw);
    echo('checkCreds RC = ' . $rc);