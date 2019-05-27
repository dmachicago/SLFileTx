/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function getMemberListV2 () {
    console.log("dbClass called");
    var debug = 0;
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var ResultObj;
    var phpurl = global_php_url + 'validateUser.php';
    var jsonObj = null;

    console.log("dbClass txtuserid: ", txtuserid);
    if ( sessionStorage.getItem('UID') == undefined )
        sessionStorage.setItem('UID', txtuserid);
    if ( sessionStorage.getItem('PW') == undefined )
        sessionStorage.setItem('PW', pw);
    if ( sessionStorage.getItem('SID') == undefined )
        sessionStorage.setItem('SID', sid);

    $.ajax({
        data: {
            userid: txtuserid,
            pwhash: pw,
            sessionid: sid,
            funcName: "getMyMemberList"
        },
        type: "POST",
        url: phpurl,
        dataType: 'json',
        success: function (Results) {
            if ( !( 'error' in Results ) ) {
                console.log("loginMember 05: ");

                jsonObj = JSON.parse(Results);
                console.log(jsonObj);

                for ( var p in jsonObj ) {
                    if ( jsonObj.hasOwnProperty(p) ) {
                        console.log('00:', jsonObj[p]) + "\n";
                    }
                }
                for ( var i = 0; i < jsonObj.length; i++ ) {
                    for ( var prop in jsonObj[i] ) {
                        if ( jsonObj[i].hasOwnProperty(prop) ) {
                            console.log('01:', jsonObj[i][prop]) + "\n";
                        }
                    }
                }
            }
            else {
                console.log("ERROR on Login:");
                console.log(Results.error);
                console.log("Results", ResultObj);
                alert("Failed to login... review console log");
            }
        }
    });
}