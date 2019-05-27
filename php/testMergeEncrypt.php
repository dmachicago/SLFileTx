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
    
    $FileID = 235;
    $FromEmail = 'dale';
    $segmentNbr = '10';
    $ToEmailArray = array('Dean', 'MR');
    
    $DB->insertMemberFiles($filename, $FileID, $FromEmail, $ToEmailArray, $segmentNbr);