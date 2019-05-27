<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    //****************************************************************************************************************
    
    //select * from UploadedFiles where FileName = 'chrome.dll';
    //select * from UploadedFiles where FileName = 'chrome.dll' ;
    //select count(*) from MemberFiles where FileID in (select FileID from UploadedFiles where FileName = 'chrome.dll');
    //select * from MemberFiles where FileID in (select FileID from UploadedFiles where FileName = 'chrome.dll' and ToEmail = 'dale');
    //select * from FileKeys where FileName = 'chrome.dll';
    //select * from enckey  where FileName = 'chrome.dll';
    
    //select PendingDownLoadCount from UploadedFiles where FileName = 'InstrumentLanding.jpg' ;
    /* GET decrement remaining download count */
    //update UploadedFiles set PendingDownLoadCount = PendingDownLoadCount-1 where FileName = 'InstrumentLanding.jpg' ;
    
    /* GET increment remaining download count */
    //update UploadedFiles set PendingDownLoadCount = PendingDownLoadCount + 1 where FileName = 'InstrumentLanding.jpg' ;
    
    include_once 'global.php';
    include_once 'dbFuncs.php';
    
    $qDebug = 0;
    
    $DbFunc = new dbFuncs;
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $FileUserID = '';
    $Files = '';
    $ListOfFiles = '';
    $success = false;
    
    $startms = microtime();
    
    if ( defined('STDIN') ) {
        if ( $qDebug == 1 ) {
            print "Executing via STDIN - debug = 1:" . PHP_EOL;
        }
        
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $FileUserID = $argv[4];
        $Files = $argv[5];
        
        if ( $qDebug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
            print "Arg1x - userid $argv[1]" . PHP_EOL;
            print "Arg2x - pwhash $argv[2]" . PHP_EOL;
            print "Arg3x - sid $argv[3]" . PHP_EOL;
            print "Arg4x - FileUserID $argv[4]" . PHP_EOL;
            print "Arg5x - Files $argv[5]" . PHP_EOL;
        }
    } else {
        if ( $qDebug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sid'];
        $FileUserID = $_POST['FileUserID'];
        $Files = $_POST['Files'];
        $stms = $_POST['startms'];
    }
    
    if ( $qDebug == 1 ) print '@00 Files: ' . $Files . PHP_EOL;
    
    if ( !isset($Files) ) {
        echo "ERROR: _POST['Files'] missing, aborting." . PHP_EOL;
        
        return -1;
    }
    
    if ( !preg_match('/^[-a-z0-9_][-a-z0-9_.]*$/i', $Files) ) {
        print ('Files Format error, continuing...' . $Files) . PHP_EOL;
    }
    if ( $qDebug == 1 ) print 'vaporize: 00' . PHP_EOL;
    
    if ( $qDebug == 1 ) {
        print '1: ' . PHP_EOL . $global_SvrName . PHP_EOL;
        print '2: ' . $global_dbname . PHP_EOL;
        print '3: ' . $global_pass . PHP_EOL;
        print '4: ' . $global_user . PHP_EOL;
        print '5: ' . $userid . PHP_EOL;
    }
    
    $ListOfFiles = explode("|", $Files);
    
    //print_r ($ListOfFiles);
    
    $DbFunc->setDbservername($global_SvrName);
    $DbFunc->setDbname($global_dbname);
    $DbFunc->setDbpassword($global_pass);
    $DbFunc->setDbusername($global_user);
    $DbFunc->setCurrUser($userid);
    //$conn = $DbFunc->getConn ();
    $conn = '';
    
    try {
        $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
        $DbFunc->setDbConn();
        if ( $conn != null ) {
            if ( $qDebug == 1 ) {
                print '-------------------------------------------' . PHP_EOL;
                print 'CONNECTION Established...' . PHP_EOL;
                print '-------------------------------------------' . PHP_EOL;
            }
        }
    }
    catch ( Exception $e ) {
        echo 'Failed to open DB Caught exception: ', $e->getMessage(), "\r\n";
    }
    
    echo 'Checking creds on: ' . $userid . ' / ' . $pwhash . PHP_EOL;
    $rc = checkCreds($userid, $pwhash, $conn);
    
    if ( $rc <= 0 ) {
        ECHO "ZA1 CREDS failed, aborting...." . PHP_EOL;
        
        return -1;
    }
    
    $UploadPath = $global_uploaddir;
    $EncryptionPath = $global_uploadencrypteddir;
    $EncPath = $global_uploadencrypteddir;
    
    /** 1- See how many DOWNLOADS are left on this file ** /
     *
     * /** 2- If the downloads remaing is zero, remove the encrypted FILE, delete the Decrypted file ** /
     *
     * /** 3- If the downloads remaing is > zero, DECREMENT the remaining downloads, delete the Decrypted file ** /
     *
     * /** DELETED THE ENCRYPTED FILE **/
    
    $target = $UploadPath . $Files;
    $DecTarget = $UploadPath . 'Decrypted/' . $Files;
    $EncTarget = $EncryptionPath . '/' . $Files . '.ENC';
    
    if ( $qDebug == 1 ) {
        print "** UploadPath = : " . $UploadPath . PHP_EOL;
        print "** EncryptionPath = : " . $EncryptionPath . PHP_EOL;
        print "** Files = : " . $Files . PHP_EOL;
        print "** target = : " . $target . PHP_EOL;
        print "** EncTarget = : " . $EncTarget . PHP_EOL;
    }
    
    //if ( file_exists($target) ) {
    //    if ( $qDebug == 1 ) print "merge 03 file exists: $target " . PHP_EOL;
    //    unlink($target);
    //}
    
    $source = $target;
    $dest = $EncTarget;
    
    $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
    
    foreach ( $ListOfFiles as &$Files ) {
        
        if ( $qDebug == 1 ) print ("==> Processing File: $Files") . PHP_EOL;
        
        /****************** 1- See how many DOWNLOADS are left on this file ******************/
        $count = $DbFunc->getPendingFileCount($Files, $conn);
        if ( $qDebug == 1 ) print ("==> Pending #downloads: $count") . PHP_EOL;
        if ( $count <= 0 ) {
            /**************************************************************************************************************************************/
            /********* it is a FULL remove of the entire FILE from the database *********/
            $rc = $DbFunc->deleteUploadedFileByName($Files, $conn);
            /**************************************************************************************************************************************/
            $rc = $DbFunc->deleteFileKeysByName($Files, $conn);
            /**************************************************************************************************************************************/
            $rc = $DbFunc->deleteFromMemberFilesByFileID($Files, $conn);
            /**************************************************************************************************************************************/
            $rc = $DbFunc->deleteUploadedFilesByFileID($Files, $conn);
            if ( $rc > 0 ) {
                $aResult['SUCCESS'] = '1';
                $aResult['GOODRUN'] = '1';
            }
            
            /**************************************************************************************************************************************/
            /*** NOW, REMOVE THE PHYSICAL FILES FROM the MAIN, ENCRYPTED AND DECRYPTED REPOSITORIES ***********************************************/
            /**************************************************************************************************************************************/
            IF ( $rc >= 0 ) {
                if ( file_exists($target) ) {
                    LOGX("DELETING FILE : " . $target);
                    unlink($target);
                    
                } else {
                    LOGX("NOTICE : " . $target . " does not exist, cannot remove.");
                }
                if ( file_exists($DecTarget) ) {
                    LOGX("DELETING FILE : " . $DecTarget);
                    unlink($DecTarget);
                    
                } else {
                    LOGX("NOTICE : " . $DecTarget . " does not exist, cannot remove.");
                }
                if ( file_exists($EncTarget) ) {
                    LOGX("DELETING FILE : $EncTarget ");
                    unlink($EncTarget);
                } else {
                    LOGX("NOTICE : $EncTarget does not exist, cannot remove.");
                }
            }
            /**************************************************************************************************************************************/
        } else {
            LOGX("DECREMENTING FILE COUNT: " . $target);
            /********* it is a remove of the user portion of the FILE from the database *********/
            /*****decrement the remaining download count *******/
            $rc = $DbFunc->decrementFileCnt($Files, $conn);
            /*****Remove references to USER FILES for target file *******/
            $rc = $DbFunc->removeReferencesUserFiles($Files, $FileUserID, $conn);
            if ( $rc > 0 ) {
                $aResult['SUCCESS'] = '1';
                $aResult['GOODRUN'] = '1';
            }
        }
    }
    
    $endms = microtime();
    $tms = $endms - $startms;
    $aResult['svr_startms'] = $startms;
    $aResult['svr_endms'] = $endms;
    $aResult['svr_elapsedms'] = $tms;
    $aResult['rx_ms'] = $stms;
    
    //----------------------------------------------------------------------------------------------------------------------------------
    mysqli_close($conn);
    //----------------------------------------------------------------------------------------------------------------------------------
    $jsonStr = json_encode($aResult);
    echo $jsonStr;