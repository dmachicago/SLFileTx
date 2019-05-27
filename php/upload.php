<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    header("Access-Control-Allow-Origin: *");
    //--------------------------------------------------------------------------
    // ADD Includes
    //--------------------------------------------------------------------------
    
    $uDebug = 0;
    
    $included_files = get_included_files();
    
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    if ( !in_array("crypto.php", $included_files) ) {
        include_once 'crypto.php';
    }
    if ( !in_array("dbFuncs.php", $included_files) ) {
        include_once 'dbFuncs.php';
    }
    
    //if ( !in_array("aes.php", $included_files) ) {
    //    include_once 'aes.php';
    //}
    
    //    $crypt = new clsEncryption();
    //    $crypt->key = $crypt->rand_key();
    //    $crypt->iv = $crypt->rand_iv();
    
    $DB = new dbClass;
    $DbFunc = new dbFuncs;
    
    $UserID = '';
    $pwhash = '';
    $sid = '';
    
    $ResultsArray = array();
    
    //xhr . setRequestHeader("ToEmail", jsonToEmails);
    
    if ( defined('STDIN') ) {
        if ( $uDebug == 1 ) echo ("Executing via STDIN.") . PHP_EOL;
        
        $filename = $argv[1];   //$_SERVER['HTTP_X_FILE_NAME'];
        $filesize = $argv[2];   //$_SERVER['HTTP_X_FILE_SIZE'];
        $index = $argv[3];      //$_SERVER['HTTP_X_INDEX'];
        $FromEmail = $argv[4];   //$_SERVER['HTTP_FromEmail'];
        $ToEmails = $argv[5];   //$_SERVER['HTTP_ToEmail'];
        $currUserID = $argv[6]; //$_SERVER['HTTP_gUserID'];
        $currPw = $argv[7];     //$_SERVER['HTTP_gPwID '];
        $currSessionID = $argv[8];   //$_SERVER['HTTP_gSessionID'];
        $arrayOfEmails = explode(",", $ToEmails);   //explode(",", $ToEmails);
        
        $filesize2 = $argv[9];   //$_SERVER['HTTP_filesize'];
        $segmentCount = $argv[10];   //$_SERVER['HTTP_segmentCount'];
        $segmentNbr = $argv[11];   //$_SERVER['HTTP_segmentNbr'];
        $segmentSize = $argv[12];   //$_SERVER['HTTP_segmentSize'];
        $directory = $argv[13];   //$_SERVER['HTTP_directory'];
        $filehash = $argv[14];   //$_SERVER['HTTP_filehash'];
        $BYTES_PER_CHUNK = $argv[15];   //$_SERVER['HTTP_BYTES_PER_CHUNK'];
        
        if ( $uDebug == 1 ) {
            print "FROM STDIN" . PHP_EOL;
            print "filename: $filename" . PHP_EOL;
            print "filesize: $filesize" . PHP_EOL;
            print "index: $index" . PHP_EOL;
            print "FromEmail: $FromEmail" . PHP_EOL;
            print "ToEmails: $ToEmails" . PHP_EOL;
            print "currUserID: $currUserID" . PHP_EOL;
            print "currPw: $currPw" . PHP_EOL;
            print "currSessionID: $currSessionID" . PHP_EOL;
            print "arrayOfEmails: " . var_dump($arrayOfEmails) . PHP_EOL;
            print "filesize2: $filesize2" . PHP_EOL;
            print "segmentCount: $segmentCount" . PHP_EOL;
            print "segmentNbr: $segmentNbr" . PHP_EOL;
            print "segmentSize: $segmentSize" . PHP_EOL;
            print "directory: $directory" . PHP_EOL;
            print "filehash: $filehash" . PHP_EOL;
            print "BYTES_PER_CHUNK: $BYTES_PER_CHUNK" . PHP_EOL;
            
            LOGX('NEW $target len#: ' . filesize($target));
            LOGX("FROM STDIN" . PHP_EOL);
            LOGX("filename: $filename" . PHP_EOL);
            LOGX("filesize: $filesize" . PHP_EOL);
            LOGX("index: $index" . PHP_EOL);
            LOGX("FromEmail: $FromEmail" . PHP_EOL);
            LOGX("ToEmails: $ToEmails" . PHP_EOL);
            LOGX("currUserID: $currUserID" . PHP_EOL);
            LOGX("currPw: $currPw" . PHP_EOL);
            LOGX("currSessionID: $currSessionID" . PHP_EOL);
            LOGX("arrayOfEmails: " . var_dump($arrayOfEmails) . PHP_EOL);
            LOGX("filesize2: $filesize2" . PHP_EOL);
            LOGX("segmentCount: $segmentCount" . PHP_EOL);
            LOGX("segmentNbr: $segmentNbr" . PHP_EOL);
            LOGX("segmentSize: $segmentSize" . PHP_EOL);
            LOGX("directory: $directory" . PHP_EOL);
            LOGX("filehash: $filehash" . PHP_EOL);
            LOGX("BYTES_PER_CHUNK: $BYTES_PER_CHUNK" . PHP_EOL);
            
        }
    } else {
        
        if ( $uDebug == 1 ) {
            echo "Exec @ Parms 000A" . PHP_EOL;
        }
        
        if ( !isset($_SERVER['HTTP_X_FILE_SIZE']) ) {
            if ( $uDebug == 1 ) echo "Loc 000A" . PHP_EOL;
            throw new Exception('File-Size required');
        }
        
        if ( $uDebug == 1 ) echo "Loc 001" . PHP_EOL;
        if ( !isset($_SERVER['HTTP_GUSERID']) ) {
            throw new Exception('gUserID required');
        }
        if ( $uDebug == 1 ) echo "Loc 002" . PHP_EOL;
        if ( !isset($_SERVER['HTTP_GSESSIONID']) ) {
            throw new Exception('gSessionID required');
        }
        if ( $uDebug == 1 ) echo "Loc 003" . PHP_EOL;
        if ( !isset($_SERVER['HTTP_GPWID']) ) {
            throw new Exception('gPwID required');
        }
        if ( $uDebug == 1 ) echo "Loc 004" . PHP_EOL;
        if ( !isset($_SERVER['HTTP_TOEMAIL']) ) {
            throw new Exception('TO Email required');
        }
        if ( $uDebug == 1 ) echo "Loc 005" . PHP_EOL;
        if ( !isset($_SERVER['HTTP_FROMEMAIL']) ) {
            throw new Exception('From Email required');
        }
        if ( $uDebug == 1 ) echo "Loc 006" . PHP_EOL;
        if ( !isset($_SERVER['HTTP_X_FILE_NAME']) ) {
            throw new Exception('Name required');
        }
        if ( $uDebug == 1 ) echo "Loc 007" . PHP_EOL;
        if ( !preg_match('/^[-a-z0-9_][-a-z0-9_.]*$/i', $_SERVER['HTTP_X_FILE_NAME']) ) {
            throw new Exception('Name error');
        }
        if ( $uDebug == 1 ) echo "Loc 008" . PHP_EOL;
        if ( $uDebug == 1 ) echo "Loc 02" . PHP_EOL;
        
        // index must be set, and number
        if ( !isset($_SERVER['HTTP_X_INDEX']) ) {
            throw new Exception('Index required');
        }
        if ( !preg_match('/^[0-9]+$/', $_SERVER['HTTP_X_INDEX']) ) {
            throw new Exception('Index error');
        }
        
        if ( $uDebug == 1 ) echo "Loc 03" . PHP_EOL;
        $filename = $_SERVER['HTTP_X_FILE_NAME'];
        $filesize = $_SERVER['HTTP_X_FILE_SIZE'];
        $index = $_SERVER['HTTP_X_INDEX'];
        $FromEmail = $_SERVER['HTTP_FROMEMAIL'];
        $ToEmails = $_SERVER['HTTP_TOEMAIL'];
        $currUserID = $_SERVER['HTTP_GUSERID'];
        $currPw = $_SERVER['HTTP_GPWID '];
        $currSessionID = $_SERVER['HTTP_GSESSIONID'];
        //$arrayOfEmails = json_decode($ToEmails);
        $arrayOfEmails = explode(",", $ToEmails);
        
        $filesize2 = $_SERVER['HTTP_FILESIZE'];
        $segmentCount = $_SERVER['HTTP_SEGMENTCOUNT'];
        $segmentNbr = $_SERVER['HTTP_SEGMENTNBR'];
        $segmentSize = $_SERVER['HTTP_SEGMENTSIZE'];
        $directory = $_SERVER['HTTP_DIRECTORY'];
        $filehash = $_SERVER['HTTP_FILEHASH'];
        //missing
        $BYTES_PER_CHUNK = $_SERVER['HTTP_CHUNKSIZE'];
        if ( $uDebug == 1 ) echo "Loc 04" . PHP_EOL;
        if ( $uDebug == 1 ) {
            echo "FROM POST DATA:" . PHP_EOL;
            echo "filename: $filename" . PHP_EOL;
            echo "filesize: $filesize" . PHP_EOL;
            echo "index: $index" . PHP_EOL;
            echo "FromEmail: $FromEmail" . PHP_EOL;
            echo "ToEmails: $ToEmails" . PHP_EOL;
            echo "currUserID: $currUserID" . PHP_EOL;
            echo "currPw: $currPw" . PHP_EOL;
            echo "currSessionID: $currSessionID" . PHP_EOL;
            echo "arrayOfEmails: " . var_dump($arrayOfEmails) . PHP_EOL;
            echo "filesize2: $filesize2" . PHP_EOL;
            echo "segmentCount: $segmentCount" . PHP_EOL;
            echo "segmentNbr: $segmentNbr" . PHP_EOL;
            echo "segmentSize: $segmentSize" . PHP_EOL;
            echo "directory: $directory" . PHP_EOL;
            echo "filehash: $filehash" . PHP_EOL;
            echo "BYTES_PER_CHUNK: $BYTES_PER_CHUNK" . PHP_EOL;
            
            LOGX("FROM POST DATA:");
            LOGX("filename: $filename");
            LOGX("filesize: $filesize");
            LOGX("index: $index");
            LOGX("FromEmail: $FromEmail");
            LOGX("ToEmails: $ToEmails");
            LOGX("currUserID: $currUserID");
            LOGX("currPw: $currPw");
            LOGX("currSessionID: $currSessionID");
            LOGX("arrayOfEmails: " . var_dump($arrayOfEmails));
            LOGX("filesize2: $filesize2");
            LOGX("segmentCount: $segmentCount");
            LOGX("segmentNbr: $segmentNbr");
            LOGX("segmentSize: $segmentSize");
            LOGX("directory: $directory");
            LOGX("filehash: $filehash");
            LOGX("BYTES_PER_CHUNK: $BYTES_PER_CHUNK");
            
        }
    }
    
    $UserID = $FromEmail;
    $pwhash = $currPw;
    $sid = $currSessionID;
    
    if ( $uDebug == 1 ) echo "Upload 00" . PHP_EOL;
    $conn = mysqli_connect("localhost", $global_user, $global_pass, $global_dbname);
    if ( mysqli_connect_errno() ) {
        if ( $uDebug == 1 ) array_push($ResultsArray, ["Failed to connect to DB: " => mysqli_connect_error()]);
        
        return -1;
    } else {
        if ( $uDebug == 1 ) echo 'DB Connection successful...' . PHP_EOL;
    }
    
    if ( $uDebug == 1 ) echo "Upload 01" . PHP_EOL;
    if ( !$conn ) {
        if ( $uDebug == 1 ) if ( $uDebug == 1 ) array_push($ResultsArray, ["LOGIN ERROR 001: " => mysqli_connect_error()]);
        $aResult['ERROR: '] = mysqli_connect_error();
        $aResult['COUNT'] = $count;
        
        return -1;
    }
    
    if ( $uDebug == 1 ) echo "Upload 02" . PHP_EOL;
    $DB->setGDBUser($global_user);
    $DB->setGDBPass($global_pass);
    $DB->setGDBName($global_dbname);
    $DB->setGDBConn($conn);
    $DB->setGDBSid($sid);
    $DB->setCurrUser($currUserID);
    $DB->setCurrPw($currPw);
    $DB->setCurrSessionID($sid);
    
    if ( $uDebug == 1 ) echo "Upload 03" . PHP_EOL;
    
    if ( $uDebug == 1 ) echo "calling upLoad.php->checkCreds with $currUserID, $currPw" . PHP_EOL;
    
    $DbFunc->setDbservername($global_SvrName);
    $DbFunc->setCurrSessionid($sid);
    $DbFunc->setDbname($global_dbname);
    $DbFunc->setDbpassword($global_pass);
    $DbFunc->setDbusername($global_user);
    $DbFunc->setConn();
    
    $DbFunc->setSessionLastAcquisition($UserID, $pwhash, $sid);
    
    $rc = $DbFunc->checkCreds($currUserID, $currPw);
    $rc = 1;
    
    if ( $uDebug == 1 ) echo '@@ upload CkCred rc = ' . $rc . PHP_EOL;
    //**********************************GOOD HERE **********************************************
    
    //$checkit = 1 ;
    //if ($checkit == 1){
    //    echo "CHECKED TO HERE GOOD." . PHP_EOL;
    //    return;
    //}
    
    if ( $rc == 0 ) {
        echo "Upload 04 Failed Credential check" . PHP_EOL;
        throw new Exception('Upload 04 Failed Credential check');
        
        return -1;
    } else {
        if ( $uDebug == 1 ) echo "Passed CRED check." . PHP_EOL;
        if ( $uDebug == 1 ) echo "Upload 05" . PHP_EOL;
    }
    //********************************************************************************
    
    if ( $uDebug == 1 ) echo "Upload 07" . PHP_EOL;
    if ( !file_exists('/var/www/html/SLupload/uploads/logx/') ) {
        mkdir("/var/www/html/SLupload/uploads/logx/");
    }
    
    if ( $uDebug == 1 ) echo "Upload 08" . PHP_EOL;
    
    $target = "/var/www/html/SLupload/uploads/" . $filename . '-' . $index;
    $DbFunc->LogIt('--------------------- START UPLOAD of: ' . $target);
    
    $DoItThisWay = 0;
    $x = 0;
    if ( $uDebug == 1 ) echo "Upload 09 / DoItThisWay: $DoItThisWay" . PHP_EOL;
    
    if ( $DoItThisWay == 0 ) {
        if ( $uDebug == 1 ) echo "Upload 10" . PHP_EOL;
        $putdata = fopen("php://input", "r");
        $fp = fopen($target, "wb");
        
        if ( $uDebug == 1 ) echo 'Upload 11' . PHP_EOL . 'TARGET FILE: ' . $target . PHP_EOL;
        //*********************************************************************
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
        while ( $data = fread($putdata, 1024 * 32) ) {
            fwrite($fp, $data);
        }
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
        //*********************************************************************
        if ( $uDebug == 1 ) echo "Upload 12" . PHP_EOL;
        
        fclose($fp);
        fclose($putdata);
        $x = filesize($target);
        
        if ( $uDebug == 1 ) {
            echo "ZZZ filesize(target) $x" . PHP_EOL;
            echo "ZZZ Upload 13" . PHP_EOL;
        }
        
        try {
            if ( $uDebug == 1 ) echo "Upload 14" . PHP_EOL;
            LOGX('NEW $target len#: ' . filesize($target));
            
            $DbFunc->setDbservername($global_host);
            $DbFunc->setDbusername($global_user);
            $DbFunc->setDbpassword($global_pass);
            $DbFunc->setDbname($global_dbname);
            $DbFunc->setCurrUser($currUserID);
            $DbFunc->setCurrPw($currPw);
            $DbFunc->setCurrSessionid($currSessionID);
            
            if ( $uDebug == 1 ) print "call insertFileUpload 14B" . PHP_EOL;
            
            $FileID = $DbFunc->insertFileUpload($filename, $segmentCount, $segmentNbr, $segmentSize, $directory, $filehash);
            
            if ( $uDebug == 1 ) print 'Returned FileID: <' . $FileID . '>' . PHP_EOL;
            if ( $FileID > 0 ) {
                
                if ( $uDebug == 1 ) {
                    echo "ZZZ01 calling insertMemberFiles @ " . $FileID . '/' . $currUserID . '/' . $segmentNbr . PHP_EOL;
                    LOGX("ZZZ01 calling insertMemberFiles @ " . $FileID . '/' . $currUserID . '/' . $segmentNbr);
                    
                    echo "ZZZ02 insertMemberFiles @ " . var_dump($arrayOfEmails) . PHP_EOL;
                }
                
                $DbFunc->LogIt("ZZZ01 calling insertMemberFiles @ " . $FileID . '/' . $currUserID . '/' . $segmentNbr . PHP_EOL);
                $DbFunc->LogIt("ZZZ02 insertMemberFiles @ " . var_dump($arrayOfEmails) . PHP_EOL);
                
                $RowNbr = $DbFunc->insertMemberFiles($filename, $FileID, $currUserID, $arrayOfEmails, $segmentNbr);
                
                if ( $uDebug == 1 ) {
                    print '@@ 01: RowNbr: ' . $RowNbr . PHP_EOL;
                    print "ZZZ insertMemberFiles RowNbr = $RowNbr" . PHP_EOL;
                    print "Upload 17: $rc" . PHP_EOL;
                }
                
                if ( $RowNbr > 0 ) {
                    $iv = guidv4();
                    $key = guidv4();
                    if ( $uDebug == 1 ) echo "Upload 17B Keys: " . $iv . '  /  ' . $key . PHP_EOL;
                } else
                    echo 'ERROR 1x insertFileKeys failed.' . PHP_EOL;
            } else {
                echo 'ERROR 2x insertFileUpload failed.' . PHP_EOL;
                LOGX('ERROR 2x insertFileUpload failed.');
            }
            
        }
        catch ( Exception $e ) {
            echo 'ERROR 3x transfer failed: ' . $e->getMessage() . PHP_EOL;
            LOGX('ERROR 3x transfer failed: ' . $e->getMessage());
        }
    } else {
        if ( $uDebug == 1 ) echo "Upload 20: $rc" . PHP_EOL;
        $input = fopen("php://input", "r");
        $x = file_put_contents($target, $input);
    }
    LOGX('UPLOAD: $target bytes written: ' . $x);
    if ( $uDebug == 1 ) echo "Upload END" . PHP_EOL;