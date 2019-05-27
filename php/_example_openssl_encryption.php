<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    define('AES_256_CBC', 'aes-256-cbc');
    $encryption_key = openssl_random_pseudo_bytes(32);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $data = "Here's some data to encrypt!";
    echo "Before encryption: $data " . PHP_EOL;
    $encrypted = openssl_encrypt($data, AES_256_CBC, $encryption_key, 0, $iv);
    echo "Encrypted: $encrypted " . PHP_EOL;
    $encrypted = $encrypted . ':' . $iv;
    $parts = explode(':', $encrypted);
    $decrypted = openssl_decrypt($parts[0], AES_256_CBC, $encryption_key, 0, $parts[1]);
    echo "Decrypted: $decrypted" . PHP_EOL;
?>