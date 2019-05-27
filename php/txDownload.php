<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $filename = "/uploads/WHOLE/BarkersFrontPorch.JPG";
    $filesize = filesize($filename);
    print ("filesize = $filesize");
    $filename = $_SERVER['FileName'];
    
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:" . $filesize);
//    header("Content-Disposition: attachment");
    header("Content-Disposition: download");
    
    $handle = fopen($filename, "rb");
    if ( false === $handle ) {
        exit("Failed to open stream to URL");
    }
    
    $i = 0;
    while ( !feof($handle) ) {
        $i += 1;
        print ("i = $i");
        //echo fread($handle, 1024*1024*10);
        echo fread($handle, 1024 * 1024);
    }
    
    fclose($handle);