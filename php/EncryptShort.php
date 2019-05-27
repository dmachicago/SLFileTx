<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    /**
     * Created by IntelliJ IDEA.
     * User: wdale
     * Date: 3/23/2017
     * Time: 7:05 PM
     */
    
    include('aes.php');
    
    $crypt = new clsEncryption();
    
    $crypt->key = $crypt->rand_key();
    $crypt->iv = $crypt->rand_iv();
    
    $file = 'path/to/file/file.txt';
    
    $crypt->encrypt_file($file, $file . '.enc');
    
    $crypt->decrypt_file($file . '.enc', $file);