<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    //Test Protocol:
    //php testMemberFileInsert.php
    
    include_once 'dbFuncs.php';
    $DB = new dbFuncs();
    print "@DB: FUNCS ->" . get_class($DB) . "\n";
    
    $DB->debug = 1;
    $DB->dbusername = "wmiller";
    $DB->dbpassword = "Junebug@01";
    $DB->conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    $DB->gDBConn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    $ToEmails = 'dean|mr|mj|dale|mr';
    $ToEmailArray = explode('|', $ToEmails);

//Filename and FileID must be set to match the Uploaded Files Table.
    $filename = 'dance001.png';
    $FileID = 29;
    
    $FromEmail = 'dale';
    $segmentNbr = '1';
    
    $DB->insertMemberFiles($filename, $FileID, $FromEmail, $ToEmailArray, $segmentNbr);