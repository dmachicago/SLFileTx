<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    include_once 'dbClass.php';
    include_once 'Encrypt.php';
    $debug = 0;
    
    $userid = '';
    $pwhash = '';
    $sid = '';
    $aResult = array();

//var_dump($argv);
//        var_dump(isset($argv));
    
    if ( defined('STDIN') ) {
        if ( $debug == 1 ) print "Executing via STDIN." . PHP_EOL;
        $userid = $argv[1];
        $pwhash = $argv[2];
        $sid = $argv[3];
        if ( $debug == 1 ) {
            if ( $debug == 1 ) {
                print "Arg1x - User Parameter $argv[1]" . PHP_EOL;
                print "Arg2x - pwhash Parameter $argv[2]" . PHP_EOL;
                print "Arg3x - Session ID Parameter $argv[3]" . PHP_EOL;
                print "1x - User Parameter $userid" . PHP_EOL;
                print "1x - pwhash Parameter $pwhash" . PHP_EOL;
                print "1x - Session ID Parameter $sid" . PHP_EOL;
            }
        }
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        //$userid = cleanString($userid);
        if ( $debug == 1 ) {
            print "1a - User Parameter $userid" . PHP_EOL;
            print "1a - pwhash Parameter $pwhash" . PHP_EOL;
            print "1a - Session ID Parameter $sid" . PHP_EOL;
        }
    } else {
        if ( $debug == 1 ) {
            print "Executing via URL - debug = 1:" . PHP_EOL;
        }
        
        if ( isset($_POST['userid']) ) $userid = cleanString($_POST['$userid']);
        if ( isset($_POST['pwhash']) ) $pwhash = cleanString($_POST['$pwhash']);
        if ( isset($_POST['sessionid']) ) $sessionid = cleanString($_POST['$sessionid']);
        
        $userid = $_POST['userid'];
        $pwhash = $_POST['pwhash'];
        $sid = $_POST['sessionid'];
        
        if ( $debug == 1 ) {
            print "1z - User Parameter $userid" . PHP_EOL;
            print "1z - pwhash Parameter $pwhash" . PHP_EOL;
            print "1z - Session ID Parameter $sid" . PHP_EOL;
        }
        //$userid = cleanString($_POST['userid']);
        //$pwhash = cleanString($_POST['$pwhash']);
        //$sid = cleanString($_POST['$sid']);
    }
    
    $tgtdir = '/uploads';
    getDirFiles($tgtdir);
    
    function getDirFiles ($dir)
    {
        echo "getDirFiles 00: " . $dir . PHP_EOL;
        if ( is_dir($dir) ) {
            echo "getDirFiles 01" . PHP_EOL;
            if ( $dh = opendir($dir) ) {
                echo "getDirFiles 02" . PHP_EOL;
                while ( ($file = readdir($dh)) !== false ) {
                    echo "filename:" . $file;
                }
                closedir($dh);
            }
        } else {
            echo "ERROR - $dir is not a directory";
        }
    }
    
    //function ReadFile ($fqn){
    //    $filename = $fqn;
    //    $handle = fopen($filename, "rb");
    //    //$contents = fread($handle, filesize($filename));
    //    fclose($handle);
    //    return $contents ;
    //}