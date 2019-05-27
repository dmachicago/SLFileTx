<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    include_once 'global.php';
    
    /*******************************************************************/
    /** define the directory **/
    $dir = $global_uploaddir;
    
    /*** cycle through all files in the directory ***/
    foreach ( glob($dir . "*") as $file ) {
        
        /*** if file is 24 hours (86400 seconds) old then delete it ***/
        if ( time() - filectime($file) > 86400 ) {
            unlink($file);
        }
    }
    
    $dir = $global_uploaddecrypteddir;
    
    /*** cycle through all files in the directory ***/
    foreach ( glob($dir . "*") as $file ) {
        
        /*** if file is 24 hours (86400 seconds) old then delete it ***/
        if ( time() - filectime($file) > 86400 ) {
            unlink($file);
        }
    }
    
    /*******************************************************************/
    /** define the directory **/
    $dir = $global_uploaddecrypteddir;
    /*** cycle through all files in the directory ***/
    foreach ( glob($dir . "/*") as $file ) {
        
        /*** if file is 24 hours (86400 seconds) old then delete it ***/
        if ( time() - filectime($file) > 86400 ) {
            unlink($file);
        }
    }
    
    $dir = $global_uploaddecrypteddir;
    
    /*** cycle through all files in the directory ***/
    foreach ( glob($dir . "*") as $file ) {
        
        /*** if file is 24 hours (86400 seconds) old then delete it ***/
        if ( time() - filectime($file) > 86400 ) {
            unlink($file);
        }
    }