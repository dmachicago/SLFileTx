/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

var _submit = document.getElementById('_submit'),
    _file = document.getElementById('_file'),
    _progress = document.getElementById('_progress');

var uploadSelectedFiles = function () {
    _progress = document.getElementById('_progress');
    _file = document.getElementById('_file');
    if ( _file == null ) {
        alert("Appears no file is selected.");
        return;
    }
    if ( _file.files.length === 0 ) {
        alert("Appears no file is selected.");
        return;
    }
    console.log('Uploading: ', _file.files[0]);
    var data = new FormData();
    data.append('SelectedFile', _file.files[0]);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if ( request.readyState == 4 ) {
            try {
                var resp = JSON.parse(request.response);
            }
            catch ( e ) {
                var resp = {
                    status: 'error',
                    data: 'Unknown error occurred: [' + request.responseText + ']'
                };
            }
            console.log(resp.status + ': ' + resp.data);
        }
    };

    request.upload.addEventListener('progress', function (e) {
        _progress.style.width = Math.ceil(e.loaded / e.total) * 100 + '%';
    }, false);

    request.open('POST', 'php/uploadFiles.php');
    request.send(data);
}

//_submit.addEventListener( 'click', upload );

function xhrFileUpload () {
}