/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

const BYTES_PER_CHUNK = 1024 * 1024; // 1MB chunk sizes.
var slices; // slices, value that gets decremented
var slicesTotal; // total amount of slices, constant once calculated

function changePw () {
    var userid = $('#pwUserid').val();
    var currpw = $('#pwPassword').val();
    var newpw = $('#pwNew').val();
    var pw2 = $('#pwNew2').val();

    if ( newpw != pw2 ) {
        alert("The proposed password is not equal to the retyped password, returning.");
        return;
    }

    alert('Calling changeMyPW.php');
}
function ckPw () {
    var userid = $('#pwUserid').val();
    var currpw = $('#pwPassword').val();
}
function recallLogin () {
    var remember = getCookie('xrememberme');
    if ( remember == '1' ) {
        var uid = getCookie('xuserid');
        $('#idUserid').val(uid);
        var xpw = getCookie('xpw');
        $('#idPassword').val(xpw);
    }
}

function saveCookie (name, txt) {
    localStorage.setItem(name, txt);
}
function getCookie (name) {
    var str = localStorage.getItem(name);
    return str;
}

function rememberMe () {
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();

    saveCookie('xuserid', userid);
    saveCookie('xpw', pw);
    saveCookie('xrememberme', '1');
}
function forgetMe () {
    localStorage.removeItem('xuserid');
    localStorage.removeItem('xpw');
    saveCookie('xrememberme', '0');
}
function hideSelect (loc) {
    var hideSelect_debug = 1;
    if ( hideSelect_debug == 1 )
        console.log('** hideSelect: ', loc);

    $("#divSend").hide();
    $("#divSelectFiles").hide();
    $("#divMyGroups").hide();
    $("#percent").hide();

    $("#divProgress").hide();
    $("#divTx").hide();

    $("#hr1").hide();
    $("#hr2").hide();
    $("#hr3").hide();

    $("#divDownload").show();
}
function hideDownload () {
    //console.log( '** hideDownload' );
    $("#divDownload").hide();

    $("#divSend").show();

    $("#hr1").show();
    $("#hr2").show();
    $("#hr3").show();

    $("#divSelectFiles").show();
    $("#divMyGroups").show();
    $("#percent").show();
    $("#divProgress").show();
    $("#divTx").show();
}
function hideAll () {
    //console.log( '** hideAll' );
    $("#divSend").hide();
    $("#divSelectFiles").hide();
    $("#divMyGroups").hide();
    $("#percent").hide();
    $("#divDownload").hide();
    $("#divProgress").hide();
    $("#divTx").hide();
    $("#hr1").hide();
    $("#hr2").hide();
    $("#hr3").hide();
}

//Calculate slices and indirectly upload a chunk of a file via uploadFile()
function SendFiles () {
    var debug = 1;
    if ( debug == 1 ) console.log('SendFiles 00 DEBUG SET ON');

    $("#footer").hide();
    $('#divTx').empty();

    var i = 0;
    var xhr;
    var filecnt;
    var arrFiles = new Array();
    var arrShards = new Array();
    var arrFileSize = new Array();
    var arrayFileHash = new Array();

    var files = document.getElementById('fileToUpload').files;
    if ( debug == 1 ) console.log('SendFiles files count: ', files.length);
    pb2.min = 0;
    pb2.max = files.length;

    for ( i = 0; i < files.length; i++ ) {
        if ( debug == 1 ) console.log('SendFiles 10');
        pb2.value = i + 1;
        var blob = document.getElementById('fileToUpload').files[i];
        var ix = blob.name.indexOf(' ');
        if ( ix < 0 ) {
            $("#divTx").append('<label id="lbl' + blob.name + '">' + blob.name + '</label>');
            $("#divTx").append('<br />');
            $("#divTx").append('<progress id="pb' + blob.name + '" value="0" max="100" style="width: 100%"></progress>');

            var start = 0;
            var end;
            var index = 0;

            // calculate the number of slices
            if ( blob.size <= BYTES_PER_CHUNK ) {
                slices = 1
            }
            else {
                slices = Math.ceil(blob.size / BYTES_PER_CHUNK);
            }
            slicesTotal = slices;

            if ( debug == 1 ) console.log('SendFiles 11');

            arrFiles.push(blob.name);
            arrShards.push(slicesTotal);
            arrFileSize.push(blob.size);
            var timeInMsStart = Date.now();
            if ( debug == 1 ) console.log('SendFiles 11A');
            //arrayFileHash.push(CryptoJS.SHA1(blob));
            if ( debug == 1 ) console.log('SendFiles 11B');
            var timeInMsEnd = Date.now();

            //console.log("SendFiles 12 GEN'D HASH: ", CryptoJS.SHA1(blob));
            if ( debug == 1 ) console.log("SendFiles 12 HASH TIME IN MS: ", timeInMsEnd - timeInMsStart);
            if ( debug == 1 ) console.log("SendFiles 13 Start / blobsize: ", start, blob.size);

            while ( start < blob.size ) {
                end = start + BYTES_PER_CHUNK;
                if ( debug == 1 ) console.log("SendFiles 14 end / Start / blobsize: ", end, start, blob.size);

                if ( end > blob.size ) {
                    end = blob.size;
                }

                //console.log( '@UPLOADING : ', blob.name, index, slicesTotal );
                if ( debug == 1 ) console.log("SendFiles call to uploadFile...");
                uploadFile(blob, index, start, end, arrFiles, arrShards, arrFileSize, arrayFileHash, BYTES_PER_CHUNK);

                start = end;
                index++;
            }
        }
        else
            alert('File "' + blob.name + '" contains spaces int he name, skipping...');
    }
}

