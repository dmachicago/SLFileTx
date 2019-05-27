<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $included_files = get_included_files();
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    
    function killSession ($pUserid)
    {
        $rc = -1;
        if ( isset($_SESSION[$pUserid]) ) {
            $_SESSION = array();
            unset($_SESSION);
            session_destroy();
            //echo "session destroyed...";
            $rc = 1;
        }
        
        return $rc;
    }
    
    function setSessionID ($pUserid, $conn)
    {
        
        $debug = 0;
        $sid = session_id();
        if ( $sid ) {
            echo "Yes: no need to call session_start since ID is " . $sid;
            exit;
        } else {
            session_start();
            $sid = session_id();
        }
        
        if ( session_id() === "" ) {
            session_start();
        }
        
        $DB = new dbClass();
        
        if ( $DB->getGDBSid() != null ) {
            return $DB->getGDBSid();
        }
        
        $global_sessionID = $sid;
        $sessionID = $sid;
        
        $Guid2 = guidv4();
        $GuidID = guidv4();
        $today = date("Y-m-d H:i:s");  // 2001-03-10 17:16:18 (the MySQL DATETIME format)
        
        if ( $debug == 1 ) {
            echo 'setSessionID -> SESSION Status: "' . session_status() . '"' . PHP_EOL;
            echo 'setSessionID -> PHP_SESSION_NONE: "' . PHP_SESSION_NONE . '"' . PHP_EOL;
        }
        
        $DB->setGDBSid($global_sessionID);
        $IV = substr(guidv4(), 1, 16);
        $SK = substr(guidv4(), 1, 16);
        
        if ( $debug == 1 ) echo 'setSessionID -> SESSIONID: "' . $global_sessionID . '"' . PHP_EOL;
        
        $pUserid = removeTrailingComma($pUserid);
        
        $sql = "delete from SessionKey where EmailAddr = '" . $pUserid . "'";
        if ( mysqli_query($conn, $sql) ) {
            if ( $debug == 1 ) echo "setSessionID -> Member SessionID reset... " . PHP_EOL;
        } else {
            if ( $debug == 1 ) echo "setSessionID -> Error: " . $sql . mysqli_error($conn);
        }
        
        $sql = "INSERT INTO SessionKey (SessionID, CreateDate, EmailAddr, GuidID, IV, SecretKey)
        VALUES ('" . $global_sessionID . "','" . $today . "','" . $pUserid . "','" . $GuidID . "','" . $IV . "','" . $SK . "')";
        
        if ( $debug == 1 ) echo 'setSessionID -> SESSION MySql: ' . PHP_EOL . $sql . PHP_EOL;
        
        if ( mysqli_query($conn, $sql) ) {
            /* now registering a session for an authenticated user */
            $_SESSION['SessionID'] = $sid;
            $_SESSION['IV'] = $IV;
            $_SESSION['SK'] = $SK;
            if ( $debug == 1 ) echo "SESSION ID Saved... " . $global_sessionID . PHP_EOL;
        } else {
            if ( $debug == 1 ) echo "ERROR SQL: " . $sql . mysqli_error($conn);
            $_SESSION['ERROR SQL'] = $sql;
        }
        //mysqli_close($conn);
        
        //$Results = json_encode($_SESSION);
        //return $Results ;
        $_SESSION['global_sessionID'] = $global_sessionID;
        
        $_SESSION['generated_sessionID'] = $sessionID;
        $_SESSION['$GuidID'] = $GuidID;
        
        if ( $debug == 1 ) echo 'global_sessionID: ' . $global_sessionID . PHP_EOL;
        
        return $global_sessionID;
    }
    
    
    function genUniqueNbr ()
    {
        $m = microtime(true);
        sprintf("%8x%05x\n", floor($m), ($m - floor($m)) * 1000000);
        
        return $m;
    }
    
    function setAdminSessionID ($pUserid, $conn)
    {
        
        $debug = 0;
        //$sid = getGUID();
        $sid = genUniqueNbr();
        $DB = new dbClass();
        
        if ( $debug == 1 ) echo 'setAdminSessionID SESSIONID: "' . $sid . '"' . PHP_EOL;
        
        $pUserid = removeTrailingComma($pUserid);
        
        $sql = "delete from SessionKeyAdmin where FromEmail = '" . $pUserid . "'";
        if ( mysqli_query($conn, $sql) ) {
            if ( $debug == 1 ) echo "setAdminSessionID reset... " . PHP_EOL;
        } else {
            if ( $debug == 1 ) echo "Error: " . $sql . mysqli_error($conn);
            $sid = '';
        }
        
        $sql = "INSERT INTO SessionKeyAdmin (SessionID, CreateDate, FromEmail)
        VALUES ('" . $sid . "', NOW() ,'" . $pUserid . "')";
        
        if ( $debug == 1 ) echo 'SessionKeyAdmin MySql: ' . $sql . PHP_EOL;
        
        if ( mysqli_query($conn, $sql) ) {
            /* now registering a session for an authenticated user */
            $_SESSION['AdminSessionID'] = $sid;
            if ( $debug == 1 ) echo "SessionKeyAdmin ID Saved... " . $sid . PHP_EOL;
        } else {
            if ( $debug == 1 ) echo "SessionKeyAdmin ERROR SQL: " . $sql . mysqli_error($conn);
            $_SESSION['ERROR SQL'] = $sql;
        }
        
        $_SESSION['AdminSessionID'] = $sid;
        
        if ( $debug == 1 ) echo '***>> global_sessionID: ' . $sid . PHP_EOL;
        
        return $sid;
    }

?>