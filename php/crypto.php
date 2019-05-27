<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    header("Access-Control-Allow-Origin: *");
//--------------------------------------------------------------------------
// ADD Includes
//--------------------------------------------------------------------------
    
    $debug = 0;
    $Pass = "Passingword";
    $Clear = "kleartext";
    
    /*
    $crypted = fnEncrypt($Clear, $Pass);
    echo "Encrypted: ".$crypted."</br>";
    $newClear = fnDecrypt($crypted, $Pass);
    echo "Decrypted: ".$newClear."</br>";
    */
    
    /**
     * Define the number of blocks that should be read from the source file for each chunk.
     * For 'aes-256-cbc' each block consist of 16 bytes.
     * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
     * to read/write shorter or longer chunks.
     */
    define('FILE_ENCRYPTION_BLOCKS', 10000);
    
    /**
     * Encrypt A passed file and save the result in a new file with A ".enc" suffix.
     *
     * @param string $FQN fully qualified name and Path to file that will be encrypted
     * @param string $key The key used for the encryption
     * @param string $dest File name where the encryped file should be written to.
     *
     * @return string|false  Returns the file name that has been created or FALSE if an error occured
     *
     * USAGE:
     * $fileName = __DIR__.'/testfile.txt';
     * $key = 'my secret key';
     * file_put_contents($fileName, 'Hello World, here I am.');
     * encryptFile($fileName, $key, $fileName . '.enc');
     * decryptFile($fileName . '.enc', $key, $fileName . '.dec');
     * This will create three files:
     *
     * testfile.txt with the plain text
     * testfile.txt.enc with the encrypted file
     * testfile.txt.dec with the decrypted file. This should have the same content as testfile.txt
     */
    function encryptFileBuffer ($FQN, $key, $dest)
    {
        $key = substr(sha1($key, true), 0, 16);
        $iv = openssl_random_pseudo_bytes(16);
        
        $error = false;
        if ( $fpOut = fopen($dest, 'w') ) {
            // Put the initialization vector at the beginning of the file
            fwrite($fpOut, $iv);
            if ( $fpIn = fopen($FQN, 'rb') ) {
                while ( !feof($fpIn) ) {
                    $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
                    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
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
     * Decrypt the passed file and saves the result in a new file, removing the
     * last 4 characters from file name.
     *
     * @param   string $FQN fully qualified name and Path to file that will be decrypted
     * @param   string $key The key used for the decryption (must be the same as for encryption)
     * @param   string $dest File name and path where the decrypted file should be written.
     *
     * @return  string|false    Returns the file name that has been created or FALSE if an error occured
     */
    function decryptFileBuffer ($FQN, $key, $dest)
    {
        $key = substr(sha1($key, true), 0, 16);
        
        $error = false;
        if ( $fpOut = fopen($dest, 'w') ) {
            if ( $fpIn = fopen($FQN, 'rb') ) {
                // Get the initialzation vector from the beginning of the file
                $iv = fread($fpIn, 16);
                while ( !feof($fpIn) ) {
                    // we have to read one block more for decrypting than for encrypting
                    $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1));
                    $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
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
    
    function fnEncrypt ($sValue, $sSecretKey)
    {
        return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))), "\0");
    }
    
    function fnDecrypt ($sValue, $sSecretKey)
    {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)), "\0");
    }
    
    function EnCrypt ($string, $key)
    {
        
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        
        $encrypted = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), $string, MCRYPT_MODE_CBC, $iv));
        
        return $encrypted;
    }
    
    function DeCrypt ($string, $key)
    {
        $data = base64_decode($string);
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
        
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv), "\0");
        
        return $decrypted;
    }
    
    function genHash ($str)
    {
        $hash = sha1($str);
        
        return $hash;
    }
    
    function dbEncrypt ($string, $key)
    {
        
        $decrypted = '';
        $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
        if ( !$conn ) {
            $aResult['*** ERROR: '] = mysqli_error();
        } else {
            $sql = "SELECT AES_ENCRYPT('" + $string + "','" + $key + "')";
            $QryResults = mysqli_query($conn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $decrypted = $row[0];
                }
            } else {
                $decrypted = '';
            }
        }
        
        mysqli_close($conn);
        
        return $decrypted;
        
    }
    
    function dbDecrypt ($string, $key)
    {
        $decrypted = '';
        $conn = mysqli_connect($global_host, $global_user, $global_pass, $global_dbname);
        if ( !$conn ) {
            $aResult['*** ERROR: '] = mysqli_error();
        } else {
            $sql = "SELECT AES_DECRYPT('" + $string + "','" + $key + "')";
            $QryResults = mysqli_query($conn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                while ( $row = mysqli_fetch_assoc($QryResults) ) {
                    $decrypted = $row[0];
                }
            } else {
                $decrypted = '';
            }
        }
        
        mysqli_close($conn);
        
        return $decrypted;
    }

?>