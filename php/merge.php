<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    //****************************************************************************************************************
    
    $xdebug = 0;
    
    $included_files = get_included_files();
    include_once 'cryptoFunctions.php';
    
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    include_once 'global.php';
    
    include_once 'dbFuncs.php';
    
    $DB = new dbClass;
    $DbFunc = new dbFuncs;
    
    $UserID = '';
    $pwhash = '';
    $sid = '';
    
    $FileName = '';
    $TotalChards = 0;
    $Action = '';
    $success = false;
    
    if ( defined('STDIN') ) {
        if ( $xdebug == 1 ) {
            print "Executing via STDIN - debug = 1:" . PHP_EOL;
        }
        
        $UserID = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        $FileName = $argv[4];
        $TotalChards = $argv[5];
        $Action = $argv[6];
        
        if ( $xdebug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
            print "Arg1x - UserID $argv[1]" . PHP_EOL;
            print "Arg2x - pwhash $argv[2]" . PHP_EOL;
            print "Arg3x - sid $argv[3]" . PHP_EOL;
            print "Arg4x - FileName $argv[4]" . PHP_EOL;
            print "Arg5x - TotalChards $argv[5]" . PHP_EOL;
            print "Arg6x - Action $argv[6]" . PHP_EOL;
        }
    } else {
        if ( $xdebug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        $UserID = $_POST['UserID'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sid'];
        $FileName = $_POST['FileName'];
        $TotalChards = $_POST['TotalChards'];
        $Action = $_POST['Action'];
    }
    
    if ( $xdebug == 1 ) echo '@00 $FileName: ' . $FileName;
    
    if ( !isset($FileName) ) {
        echo "ERROR: _REQUEST['FileName'] missing, aborting." . PHP_EOL;
        
        return -1;
    } else {
        $FileName = $FileName;
    }
    
    if ( !preg_match('/^[-a-z0-9_][-a-z0-9_.]*$/i', $FileName) ) {
        print ('FileName Format error, continuing...') . PHP_EOL;
    }
    if ( $xdebug == 1 ) print 'merge: 00' . PHP_EOL;
    
    // TotalChards must be set, and number
    if ( !isset($TotalChards) ) {
        print "TotalChards is missing, continuing." . PHP_EOL;
    }
    
    if ( !isset($Action) ) {
        print "Action is missing, aborting...." . PHP_EOL;
        ECHO "Action is missing, aborting...." . PHP_EOL;
        
        return -1;
    }
    
    if ( !preg_match('/^[0-9]+$/', $TotalChards) ) {
        print ('Suspected Index error: ' . $TotalChards);
    }
    
    print '1: ' . PHP_EOL . $global_SvrName . PHP_EOL;
    print '2: ' . $global_dbname . PHP_EOL;
    print '3: ' . $global_pass . PHP_EOL;
    print '4: ' . $global_user . PHP_EOL;
    print '5: ' . $UserID . PHP_EOL;
    
    $DbFunc->setDbservername($global_SvrName);
    $DbFunc->setDbname($global_dbname);
    $DbFunc->setDbpassword($global_pass);
    $DbFunc->setDbusername($global_user);
    $DbFunc->setCurrUser($UserID);
    $conn = '';
    
    $DbFunc->LogIt('@merge.php 01 -> processing ' . $FileName);
    
    try {
        print 'DB NAME: ' . $global_dbname . PHP_EOL;
        $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
        $DbFunc->setDbConn();
    }
    catch ( Exception $e ) {
        echo 'Failed to open DB Caught exception: ', $e->getMessage(), "\r\n";
    }
    
    echo 'from merger - creds: ' . $UserID . ' / ' . $pwhash . PHP_EOL;
    $rc = checkCreds($UserID, $pwhash, $conn);
    if ( $rc <= 0 ) {
        ECHO "CREDS failed, aborting...." . PHP_EOL;
        
        return -1;
    }
    
    $DbFunc->LogIt('@merge.php 02');
    
    $UploadPath = $global_uploaddir;
    $EncryptionPath = $global_uploadencrypteddir;
    if ( $xdebug == 1 ) print "merge 01: $UploadPath" . PHP_EOL;
    // we store chunks in directory named after filename
    if ( !file_exists($UploadPath) ) {
        mkdir($UploadPath);
    }
    
    $ShardPath = $global_uploadsharddir;
    if ( $xdebug == 1 ) print "merge 01: $ShardPath" . PHP_EOL;
    // we store chunks in directory named after filename
    if ( !file_exists($ShardPath) ) {
        mkdir($ShardPath);
    }
    
    $EncPath = $global_uploadencrypteddir;
    if ( $xdebug == 1 ) print "merge 01: $EncPath" . PHP_EOL;
    if ( !file_exists($EncPath) ) {
        mkdir($EncPath);
    }
    
    /** DELETED THE ENCRYPTED FILE **/
    if ( $Action == 'D' ) {
        if ( $xdebug == 1 ) print 'ACTION = "D".' . PHP_EOL;
        $DbFunc->LogIt('@merge.php ACTION = "D".');
        $DecryptedTarget = $global_uploaddecrypteddir . '/' . $FileName;
        $EncTarget = $global_uploadencrypteddir . '/' . $FileName . '.ENC';
        
        if ( !file_exists($EncTarget) ) {
            echo 'File DOES NOT exist: ' . $EncTarget . PHP_EOL;
            
            return -1;
        }
        
        if ( file_exists($DecryptedTarget) ) {
            echo 'Overwritting file : ' . $DecryptedTarget . PHP_EOL;
            unlink($DecryptedTarget);
        }
        
        //****************************************************************************************************
        $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
        $MySql = "select skey from enckey where FileName = '$FileName' ";
        $QryResults = mysqli_query($conn, $MySql);
        
        if ( mysqli_num_rows($QryResults) > 0 ) {
            while ( $row = mysqli_fetch_assoc($QryResults) ) {
                $k = $row["skey"];
            }
        } else {
            $k = '';
            echo "ERROR->Action D, failed to retrieve key: " . $MySql . PHP_EOL;
        }
        mysqli_close($conn);
        //****************************************************************************************************
        
        if ( $xdebug == 1 ) '@@ EncTarget file : ' . $EncTarget . PHP_EOL;
        if ( $xdebug == 1 ) '@@ DecryptedTarget file : ' . $DecryptedTarget . PHP_EOL;
        
        $source = $EncTarget;
        $dest = $DecryptedTarget;
        
        $key = $k;
        
        decryptFile($source, $dest, $key);
        echo '@@ Decrypting file : ' . $source . ' into ' . $dest . PHP_EOL;
        $DbFunc->LogIt('@@ Decrypting file : ' . $source . ' into ' . $dest);
        
        if ( file_exists($dest) ) {
            if ( $xdebug == 1 ) 'SUCCESS - Decrypted file : ' . $source . ' into ' . $dest . PHP_EOL;
        }
        
        $DecryptedFN = basename($dest);
        
        $rc = -1;
        if ( !file_exists($dest) ) {
            $rc = -1;
            if ( $xdebug == 1 ) echo 'ERROR failed to decrypt file : ' . $source . PHP_EOL;
            $DbFunc->LogIt('ERROR failed to decrypt file : ' . $source);
        } else {
            $rc = 1;
            if ( $xdebug == 1 ) echo 'Decrypted file is : ' . $dest . PHP_EOL;
            $DbFunc->LogIt('Decrypted file is : ' . $dest);
        }
        
        return $rc;
    }
    
    /** ENCRYPT & MERGE FILE **/
    if ( $Action == 'E' ) {
        
        $DbFunc->LogIt('@merge.php ACTION E');
        
        $target = $UploadPath . $FileName;
        $EncTarget = $EncryptionPath . '/' . $FileName . '.ENC';
        
        if ( $xdebug == 1 ) print "** target = : " . $target . PHP_EOL;
        
        if ( file_exists($target) ) {
            if ( $xdebug == 1 ) print "merge 03 file exists: $target " . PHP_EOL;
            $DbFunc->LogIt("merge 03 file exists: $target ");
            unlink($target);
            //file_put_contents($logfile, '!! RESET: ' . $target . "\r\n", FILE_APPEND);
        }
        
        //$logon = 0;
        //$logfile = '/logx/uploadlog.txt';
        //$x = 0;
        
        try {
            $fp = fopen($target, 'w');
            if ( $xdebug == 1 ) print "merge 04 : $target : opened for write" . PHP_EOL;
            if ( $xdebug == 1 ) $DbFunc->LogIt("merge 04 : $target : opened for write");
        }
        catch ( Exception $e ) {
            echo 'Caught exception: ', $e->getMessage(), "\r\n";
            $DbFunc->LogIt('@merge.php Caught exception: ' . $e->getMessage());
            //file_put_contents($logfile, 'ERROR 010: ' . $e->getMessage(), "\r\n", FILE_APPEND);
        }
        
        $bytes = 0;
        $writetype = 1;
        
        for ( $i = 0; $i < $TotalChards; $i++ ) {
            
            $ShardFQN = '../uploads/' . $FileName . '-' . $i;
            
            $DbFunc->LogIt('@merge.php ShardFQN: ' . $ShardFQN);
            
            $filedata = file_get_contents($ShardFQN);
            
            if ( $writetype == 0 ) $bytes = fwrite_stream($fp, $filedata); else {
                try {
                    $bytes = fwrite($fp, $filedata);
                }
                catch ( Exception $e ) {
                    echo 'Caught exception: ' . $e->getMessage() . "\r\n";
                }
            }
            
            echo 'UNLINKING: ' . $ShardFQN . PHP_EOL;
            
            $success = unlink($ShardFQN);
        }
        
        fclose($fp);
        
        $DbFunc->LogIt('merge SUCCESS = <' . $success . '> for file ' . $FileName);
        
        if ( $success == true ) {
            $source = $target;
            $dest = $EncTarget;
            $key = guidv4();
            echo 'Hash02 Key: ' . $key . PHP_EOL;
            $key = hash('sha256', $key);
            $MySql = '';
            
            $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
            
            $MySql = "insert into enckey (FileName, skey) values ('$FileName', '$key') " . PHP_EOL;
            $MySql = $MySql . "ON DUPLICATE KEY UPDATE skey = '$key' ";
            if ( mysqli_query($conn, $MySql) ) {
                $rc = 1;
                echo "- - - Inserted KEY: " . $key . PHP_EOL;
            } else {
                $rc = -1;
                echo "ERROR: Could not execute: " . $MySql . mysqli_error($conn);
            }
            mysqli_close($conn);
            //*******************************************************************************************
            
            // $key = pullCommguid($FileName) ;
            FileEncryptMove($source, $dest, $key);
            
            //$debug = 1 ;
            //$type = 'k';
            //$rownbr = $DbFunc->getMemberFilesRowID($FileName);
            //if ($debug == 1) echo '->FileName: ' . $FileName . PHP_EOL ;
            //if ($debug == 1) echo '->rownbr: ' .$rownbr. PHP_EOL ;
            //$k = $DbFunc->getEncKey($RowNbr, $type);
            //if ($debug == 1) echo '->skey: ' .$k. PHP_EOL ;
            //if ( $rownbr > 0 ) {
            //    FileEncryptMove($source, $dest, $k);
            //} else {
            //    echo 'E055 -> ERROR: failed to encrypt file < ' . $FileName . ' >';
            //}
        }
    }
    
    /**
     * @param $FileName
     *
     * @return mixed|string
     */
    function pullCommguid ($FileName)
    {
        include_once 'global.php';
        echo '***** 02 DATA: ' . ' / ' . $global_SvrName . ' / ' . $global_user . ' / ' . $global_pass . ' / ' . $global_dbname;
        
        $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
        $MySql = "select commguid from UploadedFiles where FileName = '$FileName' ";
        
        $QryResults = mysqli_query($conn, $MySql);
        //var_dump($QryResults) . PHP_EOL;
        if ( mysqli_num_rows($QryResults) > 0 ) {
            while ( $row = mysqli_fetch_assoc($QryResults) ) {
                $k = $row["commguid"];
            }
        } else {
            $k = '';
            echo "ERROR->pullCommguid: " . $MySql . PHP_EOL;
        }
        mysqli_close($conn);
        
        $k = str_replace(' ', '', $k);
        
        return $k;
    }
    
    function saveCommguid ($FileName, $key)
    {
        include_once 'global.php';
        $rc = -1;
        
        $conn = mysqli_connect($global_SvrName, $global_user, $global_pass, $global_dbname);
        $MySql = "insert into enckey (FileName, skey) values ('$FileName', '$key') ";
        //$MySql = $MySql + "ON DUPLICATE KEY UPDATE skey = '$key' " ;
        if ( mysqli_query($conn, $MySql) ) {
            $rc = 1;
        } else {
            $rc = -1;
            echo "ERROR: Could not able to execute: $MySql " . mysqli_error($conn);
        }
        mysqli_close($conn);
        
        return $rc;
    }