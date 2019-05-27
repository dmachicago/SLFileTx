<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    function memberLogin ($userid, $MemberPassWord, $aResult)
    {
        
        include_once 'global.php';
        include_once 'filedb.php';
        $count = 0;
        
        //$aResult['testDbAttach $userid: '] = $userid;
        //$aResult['testDbAttach $pwhash: '] = $pwhash;
        //$aResult['testDbAttach $global_dbname: '] = $global_dbname;
        
        //$conn = mysqli_connect('127.0.0.1:3307', 'wmiller', 'Junebug@01', $global_dbname);
        $conn = mysqli_connect($global_host, 'wmiller', 'Junebug@01', $global_dbname);
        if ( !$conn ) {
            $aResult['*** ERROR: '] = mysqli_error();
            $aResult['COUNT'] = $count;
        } else {
            $aResult['*** SUCCESS: '] = mysqli_error();
            $sql = "SELECT FromEmail FROM Member where FromEmail = '" . $userid . "' and MemberPassWord = '" . $MemberPassWord . "'";
            //$aResult['$sql: '] = $sql;
            $QryResults = mysqli_query($conn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $count = $count + 1;
                    //$aResult['FROMEMAIL'] = $row["FromEmail"] ;
                    //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
                }
                $aResult['COUNT'] = $count;
            } else {
                $aResult['COUNT'] = $count;
            }
        }
        
        mysqli_close($conn);
        $Results = json_encode($aResult);
        
        return $Results;
    }
    
    function testDbAttach ($u, $p, $aResult)
    {
        
        include_once 'global.php';
        include_once 'filedb.php';
        
        $userid = $u;
        $pwhash = $p;
        $host = $global_host;
        $global_dbname = "k3";
        
        $aResult['testDbAttach $userid: '] = $userid;
        $aResult['testDbAttach $pwhash: '] = $pwhash;
        
        //$link = mysqli_connect('127.0.0.1:3307', 'wmiller', 'Junebug@01', $global_dbname);
        $link = mysqli_connect($global_host, 'wmiller', 'Junebug@01', $global_dbname);
        if ( !$link ) {
            $aResult['*** ERROR: '] = mysqli_error();
            mysqli_close($link);
        } else {
            //$aResult['*** $link: '] = $link;
            $aResult['*** SUCCESS: '] = mysqli_error();
            mysqli_close($link);
        }
        
        $results = json_encode($aResult);
        
        return $results;
    }
    
    //--------------------------------------------------------------------------
    // php script for fetching data from mysql database
    //--------------------------------------------------------------------------
    function testlink ($a, $b, $c, $aResult)
    {
        $user = $a;
        $pass = $b;
        $roomname = $c;
        $global_dbname = "K3";
        
        $aResult['testlink parm $user: '] = $user;
        $aResult['testlink parm $pass: '] = $pass;
        $aResult['testlink parm $roomname: '] = $roomname;
        $aResult['testlink parm $global_dbname: '] = $global_dbname;
        
        //header('Content-Type: application/json');
        
        $results = json_encode($aResult);
        
        return $results;
    }
    
    $aResult = array();
    
    if ( !isset($_POST['functionname']) ) {
        $aResult['error'] = 'No function name!';
    }
    if ( !isset($_POST['userid']) ) {
        $aResult['error'] = 'No userid!';
    }
    if ( !isset($_POST['pw']) ) {
        $aResult['error'] = 'No pw!';
    }
    if ( !isset($_POST['RoomName']) ) {
        $aResult['error'] = 'No RoomName!';
    }
    
    $functionname = $_POST['functionname'];
    $user = $_POST['userid'];
    $pass = $_POST['pw'];
    $roomname = $_POST['RoomName'];
    
    $aResult['$functionname'] = $functionname;
    $aResult['$user'] = $user;
    $aResult['$pass'] = $pass;
    $aResult['$roomname'] = $roomname;
    
    if ( $functionname == 'testDbAttach' ) {
        $aResult['EXCUTING'] = $functionname;
        echo testDbAttach($user, $pass, $aResult);
    } else if ( $functionname == 'testlink' ) {
        $aResult['EXCUTING'] = $functionname;
        echo testlink($user, $pass, $roomname, $aResult);
    } else if ( $functionname == 'memberLogin' ) {
        $aResult['EXCUTING'] = $functionname;
        echo memberLogin($user, $pass, $aResult);
    } else {
        $aResult['ERROR'] = 'INCORRECT function name: ' . $functionname;
        echo json_encode($aResult);
    }

?>