/**
 * Blob to ArrayBuffer (needed ex. on Android 4.0.4)
 **/
var str2ab_blobreader = function (str, callback) {
    var blob;
    BlobBuilder = window.MozBlobBuilder || window.WebKitBlobBuilder || window.BlobBuilder;
    if ( typeof ( BlobBuilder ) !== 'undefined' ) {
        var bb = new BlobBuilder();
        bb.append(str);
        blob = bb.getBlob();
    }
    else {
        blob = new Blob([str]);
    }
    var f = new FileReader();
    f.onload = function (e) {
        callback(e.target.result)
    }
    f.readAsArrayBuffer(blob);
}

/**
 * Performs actual upload, adjustes progress bars
 *
 * @param blob
 * @param index
 * @param start
 * @param end
 */
function uploadFile (blob, index, start, end, arrFiles, arrShards, arrFileSize, arrayFileHash, BYTES_PER_CHUNK) {
    var debug = 0;
    var xhr;
    var end;
    var chunk;
    var currSlice

    if ( debug == 1 ) console.log('uploadFile 00');

    var UserID = $('#idUserid').val()
    var pwhash = $('#idPassword').val()

    gUserID = UserID;
    gPwID = pwhash;
    gSessionID = global_sessionid;

    if ( debug == 1 ) {
        console.log('gUserID: ', gUserID);
        console.log('gPwID: ', gPwID);
        console.log('gSessionID: ', gSessionID);
    }

    var filesize = 0;
    var filehash = "";
    var filesize = 0;

    xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if ( xhr.readyState == 4 ) {
            if ( debug == 1 ) {
                console.log('xhr.readyState: ', xhr.readyState);
            }
            if ( xhr.responseText ) {
                if ( debug == 1 )
                    console.log('xhr.responseText: ', xhr.responseText);
            }

            var x = arrFiles.indexOf(blob.name);
            if ( debug == 1 ) {
                console.log("arrFiles.indexOf: ", x, blob.name);
            }
            currSlice = arrShards[x];
            filesize = arrFileSize[x];
            filehash = arrayFileHash[x];

            if ( debug == 1 ) {
                console.log('.......................................');
                console.log('uploadFile blob.name: ', blob.name);
                console.log('uploadFile Array X: ', x);
                console.log('uploadFile currSlice: ', currSlice);
                console.log('uploadFile filesize: ', filesize);
                console.log('uploadFile filehash: ', filehash);
            }
            //slices--;
            currSlice--;
            arrShards[x] = currSlice;
            // if we have finished all slices
            //if ( slices == 0 )
            if ( currSlice == 0 ) {
                if ( debug == 1 ) console.log("uploadFile Merging shards: ", blob.name);
                mergeFile(blob);
                var pid = '#pb' + blob.name;
                var pid2 = 'pb' + blob.name;
                var lblid = 'lbl' + blob.name;
                var progressBar = document.getElementById(pid2);
                var LBL = document.getElementById(lblid);
                LBL.innerHTML = LBL.innerText + ' 100%';
                progressBar.max = progressBar.value = 100;
                $(pid).remove();
                $('#' + lblid).remove();
            }
        }
    };

    if ( blob.webkitSlice ) {
        chunk = blob.webkitSlice(start, end);
    }
    else if ( blob.mozSlice ) {
        chunk = blob.mozSlice(start, end);
    }
    else {
        chunk = blob.slice(start, end);
    }

    xhr.addEventListener("load", function (evt) {
        var pid2 = 'pb' + blob.name;
        //var lblid = 'lbl' + blob.name;
        var percentageDiv = document.getElementById("percent");
        //var progressBar = document.getElementById( "progressBar" );
        var progressBar = document.getElementById(pid2);
        //var LBL = document.getElementById( lblid );
        //LBL.innerHTML = LBL.innerText + ' 100%';

        progressBar.max = progressBar.value = 100;
        percentageDiv.innerHTML = "100%";
    }, false);

    xhr.upload.addEventListener("progress", function (evt) {
        var pid = '#pb' + blob.name;
        var pid2 = 'pb' + blob.name;

        var percentageDiv = document.getElementById("percent");
        //var progressBar = document.getElementById( "progressBar" );
        var progressBar = document.getElementById(pid2);
        if ( progressBar == undefined )
            console.log('progressBar undefined', pid2);

        if ( evt.lengthComputable ) {
            progressBar.max = slicesTotal;
            progressBar.value = index + 1;
            percentageDiv.innerHTML = Math.round(index / slicesTotal * 100) + "%";
            document.getElementById('lblFileProgress').innerText = blob.name;
            //$( pid ).val( index );
        }
    }, false);

    var ToEmails = getSelectedUsers();
    var jsonToEmails = ToEmails.toString();
    //console.log( 'from getSelectedUsers @ jsonToEmails: ' + jsonToEmails );

    xhr.open("post", "php/upload.php", true);

    xhr.setRequestHeader("X-File-Name", blob.name);
    xhr.setRequestHeader("X-File-Size", blob.size);
    xhr.setRequestHeader("X-Index", index);
    xhr.setRequestHeader("FromEmail", gUserID);
    xhr.setRequestHeader("ToEmail", jsonToEmails);
    xhr.setRequestHeader("gUserID", gUserID);
    xhr.setRequestHeader("gPwID", gPwID);
    xhr.setRequestHeader("gSessionID", gSessionID);
    xhr.setRequestHeader("filesize", filesize.toString());
    xhr.setRequestHeader("segmentCount", slicesTotal);
    xhr.setRequestHeader("segmentNbr", index);
    xhr.setRequestHeader("segmentSize", blob.size);
    xhr.setRequestHeader("directory", global_UploadDir);
    xhr.setRequestHeader("filehash", filehash);
    xhr.setRequestHeader("CHUNKSIZE", BYTES_PER_CHUNK);

    if ( blob.webkitSlice ) {                                     // android default browser in version 4.0.4 has webkitSlice instead of slice()
        var buffer = str2ab_blobreader(chunk, function (buf) {   // we cannot send a blob, because body payload will be empty
            try {
                xhr.send(buf);                                      // thats why we send an ArrayBuffer
            }
            catch ( e ) {
                console.log("0B - Error xhr.send : ", e); // pass exception object to error handler
            }
        });
    }
    else {
        try {
            if ( chunk != null ) {
                var l = chunk.size;
                if ( debug == 1 ) console.log('Chunk/Blob Len: ', l);
                xhr.send(chunk);          // if we support slice() everything should be ok
            }
            else {
                console.log('Chunk IS NULL');
            }
        }
        catch ( e ) {
            console.log("0A - Error xhr.send : ", e); // pass exception object to error handler
        }
    }
}

