<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    /*  USE:
        $str = "My secret String";
        $Encryptor = new Encryption;
        $encoded = $Encryptor->encode($str);
        $decoded = $Encryptor->decode($encoded);
        echo "$encoded<p>$decoded";
    */
    
    class Encryption
    {
        
        var $skey = "yourSecretKey"; // you can change it
        
        public function encode ($value)
        {
            if ( !$value ) {
                return false;
            }
            $text = $value;
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
            
            return trim($this->safe_b64encode($crypttext));
        }
        
        public function safe_b64encode ($string)
        {
            $data = base64_encode($string);
            $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
            
            return $data;
        }
        
        public function decode ($value)
        {
            if ( !$value ) {
                return false;
            }
            $crypttext = $this->safe_b64decode($value);
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
            
            return trim($decrypttext);
        }
        
        public function safe_b64decode ($string)
        {
            $data = str_replace(array('-', '_'), array('+', '/'), $string);
            $mod4 = strlen($data) % 4;
            if ( $mod4 ) {
                $data .= substr('====', $mod4);
            }
            
            return base64_decode($data);
        }
    }

?>