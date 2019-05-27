chunkload.js
============

XHR downloads a file in shards, puts the shards together and returns the file.

## Usage
    
    var cl = new Downloader.ChunkLoader("test.jpg",  // fileGuid
                                        "/files/",              // requestUrl
                                        "getChunkCount",        // chunkCountMethod
                                        "getChunk",             // getChunkMethod
                                        "fileName",             // fileGuidParamName
                                        "chunkNr");             // chunkNrParamName
                                        
    cl.startDownload(function (fileBlob) {
      onFileDownloaded(fileBlob);
      cl = null;
    });
