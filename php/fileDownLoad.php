<?php
    
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $xdebug = 0;
    $xdebug2 = 1;
    
    if ( $xdebug == 1 ) {
        $xdebug2 = 0;
    }
    
    $included_files = get_included_files();
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("sessions.php", $included_files) ) {
        include_once 'sessions.php';
    }
    /*if (!in_array("crypto.php", $included_files)) {
        include_once 'crypto.php';
    }*/
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    header("Access-Control-Allow-Origin: *");
//--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    include_once 'dbClass.php';
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $FileName = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $xdebug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $FileName = $argv[4];
        $FileID = $argv[5];
        if ( $xdebug == 1 ) {
            if ( $xdebug == 1 ) {
                echo "Arg1 - User Parameter $argv[1]" . PHP_EOL;
                echo "Arg2 - pwhash Parameter $argv[2]" . PHP_EOL;
                echo "Arg3 - Session ID Parameter $argv[3]" . PHP_EOL;
                echo "Arg4 - FileName $argv[4]" . PHP_EOL;
                echo "Arg5 - FileName $argv[4]" . PHP_EOL;
                echo "1a - User $userid" . PHP_EOL;
                echo "1a - pwhash $pwhash" . PHP_EOL;
                echo "1a - Session ID $sid" . PHP_EOL;
                echo "1a - FileName $FileName" . PHP_EOL;
                echo "1a - FileName $FileID" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
    } else {
        if ( $xdebug == 1 ) {
            echo "Executing via URL - debug = 1:" . PHP_EOL;
            var_dump($_POST) . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        if ( isset($_POST['FileName']) ) $sessionid = cleanString($_POST['$FileName']);
        if ( isset($_POST['FileID']) ) $sessionid = cleanString($_POST['FileID']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        $FileName = $_POST['FileName'];
        $FileID = $_POST['FileID'];
        
        if ( $xdebug == 1 ) {
            echo "User Parameter $userid" . PHP_EOL;
            echo "pwhash Parameter $pwhash" . PHP_EOL;
            echo "Session ID Parameter $sid" . PHP_EOL;
            echo "FileName $FileName" . PHP_EOL;
            echo "FileID $FileID" . PHP_EOL;
        }
    }
    $aResult['FileName'] = $FileName;
    $DB = new dbClass();
    if ( $xdebug == 1 ) print "DB: CLASS -> " . get_class($DB) . PHP_EOL;
    
    if ( strlen($userid) == 0 ) {
        $aResult['ERROR'] = "ERROR x1: missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    if ( strlen($pwhash) == 0 ) {
        $aResult['ERROR'] = "ERROR x2: missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    if ( strlen($sid) == 0 ) {
        $aResult['ERROR'] = "ERROR x3: missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    if ( strlen($FileName) == 0 ) {
        $aResult['ERROR'] = "ERROR x4: missing login information.";
        echo json_encode($aResult);
        
        return;
    }
    
    if ( $xdebug == 1 ) $DB->showConstant();
    //$DB->$dbSid = $sid;
    
    $currsid = session_id();
    
    if ( $xdebug == 1 ) print 'currsid: ' . $currsid . PHP_EOL;
    
    $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
    if ( mysqli_connect_errno() ) {
        $aResult['ERROR'] = "Failed to connect to DB: " . mysqli_connect_error();
        echo json_encode($aResult);
        
        return -1;
    }
    if ( !$conn ) {
        if ( $xdebug == 1 ) print "CERT ERROR 001: " . mysqli_connect_error();
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        echo json_encode($aResult);
        
        return;
    } else {
        if ( $xdebug == 1 ) print "fileDownLoad CONNECTION SET..." . PHP_EOF;
    }
    
    if ( $xdebug == 1 ) {
        print 'fileDownLoad userid = ' . $userid . PHP_EOL;
        print 'fileDownLoad global_user = ' . $global_user . PHP_EOL;
        print 'fileDownLoad databaseName= ' . $global_dbname . PHP_EOL;
        print 'fileDownLoad sid= ' . $sid . PHP_EOL;
    }
    
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    $DB->setGDBSid($sid);
    $DB->setCurrUser($userid);
    $DB->setCurrPw($pwhash);
    $DB->setCurrSessionID($sid);
    
    //$rc = $DB->checkCreds($userid, $pwhash, $conn);
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc == 0 ) {
        if ( $xdebug == 1 ) print "LOGIN ERROR... No CREDS <$rc>" . PHP_EOL;
        $aResult['ERROR'] = "LOGIN ERROR... No CREDS <$rc>";
        echo json_encode($aResult);
        
        return;
    } else {
        if ( $xdebug == 1 ) print "CREDS CHECK GOOD...";
    }
    
    $rc = $DB->setConnection();
    if ( $rc == 0 ) {
        if ( $xdebug == 1 ) print "CONNECTION ERROR... No CREDS <$rc>" . PHP_EOL;
        $aResult['ERROR'] = "X2 CONNECTION ERROR";
        echo json_encode($aResult);
        
        return;
    } else {
        if ( $xdebug == 1 ) print "CONNECTION set...";
    }
    
    //will be downloaded as $downloadName
    $downloadName = $DB->getFileNameByID($FileID);
    
    if ( $xdebug == 1 ) print ('downloadName: ') . $downloadName . "\n";
    
    //path to your original file
    $file = "/var/www/html/SLupload/uploads/" . $downloadName;
    if ( $xdebug == 1 ) print ('Downloading FQN: ') . $file . "\n";
    
    $fSize = filesize($file);
    if ( $xdebug == 1 ) print ('FileSize: ') . $fSize . "\n";
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    
    //You can use $bitrate variable to control the speed of download for example
    //set speed to 100kb
    //set download speed in bytes
    $bitrate = 1024;
    //1024 bytes = 1Kb
    
    //how much bytes to read in a chunk
    $chunkSize = $bitrate * 1024;
    
    $maxslices = 0;
    if ( $fSize > $chunkSize ) $maxslices = $fSize / $chunkSize; else
        $maxslices = 1;
    
    $whole = floor($maxslices);
    $fraction = $maxslices - $whole;
    if ( $fraction > 0 ) $maxslices = $whole + 1;
    
    if ( $xdebug == 1 ) print ('maxslices:') . $maxslices . "\n";
    if ( $xdebug == 1 ) print ('chunkSize:') . $chunkSize . "\n";
    
    if ( $xdebug == 1 ) print ('reading file:') . "\n";
    //open file for reading in binary mode
    $handle = fopen($file, 'rb');
    
    if ( $xdebug == 1 ) print ('file opened:') . "\n";
    
    //read file chunks by chunk and send output to browser until end of file is reached
    $slice = 0;
    
    $UseShard = 0;
    
    if ( $UseShard == 0 ) {
        if ( $xdebug == 1 ) print ('UseShard == 0:') . "\n";
        ob_clean();
        flush();
        readfile($file);
        exit;
    } else {
        if ( $xdebug == 1 ) print ('UseShard == 1:') . "\n";
        while ( !feof($handle) ) {
            $slice += 1;
            if ( $slice > $maxslices ) {
                break;
            }
            
            //For production, UNCOMMENT
            $piece = fread($handle, $chunkSize);
            if ( $xdebug == 1 ) {
                print 'piece len: ' . strlen($piece) . ' / slice# :' . $slice . "\n";
            }
            
            if ( $xdebug2 == 1 ) {
                //Prepare the chunk to send back to client
                $slice = fread($handle, $chunkSize);
                $b64slice = base64_encode($slice);
                $aResult['slice'] = $b64slice;
                echo json_encode($aResult);
            }
            
            //send an application-initiated buffer - return the chunk
            //For production, UNCOMMENT
            $dothis = 0;
            if ( $dothis == 1 ) {
                if ( $xdebug2 == 1 ) {
                    ob_flush();
                }
            }
            
            //usually FastCGI has a socket buffer on its own so use flush() to send the current content.
            flush();
        }
    }
    
    print '*** DONE *** ' . PHP_EOL;
    fclose($handle);