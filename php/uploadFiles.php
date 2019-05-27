<?php
    /**
     * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
     * ALL rights reserved.
     */

// Output the JSON data
    function outputJSON ($msg, $status = 'error')
    {
        header('Content-Type: application/json');
        die(json_encode(array('data' => $msg, 'status' => $status)));
    }

// Check for errors
    if ( $_FILES['SelectedFile']['error'] > 0 ) {
        outputJSON('An error ocurred when uploading.');
    }
    
    if ( !getimagesize($_FILES['SelectedFile']['tmp_name']) ) {
        outputJSON('Please ensure you are uploading an image.');
    }

// Check filetype
//if($_FILES['SelectedFile']['type'] != 'image/png'){
//    outputJSON('Unsupported filetype uploaded.');
//}

// Check filesize
    if ( $_FILES['SelectedFile']['size'] > 15000000 ) {
        outputJSON('File uploaded exceeds maximum upload size of 15Mb.');
    }

// Check if the file exists
    if ( file_exists('/var/www/html/SLupload/uploads/xxxx/' . $_FILES['SelectedFile']['name']) ) {
        outputJSON('File with that name already exists.');
    }

// Upload file
    $dest = '/var/www/html/SLupload/uploads/xxxx/' . $_FILES['SelectedFile']['name'];
///$dest = '/var/www/html/SLupload/uploads/' . $_FILES['SelectedFile']['name'] ;
//if(!move_uploaded_file($_FILES['SelectedFile']['tmp_name'], 'uploads/' . $_FILES['SelectedFile']['name'])){
    if ( !move_uploaded_file($_FILES['SelectedFile']['tmp_name'], $dest) ) {
        outputJSON('Error uploading file - check destination is writeable: ' . $dest);
    }

// Success!
    outputJSON('File uploaded successfully to "' . 'upload/' . $_FILES['SelectedFile']['name'] . '".', 'success');