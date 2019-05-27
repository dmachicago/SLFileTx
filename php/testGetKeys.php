<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    include_once 'global.php';
    include_once 'dbFuncs.php';
    $DB = new dbFuncs();
    print "@DB: FUNCS ->" . get_class($DB) . "\n";
    
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
    $DB->gDBConn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    
    $FileName = 'Dale1975.jpg';
    echo 'FileName: ' . $FileName . PHP_EOL;
    
    $RowNbr = $DB->getMemberFilesRowID($FileName);
    echo '@@ RowNbr: ' . $RowNbr . PHP_EOL;
    
    $type = 'iv';
    $k = '';
    $k = $DB->getEncKey($RowNbr, $type);
    echo 'IV: ' . $k . PHP_EOL;
    F

$type = '-';
$DB->getEncKey($RowNbr, $type);
echo 'SK: ' . $k . PHP_EOL;