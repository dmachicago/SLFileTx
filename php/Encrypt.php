<?php
    
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    class clsEncryption
    {
        
        const CIPHER = MCRYPT_RIJNDAEL_128; // Rijndael-128 is AES
        const CIPHER256 = MCRYPT_RIJNDAEL_256; // Rijndael-256 is AES
        const MODE = MCRYPT_MODE_CBC;
        
        public $key; // needs to be 32 bytes for aes
        public $iv; // needs to be 16 bytes for aes
        /*************************************************************************************************/
        /*  BELOW ARE THE OPENSSL FUNCTIONS
        /*************************************************************************************************/
        
        /* Generates a new encrypted file */
        
        public function __construct ($key = '', $iv = '')
        {
            $this->key = $key;
            $this->iv = $iv;
        }
        
        /**
         * Performs text encryption with openssl_encrypt and returns it as a string.<br />
         * If openssl_encrypt is not available encrypts with mcrypt, if mcrypt is not available encrypts with xor
         *
         * @param string $text The text to encode
         * @param string $key [optionnal] The key to use. Default is the application key
         *
         * @return string           The encrypted string
         */
        public static function encryptToString ($text, $key = null)
        {
            // Get the application key if no key is given
            if ( $key === null ) {
                $key = self::_getKey();
            }
            // To avoid same encoded string for the same string
            $text = self::hash($text) . '~~~' . $text;
            // If zlib is active we compress the value to crypt
            if ( function_exists('gzdeflate') ) {
                $text = gzdeflate($text, 9);
            }
            // Use openssl_encrypt with PHP >= 5.3.0
            if ( Config::get('general.crypt_method', 'openssl') === 'openssl' && function_exists('openssl_encrypt') && in_array('BF-ECB', openssl_get_cipher_methods()) ) {
                $method = 'BF-ECB';
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
                
                return strtr(openssl_encrypt($text, $method, $key), '+/', '-_');
            } else {
                if ( function_exists('mcrypt_encrypt') ) {
                    $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
                    $iv = mcrypt_create_iv($size, MCRYPT_RAND);
                    $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
                    
                    return rtrim(strtr(base64_encode($crypt), '+/', '-_'), '=');
                }
            }
            // ... else encrypt with xor technique
            $n = mb_strlen($text, '8bit');
            $m = mb_strlen($key, '8bit');
            if ( $n !== $m ) {
                $key = mb_substr(str_repeat($key, ceil($n / $m)), 0, $n, '8bit');
            }
            
            return base64_encode($text ^ $key);
        }
        
        public static function encrypt_pw_iv ($data, $password, $IV, $AAD)
        {
            if ( self::useOpenSSL() ) {
                $method = self::getMethod($password);
                $encrypt = openssl_encrypt($data, $method, $password, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $IV, $tag, $AAD);
            } else {
                if ( self::useSO() ) {
                    try {
                        $cipher = \Crypto\Cipher::aes(\Crypto\Cipher::MODE_GCM, self::bitLen($password));
                        $cipher->setAAD($AAD);
                        $encrypt = $cipher->encrypt($data, $password, $IV);
                        $tag = $cipher->getTag();
                    }
                    catch ( \Exception $e ) {
                        //echo $e->getMessage();
                        return false;
                    }
                } else {
                    try {
                        list($encrypt, $tag) = AESGCM::encrypt($password, $IV, $data, $AAD);
                    }
                    catch ( \Exception $e ) {
                        //echo $e->getMessage();
                        return false;
                    }
                }
            }
            
            return $encrypt . $tag;
        }
        
        public function EncryptFile ($from, $to, $password)
        {
            $rc = 0;
            $array = array("pw" => $password, "iv" => '', "from" => $from, "to" => $to);
            
            try {
                $iv = $this->createIv();
                $data = openssl_encrypt(file_get_contents($from), $this->CIPHER256, $this->password, OPENSSL_RAW_DATA, $iv);
                file_put_contents($to, $iv . $this->password . $data);
                $array['iv'] = $iv;
                $rc = 1;
            }
            catch ( Exception $e ) {
                $rc = -1;
            }
            
            return $rc;
        }
        
        public function DecryptFile ($input_file, $output_file, $password)
        {
            $rc = 0;
            $array = array("pw" => $password, "iv" => '', "from" => $from, "to" => $to);
            try {
                $iv = $this->createIv();
                $data = openssl_decrypt(file_get_contents($from), $this->CIPHER256, $this->password, OPENSSL_RAW_DATA, $iv);
                file_put_contents($to, $iv . $this->password . $data);
                $array['iv'] = $iv;
                $rc = 1;
            }
            catch ( Exception $e ) {
                $rc = -1;
            }
            
            return $rc;
        }
        
        public function DecryptFileB64 ($input_file, $output_file, $password)
        {
            $input_file_handle = @fopen($input_file, "r");
            $output_file_handle = @fopen($output_file, 'wb');
            if ( !$input_file_handle ) {
                throw new Exception("Could not open input file");
            }
            if ( !$output_file_handle ) {
                throw new Exception("Could not open output file");
            }
            while ( !feof($input_file_handle) ) {
                //4096 bytes plaintext become 7296 bytes of encrypted base64 text
                $buffer = fread($input_file_handle, 7296);
                $decrypted_string = base64_decode($this->decrypt($buffer));
                //echo strlen($buffer).'<br>';
                fwrite($output_file_handle, $decrypted_string);
            }
            fclose($input_file_handle);
            fclose($output_file_handle);
            
            return true;
        }
        
        public function decrypt ($ciphertext)
        {
            $ciphertext = base64_decode($ciphertext);
            $plaintext = mcrypt_decrypt(self::CIPHER, $this->key, $ciphertext, self::MODE, $this->iv);
            
            return rtrim($plaintext, "\0");
        }
        
        public function encryptData2File ($data, $filename, $key, $base64)
        {
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv = openssl_random_pseudo_bytes($iv_size);
            $encryptionMethod = "aes-256-cbc";
            if ( $base64 ) {
                //if already encoded64
                if ( $encryptedMessage = bin2hex($iv) . openssl_encrypt($data, $encryptionMethod, $key, 0, $iv) ) {
                } else {
                    return false;
                }
            } else {
                //not encoded64
                if ( $encryptedMessage = bin2hex($iv) . openssl_encrypt(base64_encode($data), $encryptionMethod, $key, 0, $iv) ) {
                } else {
                    return false;
                }
            }
            //unset($data['filecyp']);
            if ( FileWorks::writeFile($filename, $encryptedMessage) === false ) {
                return false;
            } else {
                return true;
            }
        }
        
        /**
         * Encrypt/decrypt value
         *
         * @param string $action - Options: encrypt/decrypt
         * @param string $string - Value to be processed
         * @param array $arr_params
         *
         * @return string
         */
        public function sha1EncryptDecryptValue ($action, $string, array $arr_params)
        {
            $output = false;
            $encrypt_method = "AES-256-CBC";
            //are keys set?
            if ( !isset($arr_params["secret_key"]) ) {
                $arr_config = $this->getServiceLocator()->get("config");
                $arr_params = $arr_config["security"];
            }
            //end if
            $secret_key = $arr_params["secret_key"];
            $secret_iv = $arr_params["secret_iv"];
            // hash
            $key = hash('sha256', $secret_key);
            //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            if ( $action == 'encrypt' ) {
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
            } else {
                if ( $action == 'decrypt' ) {
                    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                }
            }
            
            //end if
            return $output;
        }
        
        public function encodeToken ()
        {
            $tokenObject = new stdClass();
            $tokenObject->token = mt_rand(100000);
            $tokenObject->time = time();
            $token = openssl_encrypt(json_encode($token), 'AES-128-ECB', CSRF_ENCRIPTION_KEY);
        }
        
        function EncryptDecryptB64 ($action, $string)
        {
            if ( !function_exists("openssl_encrypt") ) {
                die("openssl function openssl_encrypt does not exist");
            }
            if ( !function_exists("hash") ) {
                die("function hash does not exist");
            }
            global $encryption_key;
            $output = false;
            $encrypt_method = "AES-256-CBC";
            //echo "$encryption_key\n";
            $secret_iv = 'RgX54.Ju7h';
            // hash
            $key = hash('sha256', $encryption_key);
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            if ( $action == 'encrypt' ) {
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
            } else {
                if ( $action == 'decrypt' ) {
                    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                }
            }
            
            return $output;
        }
        
        /**
         *
         * @param string $data
         * @param string $iv
         * @param string $key
         *
         * @return string|false
         */
        public function encryptAES128 ($data, $iv, $key)
        {
            // Max. 2^32 blocks with a same key (not realistic in a web application).
            $cipherText = openssl_encrypt($data, 'AES-128-CBC', $key, true, $iv);
            unset($data, $iv, $key);
            
            return $cipherText;
        }
        
        public function encryptAES256 ($value)
        {
            $value .= '|||CWSALT' . mt_rand();
            $encryptionMethod = "AES-256-CBC";
            $secretHash = md5($this->password);
            
            return openssl_encrypt($value, $encryptionMethod, $secretHash, false, substr($secretHash, 0, 16));
        }
        
        /*************************************************************************************************/
        /*  BELOW ARE THE AES FUNCTIONS
        /*************************************************************************************************/
        
        function rand_key ($length = 32)
        {
            $key = openssl_random_pseudo_bytes($length);
            
            return $key;
        }
        
        function rand_iv ()
        {
            $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
            $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
            
            return $iv;
        }
        
        public function encrypt_file ($input_file, $output_file)
        {
            $input_file_handle = @fopen($input_file, "r");
            $output_file_handle = @fopen($output_file, 'wb');
            if ( !$input_file_handle ) {
                throw new Exception("Could not open input file");
            }
            if ( !$output_file_handle ) {
                throw new Exception("Could not open output file");
            }
            while ( !feof($input_file_handle) ) {
                $buffer = base64_encode(fread($input_file_handle, 4096));
                $encrypted_string = $this->encrypt($buffer);
                //echo strlen($encrypted_string).'<br>';
                fwrite($output_file_handle, $encrypted_string);
            }
            fclose($input_file_handle);
            fclose($output_file_handle);
            
            return true;
        }
        
        public function encrypt ($input, $times = 1)
        {
            for ( $i = 0; $i < $times; $i++ ) {
                $input = openssl_encrypt($input, $this->method, $this->key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $this->iv);
            }
            
            return $input;
        }
        
        private function encryptText ($plaintext)
        {
            // Use a random IV
            $iv = openssl_random_pseudo_bytes(16);
            
            // Use IV as first block of ciphertext
            return $iv . openssl_encrypt($plaintext, "AES-128-CBC", $this->encryption_key, OPENSSL_RAW_DATA, $iv);
        }
        
    }//class clsEncryption