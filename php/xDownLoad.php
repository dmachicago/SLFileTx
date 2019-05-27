<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $fn = 'test.jpg';
    //$fn = $_GET["file"];
    //$fqn = '/uploads/WHOLE/' . $fn;
    $file = '/uploads/WHOLE/test.jpg';
    
    if ( file_exists($file) ) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        ob_clean();
        flush();
        
        readfile($file);
        exit;
    } else
        echo "ERROR: file $file does not exist, aborting.";