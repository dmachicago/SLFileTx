/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

/*
 var cl = new Downloader.ChunkLoader("test.jpg",  // fileGuid
 "/var/www/html/SLupload/uploads/",              // requestUrl
 "getChunkCount",        // chunkCountMethod
 "getChunk",             // getChunkMethod
 "fileName",             // fileGuidParamName
 "chunkNr");             // chunkNrParamName

 cl.startDownload(function (fileBlob) {
 onFileDownloaded(fileBlob);
 cl = null;
 });
 */

var Downloader;

(function (Downloader) {
    var ChunkLoader = (function () {
        function ChunkLoader (fileGuid, requestUrl, chunkCountMethod, getChunkMethod, fileGuidParamName, chunkNrParamName) {
            this.m_fileGuid = fileGuid;
            this.m_requestUrl = requestUrl;
            this.m_chunkCountMethod = chunkCountMethod;
            this.m_getChunkMethod = getChunkMethod;
            this.m_fileGuidParamName = fileGuidParamName;
            this.m_chunkNrParamName = chunkNrParamName;
        }

        ChunkLoader.prototype.startDownload = function (callback) {
            console.log("ChunkLoader startDownload: ");
            var that = this;
            this.m_allChunkDownloadedCallback = callback;
            this.m_arrBlobs = [];
            this.m_chunkQueue = [];
            this.getChunkCount(that.onChunkCountRecieved);
        };
        ChunkLoader.prototype.getChunkCount = function (callback) {
            var that = this;
            var params = this.m_fileGuidParamName + "=" + this.m_fileGuid;
            console.log("ChunkLoader getChunkCount this.m_requestUrl: ", this.m_requestUrl);
            console.log("ChunkLoader getChunkCount this.m_chunkCountMethod: ", this.m_chunkCountMethod);
            console.log("ChunkLoader getChunkCount PARMS : ", params);
            var xhr = new XMLHttpRequest();
            xhr.open("GET", this.m_requestUrl + this.m_chunkCountMethod + "?" + params, true);
            xhr.onload = function () {
                callback(this, that);
            };
            xhr.send(null);
        };
        ChunkLoader.prototype.downloadChunks = function (chunkCount, fileGuid) {
            console.log("ChunkLoader downloadChunks chunkCount: ", chunkCount);
            for ( var i = 1; i <= chunkCount; i++ ) {
                var chunk = new Chunk(i, fileGuid);
                this.m_chunkQueue.push(chunk);
            }
            this.downloadNextChunk();
        };
        ChunkLoader.prototype.downloadNextChunk = function () {
            console.log("ChunkLoader downloadNextChunk m_chunkQueue.length: ", m_chunkQueue.length);
            if ( this.m_chunkQueue.length > 0 ) {
                var nextChunk = this.m_chunkQueue.shift();
                this.downloadChunk(nextChunk);
            }
            else {
                this.m_chunkQueue = null;
            }
        };
        ChunkLoader.prototype.downloadChunk = function (chunk) {
            console.log("ChunkLoader downloadChunk chunk.chunkNr : ", chunk.chunkNr);
            var that = this;
            var params = this.m_fileGuidParamName + "=" + chunk.fileGuid + "&" + this.m_chunkNrParamName + "=" + chunk.chunkNr;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", this.m_requestUrl + this.m_getChunkMethod, true);
            xhr.responseType = "blob";
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                that.onChunkDownloaded(this, chunk);
            };
            xhr.send(params);
        };
        ChunkLoader.prototype.onChunkCountRecieved = function (xhr, that) {
            that.m_chunkCount = parseInt(xhr.responseText);
            that.downloadChunks(that.m_chunkCount, that.m_fileGuid);
        };
        ChunkLoader.prototype.onChunkDownloaded = function (xhr, downloadedChunk) {
            this.m_arrBlobs.push(xhr.response);
            if ( this.m_chunkQueue.length === 0 ) {
                this.onAllChunksDownloaded();
            }
            else {
                this.downloadNextChunk();
            }
            xhr = null;
        };
        ChunkLoader.prototype.onAllChunksDownloaded = function () {
            var finalBlob = new Blob(this.m_arrBlobs, {
                type: this.m_arrBlobs[0].type
            });
            this.m_arrBlobs = null;
            this.m_allChunkDownloadedCallback(finalBlob);
        };
        return ChunkLoader;
    })();
    Downloader.ChunkLoader = ChunkLoader;
    var Chunk = (function () {
        function Chunk (chunkNr, fileGuid) {
            this.m_chunkNr = chunkNr;
            this.m_fileGuid = fileGuid;
        }

        Object.defineProperty(Chunk.prototype, "chunkNr", {
            get: function () {
                return this.m_chunkNr;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(Chunk.prototype, "fileGuid", {
            get: function () {
                return this.m_fileGuid;
            },
            enumerable: true,
            configurable: true
        });
        return Chunk;
    })();
    Downloader.Chunk = Chunk;
})(Downloader || ( Downloader = {} ));

function UrlFileDownload (url, success) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = "blob";
    xhr.onreadystatechange = function () {
        if ( xhr.readyState == 4 ) {
            if ( success ) success(xhr.response);
        }
    };
    xhr.send(null);
}

function grabFile () {
    var cl = new Downloader.ChunkLoader(
        "test.jpg",  // fileGuid
        "/var/www/html/SLupload/uploads/",              // requestUrl
        "getChunkCount",        // chunkCountMethod
        "getChunk",             // getChunkMethod
        "fileName",             // fileGuidParamName
        "chunkNr");             // chunkNrParamName

    cl.startDownload(function (fileBlob) {
        onFileDownloaded(fileBlob);
        cl = null;
    });
}

function txDownload () {
    console.log('btntxDownload clicked');
    var that = this;
    var page_url = 'php/txDownload.php';

    var req = new XMLHttpRequest();
    req.open("POST", page_url, true);
    req.addEventListener("progress", function (evt) {
        if ( evt.lengthComputable ) {
            var percentComplete = evt.loaded / evt.total;
            console.log(percentComplete);
        }
    }, false);

    req.responseType = "blob";
    req.onreadystatechange = function () {
        if ( req.readyState === 4 && req.status === 200 ) {
            var filename = $(that).data('filename');
            if ( typeof window.chrome !== 'undefined' ) {
                // Chrome version
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(req.response);
                link.download = filename;
                link.click();
            }
            else if ( typeof window.navigator.msSaveBlob !== 'undefined' ) {
                // IE version
                var blob = new Blob([req.response], {type: 'application/force-download'});
                window.navigator.msSaveBlob(blob, filename);
            }
            else {
                // Firefox version
                var file = new File([req.response], filename, {type: 'application/force-download'});
                window.open(URL.createObjectURL(file));
            }
        }
    };
    req.send();
}

function xDownLoad (filename) {
    console.log("xDownLoad :", filename);
    var tgturl = "http://" + global_ipaddr + "/php/xDownLoad.php?file="
    window.location = tgturl + filename;
}

function SaveToDisk (blobURL, fileName) {
    var reader = new FileReader();
    reader.readAsDataURL(blobURL);
    reader.onload = function (event) {
        var save = document.createElement('a');
        save.href = event.target.result;
        save.target = '_blank';
        save.download = fileName || 'unknown file';

        var event = document.createEvent('Event');
        event.initEvent('click', true, true);
        save.dispatchEvent(event);
        ( window.URL || window.webkitURL ).revokeObjectURL(save.href);
    };
}

function sec2time (timeInSeconds) {
    var pad = function (num, size) {
            return ( '000' + num ).slice(size * -1);
        },
        time = parseFloat(timeInSeconds).toFixed(3),
        hours = Math.floor(time / 60 / 60),
        minutes = Math.floor(time / 60) % 60,
        seconds = Math.floor(time - minutes * 60),
        milliseconds = time.slice(-3);

    return pad(hours, 2) + ':' + pad(minutes, 2) + ':' + pad(seconds, 2) + ',' + pad(milliseconds, 3);
}

function checkTime (i) {
    if ( i < 10 ) {
        i = "0" + i;
    }
    return i;
}
function startTime () {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    var ms = today.getMilliseconds();
    // add a zero in front of numbers<10
    m = checkTime(m);
    s = checkTime(s);
    var t = h + ":" + m + ":" + s + ":" + ms;
    return t;
}

function b64DecodeUnicode (str) {
    return decodeURIComponent(atob(str).split('').map(function (c) {
        return '%' + ( '00' + c.charCodeAt(0).toString(16) ).slice(-2);
    }).join(''));
}

function b64EncodeUnicode (str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function (match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}

function quickDownloadV1 () {
    var t = startTime();
    console.log("** Entering quickDownload 00: " + t);
    var i = 0;
    var tdata = null;
    var phpurl = global_php_url + 'quickDownload.php';
    var filename = '20161106_113327.jpg';
    $.ajax({
        url: phpurl,
        data: {'id': '235'},
        context: document.body,
        success: function (data) {
            t = startTime();
            console.log("** Data returned: " + t);
        }
    }).done(function (data) {
        alert("quickDownload DONE fired.");
        //$(this).html(data);
    });
}

function quickDownload () {
    var t = startTime();
    console.log("** Entering quickDownload 00: " + t);
    var i = 0;
    var tdata = null;
    var phpurl = global_php_url + 'quickDownload.php';
    var filename = 'FileZilla.logXXX.txt';
    $.ajax({
        url: phpurl,
        data: {'id': '235'},
        context: document.body,
        type: 'GET',
        success: function (data) {
            t = startTime();
            console.log("** Data returned: " + t);
            -console.log("** Received data length= ", data.length);
            console.log("** calling saveAs");

            //console.log("1 DATA:" , data);
            //console.log("2 DATA:" , data.toString());

            t = startTime();
            console.log("** calling base64EncodedStr: " + t);
            //var base64EncodedStr = btoa(unescape(encodeURIComponent(data)));
            //var b64 =b64EncodeUnicode(data);

            t = startTime();
            console.log("** calling byteNumbers: " + t);

            var byteNumbers = new Array(data.length);
            for ( var i = 0; i < data.length; i++ ) {
                //byteNumbers[i] = b64.charCodeAt(i);
                byteNumbers[i] = data.charCodeAt(i);
            }
            t = startTime();
            console.log("** calling byteArray: " + t);
            var byteArray = new Uint8Array(byteNumbers);

            //var blob = new Blob([byteArray], {type: "application/octet-stream"});
            var blob = new Blob([byteArray], {type: "application/pdf"});
            t = startTime();

            console.log("** called blob: " + t);
            //saveAs(blob , filename, true);
            saveAs(blob, filename);

            console.log("** CALLED saveAs: " + filename);

            alert("Download Complete...");
        }
    }).done(function (data) {
        alert("DONE Fired");
        console.log("** @DONE Received data length= ", data.length);
        //$(this).html(data);
    });
}

function fileDownLoad (FileName, FileID) {
    var debug = 0;
    var phpurl = global_php_url + 'fileDownLoad.php';
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;

    if ( debug == 1 ) console.log('fileDownLoad 00: ', FileName, FileID);

    //setHashes();
    $.ajax({
        data: {
            'userid': txtuserid,
            'pwhash': pw,
            'sessionid': sid,
            'FileName': FileName,
            'FileID': FileID
        },
        type: "POST",
        url: phpurl,
        dataType: 'json',
        success: function (fileObject) {
            if ( debug == 1 ) console.log('fileDownLoad 01');
            if ( debug == 1 ) console.log('fileObject.count: ', Object.keys(fileObject).length);
            for ( var key in fileObject ) {
                console.log("KEY: ", key.toString());
                if ( key == "ERROR" ) {
                    var keyval = fileObject[key];
                    alert(keyval);
                }
            }
            if ( Object.keys(fileObject).length > 0 ) {
                if ( debug == 1 ) console.log('fileDownLoad 02');
                var i = 0;
                for ( var key in fileObject ) {
                    var keyval = fileObject[key];

                    if ( debug == 1 ) console.log('fileDownLoad key: ', key);
                    if ( debug == 1 ) console.log('fileDownLoad val.length: ', keyval.length);
                    if ( key != 'slice' ) {
                        if ( debug == 1 ) console.log('fileDownLoad val: ', keyval);
                    }
                    if ( key == 'slice' ) {
                        /* TODO: replace the blob content with your byte[] */
                        var blob = new Blob([keyval], {type: "application/octet-stream"});
                        saveAs(blob, FileName);
                        if ( debug == 1 ) console.log('Saved File as : ', FileName);
                    }
                    if ( key == 'ERROR' ) {
                        console.log('ERROR: ', key, keyval);
                    }
                    i = i + 1;
                }
            }
        }
    }).fail(function (jqXHR, textStatus, error) {
        // Handle error here
        var ErrTxt = jqXHR.responseText;
        alert("ERR035F: <" + error + ">");
        console.log("ERR035F-1: <***");
        console.log('Txt Length: ' + ErrTxt.length);
        console.log("ERR035F-1: ***>");
        console.log("ERR035F-2: <" + textStatus);
        console.log("ERR035F-3: <" + error);
    });
}

function emptyFileToDelete () {
    gFiLeArray = [];
}

function insertVaporize (fileName) {
    //console.log( 'insertVaporize CLICKED: ' + fileName );
    var iExist = 0;
    iExist = $.inArray(fileName, gFiLeArray)

    if ( iExist < 0 ) {
        gFiLeArray.push(fileName);
    }
}

function downloadAll () {
    console.log("starting downloadAll");
    $('#idFilesRxList a').each(function () {
        var id = $(this).attr('id');
        console.log("downloadAll ID: ", id);
        //alert( $( this ).text() );
        //alert( 'ID: ' + id );
        document.getElementById(id).click();
    });
}

function addListener (ElementID) {
    var jid = '#' + ElementID;
    $(document).on('click', jid, function () {
        alert('Delete notification for HREF: ' + ElementID);
    });
}

function getMemberPendingFiles (LOC) {
    var debug = 0;
    var phpurl = 'php/getMemberPendingFiles.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;
    var ResultObj;

    $("#footer").hide();

    var pwhashed = CryptoJS.SHA1(userid + pw);

    if ( debug == 1 ) {
        console.log('getMemberPendingFiles 00');
        console.log('phpurl: ', phpurl);
        console.log('userid: ', userid);
        console.log('pw: ', pw);
        console.log('sid: ', sid);
        console.log('pwhashed: ', pwhashed);
    }

    var d = new Date();
    var n = d.getTime();

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    fd.append("pw", pw);
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('0A getMemberPendingFiles');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            $("#idFilesRxList").empty();
            //$( "#idFilesRxList" ).find( "a" ).each( function ()
            //{
            //    $( this ).remove();
            //} );

            //$( '#idFilesRxList' ).append( '<h4>FILES:</h4>' );

            var results = xhr.responseText;
            var results = extractEmbeddedJson(results);

            if ( !results ) {
                $('#lblStatus').text('NO Pending files !')
                return;
            }
            else if ( results == '[]' ) {
                $('#lblStatus').text('NO Pending files !')
                return;
            }
            else {
                var aref = '';
                var str = "";
                var i = 0;
                var j = 0;
                var obj;
                try {
                    obj = JSON.parse(results);
                }
                catch ( e ) {
                    //alert( 'NOTICE: No pending files', e.toString() );
                    $('#lblStatus').html('NOTICE: No pending files: ' + userid);
                    return;
                }
                var obj = JSON.parse(results);
                var FileName = '';
                var FromEmail = '';
                var SentDate = '';
                var ExpireDate = '';
                var FileID = '';
                i = 0;
                var PendingFiles = 0;

                jQuery.each(obj, function (ikey, val) {
                    i += 1;

                    var valx = obj[ikey];
                    //if ( debug == 1 ) console.log( 'getMemberPendingFiles valx: ', valx );
                    var objstr = JSON.stringify(valx);
                    //if ( debug == 1 ) console.log( 'getMemberPendingFiles str valx: ', objstr );
                    var obj2 = JSON.parse(objstr);

                    jQuery.each(obj2, function (ikey2, val2) {
                        j += 1;

                        //if ( debug == 1 ) console.log( 'getMemberPendingFiles ikey2: ', ikey2, '/', val2 );

                        if ( ikey2 == 'FileName' ) {
                            FileName = obj2['FileName'];
                            if ( debug == 1 ) console.log('FileName: ', FileName);
                        }

                        if ( ikey2 == 'SentDate' ) {
                            SentDate = obj2['SentDate'];
                            if ( debug == 1 ) console.log('SentDate: ', SentDate);
                        }

                        if ( ikey2 == 'ExpireDate' ) {
                            ExpireDate = obj2['ExpireDate'];
                            if ( debug == 1 ) console.log('ExpireDate: ', ExpireDate);
                        }

                        if ( ikey2 == 'FileID' ) {
                            FileID = obj2['FileID'];
                            if ( debug == 1 ) console.log('FileID: ', FileID);
                        }
                    });

                    if ( i > 0 ) {
                        $('#lblStatus').text('PENDING files for ' + userid);
                        PendingFiles = 1;
                    }

                    if ( debug == 1 ) console.log('************************* I = ' + i + ' *************************');
                    if ( debug == 1 ) console.log('** Processing File : ' + FileName);

                    var valkey = "FID." + i + "." + FileID;
                    var btnkey = "btn" + i + "." + FileID;
                    var str = FileName + ' , ' + SentDate + ' , ' + FromEmail + ' , ' + ExpireDate;
                    var strInput = '<input type="checkbox" name="' + valkey + '" id="' + valkey + '">';
                    var strLabel = '<label for="' + valkey + '">' + str + '</label>';

                    var newButton = '<img src = "image/59.gif" alt = "x" onclick="vaporize1( \'' + FileName + '\' )" /> ';

                    $("#idFilesRxList").append(newButton);

                    aref = '<a href="uploads/Decrypted/' + FileName + '" target="_blank" id="' + valkey + '" style ="margin-left:10px" download>' + FileName + '</a><br/>';
                    $(aref).button().appendTo('#idFilesRxList').trigger("create");
                    addListener(valkey);

                });

                if ( PendingFiles == 1 ) {
                    $("#radio-choice-v-2b").click();
                }
                //$( '#idFilesRxList' ).trigger( "create" )
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        //console.log( 'getMemberPendingFiles - xhr.addEventListener: ', evt );
        if ( debug == 1 )
            console.log('getMemberPendingFiles - xhr.addEventListener: ', xhr.readyState);
    }, false);

    xhr.open("POST", phpurl, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('Login ERROR!', e.toString());
    }
}

function saveAsV1 (blob, fileName) {
    var url = window.URL.createObjectURL(blob);

    var anchorElem = document.createElement("a");
    anchorElem.style = "display: none";
    anchorElem.href = url;
    anchorElem.download = fileName;

    document.body.appendChild(anchorElem);
    anchorElem.click();

    document.body.removeChild(anchorElem);

    // On Edge, revokeObjectURL should be called only after
    // a.click() has completed, atleast on EdgeHTML 15.15048
    setTimeout(function () {
        window.URL.revokeObjectURL(url);
    }, 1000);
}

(function () {
    // convert base64 string to byte array
    var byteCharacters = atob("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");
    var byteNumbers = new Array(byteCharacters.length);
    for ( var i = 0; i < byteCharacters.length; i++ ) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    var byteArray = new Uint8Array(byteNumbers);

    // now that we have the byte array, construct the blob from it
    var blob1 = new Blob([byteArray], {type: "application/octet-stream"});

    var fileName1 = "cool.gif";
    var testsave = 0;

    if ( testsave == 1 )
        saveAs(blob1, fileName1);

    // saving text file
    var blob2 = new Blob(["cool"], {type: "text/plain"});
    var fileName2 = "cool.txt";

    if ( testsave == 1 )
        saveAs(blob2, fileName2);
})();

function downloadNoAjax (filename) {
    var fn = "/var/www/html/SLupload/uploads/BarkersFrontPorch.JPG"
    var tgturl = "http://" + global_ipaddr + "/php/downloadNoAjax.php?file="
    window.location = tgturl + fn;
}