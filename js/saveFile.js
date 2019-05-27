/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

window.requestFileSystem = window.requestFileSystem || window.webkitRequestFileSystem;
window.storageInfo = window.storageInfo || window.webkitStorageInfo;

// Request access to the file system
var fileSystem = null         // DOMFileSystem instance
    , fsType = PERSISTENT       // PERSISTENT vs. TEMPORARY storage
    , fsSize = 10 * 1024 * 1024 // size (bytes) of needed space
    ;

window.storageInfo.requestQuota(fsType, fsSize, function (gb) {
    window.requestFileSystem(fsType, gb, function (fs) {
        fileSystem = fs;
    }, errorHandler);
}, errorHandler);

function saveFile (data, path) {
    if ( !fileSystem ) return;

    fileSystem.root.getFile(path, {create: true}, function (fileEntry) {
        fileEntry.createWriter(function (writer) {
            writer.write(data);
        }, errorHandler);
    }, errorHandler);
}

function readFile (path, success) {
    fileSystem.root.getFile(path, {}, function (fileEntry) {
        fileEntry.file(function (file) {
            var reader = new FileReader();

            reader.onloadend = function (e) {
                if ( success ) success(this.result);
            };

            reader.readAsText(file);
        }, errorHandler);
    }, errorHandler);
}

// function errorHandler(e) {
//     var msg = '';
//
//     switch (e.code) {
//         case FileError.QUOTA_EXCEEDED_ERR:
//             msg = 'QUOTA_EXCEEDED_ERR';
//             break;
//         case FileError.NOT_FOUND_ERR:
//             msg = 'NOT_FOUND_ERR';
//             break;
//         case FileError.SECURITY_ERR:
//             msg = 'SECURITY_ERR';
//             break;
//         case FileError.INVALID_MODIFICATION_ERR:
//             msg = 'INVALID_MODIFICATION_ERR';
//             break;
//         case FileError.INVALID_STATE_ERR:
//             msg = 'INVALID_STATE_ERR';
//             break;
//         default:
//             msg = 'Unknown Error';
//             break;
//     };

console.log('Error: ' + msg);
}

