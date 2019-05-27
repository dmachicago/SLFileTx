/*
 * Copyright (@) 2017. dte. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function vaporizeFiles (Files) {
    var debug = 1;

    var dte = new Date();
    var startms = dte.getTime();
    var endms = dte.getTime();
    var etime = 0;

    console.log("STARTING TIME: startms: ", startms);

    var url = 'php/vaporize.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'vaporize.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);
    //2ea241cf5871ef6693b247fac1152c4499ca3a3b
    //e0151827fb5463170327edcc07183aa183b1a527

    var n = dte.getTime();
    sid = n.toString();

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    if ( debug == 1 ) console.log('00 vaporizeFiles:', Files);

    fd.append("userid", userid);
    fd.append("pwhash", pw);
    fd.append("sid", sid);
    fd.append("FileUserID", userid);
    fd.append("Files", Files);
    fd.append("startms", startms);


    if ( debug == 1 ) console.log("php vaporize.php " + " '" + userid + "' " + " '" + pw + "' '" + sid + "' '" + userid + "' '" + Files + "' ;");

    $("#imgPuma").show();

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 vaporizeFiles: readystate: ' + xhr.readyState + ' / status: ' + xhr.status);
        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            if ( debug == 1 ) console.log('results before: ', xhr.responseText);
            var results = xhr.responseText;

            results = extractJson(results);
            if ( debug == 1 ) console.log('results after: ', results);

            if ( !results ) {
                alert('Vaporize FAILED !');
                return;
            }
            else {
                var obj = JSON.parse(results);
                //var successFulExec = getObjvalue( results, 'SUCCESS' );
                var successFulExec = 0;

                for ( var key in obj ) {
                    // skip loop if the property is from prototype
                    if ( !obj.hasOwnProperty(key) ) continue;

                    var obj = obj[key];
                    for ( var prop in obj ) {
                        // skip loop if the property is from prototype
                        if ( !obj.hasOwnProperty(prop) ) continue;
                        //************************************
                        console.log("--- <" + prop + "> = <" + obj[prop] + ">");
                        //************************************
                        var ikey = prop;
                        var val = obj[prop];
                        if ( debug == 1 ) console.log('Prop: ', prop);
                        if ( debug == 1 ) console.log('VAL: ', obj[prop]);
                        if ( ikey == 'SUCCESS' && val == 0 ) {
                            var dt = new Date();
                            endms = dt.getTime();
                            etime = endms - startms;
                            console.log("startms", startms);
                            console.log("endms", startms);
                            console.log("etime", etime);
                            $('#lblStatus').val('Vaporize Successful... file(s) removed.');
                            $("#imgPuma").hide();
                            console.log('Vaporize Successful... file(s) removed.');
                            successFulExec = 0;
                            bGetPendingFiles = 1;
                        }
                        if ( ikey == 'SUCCESS' && val == 1 ) {
                            var dt = new Date();
                            endms = dt.getTime();
                            etime = endms - startms;
                            console.log("startms", startms);
                            console.log("endms", startms);
                            console.log("Total Elapsed (ms): ", etime);
                            $("#imgPuma").hide();
                            $('#lblStatus').val('Vaporize Successful... file(s) removed.');
                            console.log('Vaporize Successful... file(s) removed.');
                            if ( debug == 1 )
                                console.log('**** SUCCESS ****   : ', ikey, val);
                            successFulExec = 1;
                            bGetPendingFiles = 1;
                        }
                        if ( ikey == 'ERROR' ) {
                            var dt = new Date();
                            endms = dt.getTime();
                            etime = endms - startms;
                            console.log("startms", startms);
                            console.log("endms", startms);
                            console.log("etime", etime);
                            $("#imgPuma").hide();
                            alert("Failed vaporization: " + val);
                            $('#lblStatus').val('Failed vaporization');
                            successFulExec = 0;
                        }
                        if ( bGetPendingFiles == 1 ) {
                            var dt = new Date();
                            endms = dt.getTime();
                            $("#imgPuma").hide();
                            if ( debug == 1 ) console.log('calling getMemberPendingFiles');

                            var dt = new Date();
                            var start_time = dt.getTime();

                            getMemberPendingFiles('003');

                            var dt2 = new Date();
                            var end_time2 = dt2.getTime();
                            var elapsed_time = end_time2 - start_time;

                            console.log("start_time:", start_time);
                            console.log("end_time2:", end_time2);
                            console.log("elapsed_time:", elapsed_time);
                        }
                    }
                }

                var leavenow = 1;

                if ( leavenow == 1 ) {
                    console.log('leaving vaporize now...');
                    getMemberPendingFiles('004');
                    return;
                }

                if ( debug == 1 ) console.log('successFulExec = ', successFulExec);
                if ( successFulExec == 1 ) {
                    $('#lblStatus').val('Vaporizing server files completed.')
                    jQuery.each(obj, function (ikey, val) {
                        if ( ikey == 'sessionid' ) {
                            //if ( debug == 1 )
                            if ( debug == 1 ) console.log('**** currsessionid ****   : ', ikey, val);
                            global_sessionid = val;
                            sessionStorage.setItem('currsessionid', val);
                        }
                        if ( ikey == 'memberhash' ) {
                            var userhash = CryptoJS.SHA1(userid);
                            if ( sessionStorage.getItem('curruserhash') == val ) {
                                if ( debug == 1 ) console.log('HASH Verified...');
                            }
                            else {
                                console.log('Notice HASH:', sessionStorage.getItem('curruserhash'), val);
                            }
                        }
                        if ( debug == 1 )
                            console.log('Results: ', ikey, val);
                    });

                    if ( sessionStorage.getItem('UID') == undefined )
                        sessionStorage.setItem('UID', userid);

                    if ( sessionStorage.getItem('PWHASHED') == undefined )
                        sessionStorage.setItem('PWHASHED', pwhashed);
                    if ( sessionStorage.getItem('PW') == undefined )
                        sessionStorage.setItem('PW', pw);
                    if ( sessionStorage.getItem('SID') == undefined )
                        sessionStorage.setItem('SID', global_sessionid);

                    if ( debug == 1 ) console.log('sessionStorage.getItem( SID ): ', sessionStorage.getItem('SID'));
                    if ( debug == 1 ) console.log('global_sessionid: ', global_sessionid);

                    //*****************************************************
                    setstatus("Logged In");
                    getMyGroups();
                    selectFirstItem("lbMyGroups");
                    getMemberPendingFiles('005');
                    //*****************************************************
                }
                else {
                    $('#lblStatus').val('WARNING: Vaporizing server files FAILED.')
                    if ( debug == 1 ) console.log("loginMember 10: ");
                    $("#imgSafe").attr("src", "image/unlocked00.jfif");
                    if ( debug == 1 ) console.log("loginMember 11: ");
                    global_login_attempts++;
                    if ( debug == 1 ) console.log("loginMember 12: ");
                    if ( global_login_attempts >= 3 ) {
                        alert("LOGIN FAILED too many times, please try again in 1 hour.");
                        return;
                    }
                    else
                        alert("A001X - Failed to login...");

                    if ( debug == 1 ) console.log("loginMember global_login_attempts: ", global_login_attempts);
                    sessionStorage.setItem('UID', '');
                    sessionStorage.setItem('PW', '');
                    sessionStorage.setItem('SID', '');
                }
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('load vaporize - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        if ( debug == 1 ) console.log('Starting vaporize - xhr.open: ', xhr.readyState);
        $('#lblStatus').val('Standby... vaporizing all server files...')
        xhr.send(fd);
    }
    catch ( e ) {
        $('#lblStatus').val('FAILED to vaporize server files...')
        console.log("0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('Login ERROR!', e.toString());
    }
}

function vaporizeAll () {
    var result = confirm("Server copies of files will vaporize! CANNOT be undone, are you sure?");
    if ( result ) {
        emptyFileToDelete();
        $('#idFilesRxList a').each(function () {
            var fileName = $(this).text();
            //var id = $( this ).attr( 'id' );
            insertVaporize(fileName)
            //var strArray = JSON.stringify( gFiLeArray )
        });
        elements = gFiLeArray.join('|')
        console.log('vaporizeAll elements: ' + elements);
        vaporizeFiles(elements);
    }
}

function vaporize1 (FileName) {
    console.log('vaporize1 File: ' + FileName);
    var result = confirm("Server copy of " + FileName + " will vaporize! CANNOT be undone, are you sure?");
    if ( result ) {
        emptyFileToDelete();
        insertVaporize(FileName)
        elements = gFiLeArray.join('|')
        console.log('vaporize1 Removing File: ' + elements);
        vaporizeFiles(elements);
    }
}