/**
 *  Executed once all of the slices have been sent and performs the "MERGE ALL!"
 **/
function mergeFile (blob) {
    var debug = 0;
    var xhr;
    var fd;
    var RunAsync = true;

    idPassword

    xhr = new XMLHttpRequest();

    fd = new FormData();
    var UserID = $('#idUserid').val()
    var pwhash = $('#idPassword').val()

    if ( debug == 1 ) {
        console.log('mergeFile called: ', blob.name, slicesTotal, "E");
        console.log('UserID: ', UserID);
        console.log('pwhash: ', pwhash);
        console.log('sid: ', global_sessionid);
    }

    fd.append("UserID", UserID);
    fd.append("pwhash", pwhash);
    fd.append("sid", global_sessionid);
    fd.append("FileName", blob.name);
    fd.append("TotalChards", slicesTotal);
    fd.append("Action", "E");

    if ( debug == 1 ) {
        console.log('mergeFile 01 POST');
    }

    xhr.open("POST", "php/merge.php", RunAsync);
    xhr.send(fd);
}
function mergeFileDecrypt (blob) {
    var debug = 0;
    var xhr;
    var fd;
    var RunAsync = true;

    xhr = new XMLHttpRequest();

    fd = new FormData();
    fd.append("UserID", $('#idUserid').val());
    fd.append("pwhash", $('#idPassword').val());
    fd.append("sid", $global_sessionID);

    fd.append("FileName", blob.name);
    fd.append("TotalChards", 0);
    fd.append("Action", "D");

    if ( debug == 1 ) console.log('mergeFile called: ', blob.name, slicesTotal, "D");

    xhr.open("POST", "php/merge.php", RunAsync);
    xhr.send(fd);
}
function restartSystem () {
    var parentip = 'https://' + global_ipaddr + '/SLupload/index.html';
    window.open(parentip, '_self');
}