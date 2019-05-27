<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $debug = 0;
    header("Access-Control-Allow-Origin: *");
    
    /**
     * Define the number of blocks that should be read from the source file for each chunk.
     * For 'AES-128-CBC' each block consist of 16 bytes.
     * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
     * to read/write shorter or longer chunks.
     */
    define('FILE_ENCRYPTION_BLOCKS', 10000);
    
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
        $rc = FileEncrypt($infqn, $outfqn, $key);
        
        return $rc;
    } else if ( $action == 'd' ) {
        $rc = FileDecrypt($infqn, $outfqn, $key);
        
        return $rc;
    } else if ( $action == 'm' ) {
        $rc = FileEncryptMove($infqn, $outfqn, $key);
        
        return $rc;
    } else {
        ECHO 'ERROR: action must be supplied.';
        
        return -1;
    }
    
    function FileEncryptMove ($source, $dest, $key)
    {
        $RemoveSourceFile = 1;
        $rc = 0;
        if ( FileEncrypt($source, $dest, $key) == true ) {
            if ( file_exists($source) ) {
                if ( $RemoveSourceFile == 1 ) {
                    unlink($source);
                }
                $rc = 1;
            } else
                $rc = -5;
        } else {
            $rc = -10;
            ECHO 'ERROR FileEncryptMove: FileEncrypt failed: for ' . $source . ' / RC = ' . $rc;
        }
        
        return $rc;
    }
    
    /**
     * Encrypt the passed file and saves the result in a new file with ".enc" as suffix.
     *
     * @param string $source Path to file that should be encrypted
     * @param string $key The key used for the encryption
     * @param string $dest File name where the encryped file should be written to.
     *
     * @return string|false  Returns the file name that has been created or FALSE if an error occured
     */
    function FileEncrypt ($source, $dest, $key)
    {
        $key = substr(sha1($key, true), 0, 16);
        $iv = openssl_random_pseudo_bytes(16);
        
        $error = false;
        if ( $fpOut = fopen($dest, 'w') ) {
            // Put the initialzation vector to the beginning of the file
            fwrite($fpOut, $iv);
            if ( $fpIn = fopen($source, 'rb') ) {
                while ( !feof($fpIn) ) {
                    $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
                    $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    // Use the first 16 bytes of the ciphertext as the next initialization vector
                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $ciphertext);
                }
                fclose($fpIn);
            } else {
                $error = true;
            }
            fclose($fpOut);
        } else {
            $error = true;
        }
        
        return $error ? false : $dest;
    }
    
    /**
     * To decrypt files that have been encrypted with the above function you can use this function.
     * Decrypt the passed file and saves the result in a new file, removing the
     * last 4 characters from file name.
     *
     * @param string $source Path to file that should be decrypted
     * @param string $key The key used for the decryption (must be the same as for encryption)
     * @param string $dest File name where the decryped file should be written to.
     *
     * @return string|false  Returns the file name that has been created or FALSE if an error occured
     */
    function decryptFile ($source, $dest, $key)
    {
        $key = substr(sha1($key, true), 0, 16);
        
        $error = false;
        if ( $fpOut = fopen($dest, 'w') ) {
            if ( $fpIn = fopen($source, 'rb') ) {
                // Get the initialzation vector from the beginning of the file
                $iv = fread($fpIn, 16);
                while ( !feof($fpIn) ) {
                    // we have to read one block more for decrypting than for encrypting
                    $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1));
                    $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    // Use the first 16 bytes of the ciphertext as the next initialization vector
                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $plaintext);
                }
                fclose($fpIn);
            } else {
                $error = true;
            }
            fclose($fpOut);
        } else {
            $error = true;
        }
        
        return $error ? false : $dest;
    }
    }