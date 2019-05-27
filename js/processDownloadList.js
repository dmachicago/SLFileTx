/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

var xhr = new Array();
var target = new Object();
var toDownload = {
    'file1': 'path1/',
    'file2': 'path2/',
    'file3': 'path3/'
};

function createRequestObject () {
    var xmlHttp = false;
    if ( typeof(XMLHttpRequest) != 'undefined' ) {
        xmlHttp = new XMLHttpRequest();
    }
    if ( !xmlHttp ) {
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch ( e ) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch ( e ) {
                xmlHttp = false;
            }
        }
    }
    return xmlHttp;
}


function downloadList (fileList, callback) {
    i = 0;
    for ( var key in fileList ) {
        xhr[i] = createRequestObject();
        if ( xhr[i] ) {
            xhr[i].open('GET', 'file.php?file='
                + key
                + '&path=' + fileList[key],
                true);
            xhr[i].responseType = 'arraybuffer';
            xhr[i].onreadystatechange = null;
            xhr[i].addEventListener("load", callback, false);
            xhr[i].send(null);
        }
        i++;
    }
}

downloadList(toDownload, function (result) {
    var contDisposition = this.getResponseHeader("Content-Disposition");
    if ( contDisposition !== null ) {
        var re = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/i;
        var m;
        if ( (m = re.exec(contDisposition)) !== null ) {
            if ( m.index === re.lastIndex ) {
                re.lastIndex++;
            }
            var filename = m[1].replace(/"/g, '');
        }
        if ( toDownload[filename] ) {
            target[filename] = toDownload[filename];
        }
        var blob = new Blob([this.response], {type: "application/pdf"});
        var link = document.createElement('a');
        link.download = filename;
        link.href = window.URL.createObjectURL(blob);
        link.click();
    }
    else {
        var uInt8Array = new Uint8Array(this.response);
        var str = String.fromCharCode.apply(null, uInt8Array);
        if ( toDownload[str] ) {
            target[str] = "this_file_does_not_exist";
        }
    }
    if ( Object.keys(target).length == Object.keys(toDownload).length ) {
        console.log(target);
    }
});