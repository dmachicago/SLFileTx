<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $debug = 0;
    header("Access-Control-Allow-Origin: *");
    //--------------------------------------------------------------------------
    // ADD Includes
    //--------------------------------------------------------------------------
    include_once 'dbClass.php';
    
    $userid = '';
    $pwhash = '';
    $sessionid = '';
    $infqn = '';
    $outfqn = '';
    $key = '';
    $action = '';
    
    //var_dump($argv);
    //var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) {
            print "Executing via STDIN." . PHP_EOL;
        }
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sessionid = $argv[3];
        $infqn = $argv[4];
        $outfqn = $argv[5];
        $key = $argv[6];
        $action = $argv[7];
        
        if ( $debug == -1 ) {
            print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
            print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
            print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
            print "Arg4x - Session ID Parameter $argv[4]" . PHP_EOL;
            print "Arg5x - Session ID Parameter $argv[5]" . PHP_EOL;
            print "Arg6x - Session ID Parameter $argv[6]" . PHP_EOL;
            print "Arg67 - Session ID Parameter $argv[7]" . PHP_EOL;
            print "1x - User Parameter $userid" . PHP_EOL;
            print "2x - pwhash Parameter $pwhash" . PHP_EOL;
            print "3x - Session ID Parameter $sessionid" . PHP_EOL;
            print "4x - Session ID Parameter $infqn" . PHP_EOL;
            print "5x - Session ID Parameter $outfqn" . PHP_EOL;
            print "6x - Session ID Parameter $key" . PHP_EOL;
            print "7x - Session ID Parameter $action" . PHP_EOL;
        }
    } else {
        if ( $debug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        if ( isset($_POST['infqn']) ) $sessionid = cleanString($_POST['$infqn']);
        if ( isset($_POST['outfqn']) ) $sessionid = cleanString($_POST['$outfqn']);
        if ( isset($_POST['key']) ) $sessionid = cleanString($_POST['$key']);
        if ( isset($_POST['action']) ) $sessionid = cleanString($_POST['$action']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sessionid = $_POST['sessionid'];
        $infqn = $_POST['infqn'];
        $outfqn = $_POST['outfqn'];
        $key = $_POST['key'];
        $action = $_POST['action'];
        
        if ( $debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "2z - pwhash Parameter $pwhash" . PHP_EOL;
            print "3z - Session ID Parameter $sessionid" . PHP_EOL;
            print "4z - Session ID Parameter $infqn" . PHP_EOL;
            print "5z - Session ID Parameter $outfqn" . PHP_EOL;
            print "6z - Session ID Parameter $key" . PHP_EOL;
            print "7z - Session ID Parameter $action" . PHP_EOL;
        }
    }
    
    /*
    $xout =1 ;
    if ($xout == 1){
        echo 'XOUT Completed.';
        return;
    }
    */
    
    $DB = new dbClass();
    if ( $debug == 1 ) {
        print "DB: CLASS ->" . get_class($DB) . "\n";
    }
    
    if ( $action == 'e' ) {
        $rc = EncryptOSSL256($userid, $pwhash, $infqn, $outfqn, $key);
        
        return $rc;
    } else if ( $action == 'd' ) {
        $rc = DecryptOSSL256($userid, $pwhash, $infqn, $outfqn, $key);
        
        return $rc;
    } else {
        ECHO 'ERROR: action must be supplied.';
        
        return -1;
    }
    
    function EncryptOSSL256 ($userid, $pwhash, $infqn, $outfqn, $key)
    {
        try {
            $rc = 1;
            $cmd = 'sudo openssl aes-256-cbc -a -salt -in ' . $infqn . ' -out ' . $outfqn . ' -e -aes256 -k ' . $key;
            echo 'EncryptOSSL256: ', $cmd;
            $output = shell_exec($cmd);
            echo "EncryptOSSL256 created $output" . PHP_EOL;
        }
        catch ( Exception $e ) {
            echo 'EncryptOSSL256 exception: ', $e->getMessage(), "\n";
            
            return -10;
        }
    }
    
    function DecryptOSSL256 ($userid, $pwhash, $infqn, $outfqn, $key)
    {
        try {
            $cmd = 'sudo openssl aes-256-cbc -d -a -salt -in ' . $infqn . ' -out ' . $outfqn . ' -e -aes256 -k ' . $key;
            echo 'DecryptOSSL256: ', $cmd;
            $output = shell_exec($cmd);
            echo 'DecryptOSSL256: ' . $output . PHP_EOL;
        }
        catch ( Exception $e ) {
            echo 'DecryptOSSL256 exception: ', $e->getMessage(), "\n";
            
            return -15;
        }
    }
