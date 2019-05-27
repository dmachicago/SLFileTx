<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    //****************************************************************************************************************
    
    $debug = 0;
    
    $included_files = get_included_files();
    if ( !in_array("cryptoFunctions.php", $included_files) ) {
        include_once 'cryptoFunctions.php';
    }
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("dbFuncs.php", $included_files) ) {
        include_once 'dbFuncs.php';
    }
    
    $DB = new dbClass;
    $DbFunc = new dbFuncs;
    
    $UserID = '';
    $pwhash = '';
    $sid = '';
    $FileName = '';
    $success = false;
    
    if ( defined('STDIN') ) {
        $FileName = $argv[1];
        if ( $debug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
            if ( $debug == 1 ) {
                print "Arg1x - FileName $argv[1]" . PHP_EOL;
                print "1x - FileName Parameter $FileName" . PHP_EOL;
            }
        }
    } else {
        if ( $debug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        $FileName = $_POST['FileName'];
    }
    
    if ( $debug == 1 ) echo '@00 $FileName: ' . $FileName;
    
    if ( !isset($FileName) ) {
        echo "ERROR: _REQUEST['FileName'] missing, aborting." . PHP_EOL;
        
        return -1;
    }
    
    if ( !preg_match('/^[-a-z0-9_][-a-z0-9_.]*$/i', $FileName) ) {
        print ('FileName Format error, continuing...') . PHP_EOL;
    }
    
    $UploadPath = $global_uploaddir;
    $EncryptionPath = $global_uploadencrypteddir;
    $ShardPath = $global_uploadsharddir;
    $EncPath = $global_uploadencrypteddir;
    
    if ( $debug == 1 ) print "merge 01: UploadPath $UploadPath" . PHP_EOL;
    if ( $debug == 1 ) print "merge 01: ShardPath $ShardPath" . PHP_EOL;
    if ( $debug == 1 ) print "merge 01: EncPath $EncPath" . PHP_EOL;
    if ( $debug == 1 ) print "merge 01: EncryptionPath $EncryptionPath" . PHP_EOL;
    
    $EncTarget = $EncPath . '.' . $FileName;
    $DecryptedTarget = $global_uploaddecrypteddir . '/' . $FileName . '.ENC';
    
    if ( $debug == 1 ) {
        print "EncTarget: " . '.' . $EncTarget;
        print "DecryptedTarget: " . '.' . $DecryptedTarget;
    }
    
    if ( file_exists($EncTarget) ) {
        if ( $debug == 1 ) print "merge 03a Encrypted found: $EncTarget, proceeding. " . PHP_EOL;
        //unlink($target);
    } else {
        echo "ERROR: encrypted file $EncTarget, not found, aborting";
        
        return -10;
    }
    if ( file_exists($DecryptedTarget) ) {
        if ( $debug == 1 ) print "merge 03b Decrypted exists: $DecryptedTarget... skipping" . PHP_EOL;
        
        return 10;
    }
    
    $source = $EncTarget;
    $dest = $DecryptedTarget;
    $key = hash('sha256', $FileName);
    echo 'Hash Key: $key' . PHP_EOL;
    $b = decryptFile($source, $dest, $key);
    if ( $b == true ) {
        return 1;
    } else {
        return -1;
    }