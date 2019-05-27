<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */
    
    $fileName = basename($_GET['file']);
    $filePath = 'files/' . $fileName;
    if ( !empty($fileName) && file_exists($filePath) ) {
        <?
        php
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $_GET['file']);
        header("Content-Transfer-Encoding: binary ");
        exit;
    } else {
        echo 'ERROR: The file does not exist.';
    }