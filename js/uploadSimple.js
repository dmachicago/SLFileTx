/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function uploadSimple () {
    var debug = 0;
    var url = 'php/' + 'uploadSimple.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    //var sid = global_sessionid;
    var sid = '11223344';
    var obj;
    //var phpurl = global_php_url + 'uploadSimple.php';
    var phpurl = 'php/uploadSimple.php';
    //var pwhashed = CryptoJS.SHA1( userid + pw );
    var pwhashed = '2ea241cf5871ef6693b247fac1152c4499ca3a3b';
    //2ea241cf5871ef6693b247fac1152c4499ca3a3b
    //e0151827fb5463170327edcc07183aa183b1a527
    var d = new Date();
    var n = d.getTime();

    sid = n.toString();

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);

    xhr.onreadystatechange = function () {
        console.log('00 testPhp');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;
            console.log('results: ', results);

            //results = extractJson( results );
            ////console.log( 'xhr.responseText: ', xhr.responseText );
            ////console.log( 'results: ', results );

            //if ( !results )
            //{
            //    alert( 'Login FAILED !' );
            //    return;
            //}
            //else
            //{
            //    $shortTest = 1;
            //    if ( $shortTest == 1 )
            //    {
            //        console.log( 'SHortTest Results: ', results );
            //        return;
            //    }
            //    var obj = JSON.parse( results );
            //    console.log( '01 testPhp execution complete' );
            //    //var foundIT = getObjvalue( results, 'SUCCESS' );
            //    var foundIT = 0;
            //    jQuery.each( obj, function ( ikey, val )
            //    {
            //        console.log( 'ITEM: ', ikey, val );
            //    } );
            //}
        }
    }

    xhr.addEventListener("load", function (evt) {
        console.log('testPhp - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('Login ERROR!', e.toString());
    }
}