<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $file = '/uploads/WHOLE/BarkersFrontPorch.JPG';
    $file = '/uploads/WHOLE/FileZilla.logXXX.txt';
    
    $readasbinary = 2;
    
    if ( $readasbinary == 1 ) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        echo("FILE SIZE: " . filesize($file));
        
        $filename = $file;
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        //$byteArray = unpack("N*",$contents);
        
        ob_clean();
        flush();
        
        echo $contents;
    } elseif ( $readasbinary == 2 ) {
        if ( file_exists($file) ) {
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
        }
    } else {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        $UseShard = 1;
        
        if ( $UseShard == 0 ) {
            ob_clean();
            flush();
            readfile($file);
            exit;
        } else {
            print ('UseShard == 1:') . "\n";
            if ( file_exists($file) ) {
                $CHUNK_SIZE = 1024 * 1024;
                if ( false !== ($handler = fopen($file, 'r')) ) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    
                    header("Content-Type: application/download"); //ADDED by wdm 3/28/2017
                    
                    header('Content-Disposition: attachment; filename=' . basename($file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file)); //Remove
                    
                    print "FileSize = " . filesize($file);
                    print "CHUNK_SIZE = " . $CHUNK_SIZE;
                    print "#CHUNKs = " . $FileSize / $CHUNK_SIZE;
                    //Send the content in chunks
                    $i = 0;
                    while ( false !== ($chunk = fread($handler, $CHUNK_SIZE)) ) {
                        $i++;
                        print "i = $i";
                        echo $chunk;
                    }
                }
                exit;
            } else {
                echo "Content error->The file $file does not exist!";
            }
        }
    }