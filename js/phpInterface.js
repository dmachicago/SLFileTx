/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function phpInterface (functionname) {
    var url = 'php/' + 'systemAttach.php';
    console.log('00 testPhpScript');
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    //var sid = $( '#idRoomName' ).val();
    var obj;
    var phpurl = global_php_url + 'systemAttach.php';
    //var functionname = 'testlink';
    //var functionname = 'testDbAttach';

    var xhr;
    var fd;

    xhr = new XMLHttpRequest();

    fd = new FormData();

    console.log('functionname: ', functionname);

    fd.append("functionname", functionname);
    fd.append("userid", txtuserid);
    fd.append("pw", pw);
    fd.append("RoomName", RoomName);
    fd.append("pwhash", sha1(pw));

    xhr.onreadystatechange = function () {
        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            console.log('phpInterface - xhr.readyState: ', xhr.readyState);
            console.log('phpInterface - xhr.responseText: ', xhr.responseText);
            var myArr = JSON.parse(xhr.responseText);
            console.log('phpInterface - myArr: ', myArr);
            myFunction(myArr);
            if ( functionname == 'memberLogin' ) {
                $.each(myArr, function (k, v) {
                    console.log('key: ' + k + " / val: " + v);
                    if ( k == 'COUNT' ) {
                        if ( v > 0 ) {
                            alert('Login for ' + txtuserid + ', successful');
                        }
                        else {
                            alert('Login for ' + txtuserid + ', FAILED');
                        }
                    }
                });
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        console.log('phpInterface - xhr.addEventListener: ', xhr.readyState);
    }, false);

    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0D - Error xhr.send : ", e); // pass exception object to error handler
    }
}

function myFunction (arr) {
    console.log('00 myFunction arr: ', arr);
    //var out = "";

    for ( var key in arr ) {
        if ( arr.hasOwnProperty(key) ) {
            console.log(key + " -> " + arr[key]);
        }
    }

    console.log('01 myFunction arr: ');

    var i = 0;
    for ( i = 0; i < arr.length; i++ ) {
        console.log('myFunction arr(' + i + ') = ' + arr[i]);
    }
    //document.getElementById( "id01" ).innerHTML = out;
    //console.log(out);
}