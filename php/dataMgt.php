<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    /**
     * Created by IntelliJ IDEA.
     * User: wdale
     * Date: 3/18/2017
     * Time: 7:54 AM
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
    
    $DB = new dbClass;
    
    class dataMgt
    {
        
    }