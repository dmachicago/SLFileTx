<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    function HashFile ($source)
    {
        $hash = hash_file('crc32b', $source);
        
        return $hash;
    }
    
    function FileEncryptMove ($source, $dest, $key)
    {
        $rc = 0;
        if ( FileEncrypt($source, $dest, $key) == true ) {
            if ( file_exists($source) ) {
                unlink($source);
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
    
    //To decrypt files that have been encrypted with the above function you can use this function.
    /**
     * Dencrypt the passed file and saves the result in a new file, removing the
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