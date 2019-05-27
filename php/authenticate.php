<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $included_files = get_included_files();
    if ( !in_array("global.php", $included_files) ) {
        include_once 'global.php';
    }
    if ( !in_array("sessions.php", $included_files) ) {
        include_once 'sessions.php';
    }
    if ( !in_array("crypto.php", $included_files) ) {
        include_once 'crypto.php';
    }
    if ( !in_array("dbClass.php", $included_files) ) {
        include_once 'dbClass.php';
    }
    
    class authenticate
    {
        public $CONSTANT = null;
        public $DB = new dbClass();
        private $C = null;
        private $auth_DBHost = null;
        private $auth_DBUser = null;
        private $auth_DBPass = null;
        private $auth_DBName = null;
        private $auth_DBRoomName = null;
        private $auth_DBResults = array();
        private $auth_DBConn = null;
        private $auth_DBSid = null;
        private $dbservername = "";
        private $dbusername = "";
        private $dbpassword = "";
        private $dbname = "";
        private $CurrUser = "";
        private $CurrPw = "";
        private $CurrSessionid = "";
        private $validUser = 0;
        private $RC = 0;
        private $conn = null;
        
        public function getAuthCreds ($jsonstr)
        {
            
        }
        
        public function setConn ($Connection)
        {
            $this->conn = $Connection;
        }
        
        function isValidUserID ()
        {
            $rc = $DB->ckVars();
            if ( $rc < 1 ) return -100;
            
            $rc = $DB->ckSID($CurrSessionid, $CurrUser, $conn);
            if ( $rc < 0 ) return -110;
            
            $row = null;
            $count = null;
            $sql = "SELECT count(*) as CNT  /n";
            $sql .= "FROM Member  /n";
            $sql .= "where FromEmail = '" . $this->currUser . "'";
            
            $QryResults = mysqli_query($this->$auth_DBConn, $sql);
            if ( mysqli_num_rows($QryResults) > 0 ) {
                $count = $QryResults["CNT"];
            } else {
                $count = 0;
            }
            
            return $count;
        }
        
    }

?>