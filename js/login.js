/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function loginMember () {
    var debug = 0;
    var url = 'php/validateUser.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'validateUser.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);
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

    if (debug == 1) {
        console.log('PHP CALL:');
        console.log('php validateUser.php "' + userid + '" "' + pw + ' " "' + pw + '" "' + sid + '"');
    }

    if ( pw.toUpperCase() == 'WELCOME1!' ) {
        alert("This is a temporary password, please select options when possible and change your password. In 3 uses, this password if not changed, will be disabled. Thank you.")
        fd.append("temppw", 1);
    }
    else {
        fd.append("temppw", 0);
    }

    if ( debug == 1 ) {
        console.log('userid: ', userid);
        console.log('pw: ', pw);
        console.log('sid: ', sid);
    }

    xhr.onreadystatechange = function () {
        var bLoginSuccess = 0;
        if ( debug == 1 ) {
            console.log('00 loginMember executed.');
            console.log('xhr.status:', xhr.status);
            console.log('xhr.readyState: ', xhr.readyState);
        }

        //Just hide them right away and show them if deemed admin
        $('#btnGroupMgt').hide();
        $('#btnUserMgt').hide();

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;
            if ( debug == 1 ) console.log('*********************');
            if ( debug == 1 ) console.log('results : ', results.toString());

            var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.
            if ( debug == 1 ) console.log('*********************');
            if ( debug == 1 ) console.log('results of jstr: ', jstr);

            var A1 = new Array();
            A1 = build2dArray(jstr, 0);

            if ( debug == 1 ) console.log('*********************');
            if ( debug == 1 ) console.log('results before extractJson: ', results);
            if ( debug == 1 ) console.log('*********************');

            results = extractJson(results);

            if ( debug == 1 ) console.log('results after: ', results);
            if ( debug == 1 ) console.log('results Type: ', jQuery.type(results));

            if ( A1.length == 0 ) {
                alert('Login FAILED !');
                return;
            }
            else {
                if ( debug == 1 ) console.log('Processing 2D Array');
                $('#btnGroupMgt').hide();
                $('#btnUserMgt').hide();
                var bGetPendingFiles = 0;
                var pwChanged = 0;

                var fLen = A1.length;
                var n = 0;
                global_adminsid = "";

                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname.toUpperCase();
                    var val = A1[i].itemval;

                    //ikey = cleanTxtStr( ikey, 0 );
                    //val = cleanTxtStr( val, 0 );

                    if ( debug == 1 ) console.log('IKEY: ', ikey + " = " + val);

                    if ( ikey == 'TEMPPW' ) {
                        alert("Temporary PW Expired, please contact an administrator for reset. ");
                        $('#lblStatus').text('Temporary PW Expired, please contact an administrator for reset.');
                        pwChanged = 0;
                        return false;
                    }
                    if ( ikey == 'ERROR' ) {
                        alert("Failed login: " + val);
                        $('#lblStatus').text('Failed login for: ' + userid);
                        pwChanged = 0;
                    }

                    if ( ikey == 'ISADMIN' && val == 1 ) {
                        global_isadmin = 0;
                        $('#btnGroupMgt').show();
                        $('#btnUserMgt').show();
                    }
                    if ( ikey == 'SUCCESS' && val == 0 ) {
                        $('#lblStatus').text('LOGGED in as ' + userid);
                        pwChanged = 0;
                        bGetPendingFiles = 1;
                    }
                    if ( ikey == 'SUCCESS' && val == 1 ) {
                        bLoginSuccess = 1;
                        $('#lblStatus').text('LOGGED in as ' + userid);
                        pwChanged = 1;
                        bGetPendingFiles = 1;

                        global_sessionid = get2dItemValue(A1, 'sessionid');
                        global_adminsid = get2dItemValue(A1, 'AdminSID');
                        global_isadmin = get2dItemValue(A1, 'isadmin');

                        if ( debug == 1 )
                            console.log("global_isadmin: ", global_isadmin);

                        if ( global_isadmin == 1 ) {
                            $('#btnGroupMgt').show();
                            $('#btnUserMgt').show();
                        }
                        else {
                            $('#btnGroupMgt').hide();
                            $('#btnUserMgt').hide();
                        }

                        if ( debug == 1 ) console.log("XX global_adminsid = " + global_adminsid);
                        if ( debug == 1 ) console.log("XX global_isadmin = " + global_isadmin);
                        if ( debug == 1 ) print2dArray(A1);
                    }

                    if ( ikey.toUpperCase() == 'ADMINSID' ) {
                        global_adminsid = val;
                        if ( debug == 1 ) console.log("ADMINSID = " + global_adminsid);
                    }
                    if ( ikey == 'ISADMIN' ) {
                        global_isadmin = val;
                        if ( debug == 1 ) console.log("ADMINSID = " + global_adminsid);
                        $('#btnGroupMgt').show();
                        $('#btnUserMgt').show();

                        getGroupMembersAdmin();
                    }

                    if ( bGetPendingFiles == 1 ) {
                        getMemberPendingFiles('006');
                    }
                }
                ;

                if ( debug == 1 ) console.log('bLoginSuccess = ', bLoginSuccess);
                if ( bLoginSuccess == 1 ) {
                    var userhash = CryptoJS.SHA1(userid);
                    if ( sessionStorage.getItem('curruserhash') == val ) {
                        if ( debug == 1 ) console.log('HASH Verified...');
                    }
                    else {
                        console.log('Notice HASH:', sessionStorage.getItem('curruserhash'), val);
                    }
                    //}
                    if ( debug == 1 )
                        console.log('Results: ', ikey, val);

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
                    getMemberPendingFiles('007');
                    //*****************************************************
                }
                else {
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
                        alert("A001 - Failed to login...");

                    if ( debug == 1 ) console.log("loginMember global_login_attempts: ", global_login_attempts);

                    sessionStorage.setItem('UID', '');
                    sessionStorage.setItem('PW', '');
                    sessionStorage.setItem('SID', '');
                }
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('load loginMember - xhr.addEventListener: ', xhr.readyState);
    }, false);

    if ( debug == 1 ) console.log('Posting to url: ', url);

    xhr.open("POST", url, true);

    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('Login ERROR!', e.toString());
    }
}

function chgPw () {
    var pwDebug = 0;
    var url = 'php/chgPw.php';

    var userid = $('#pwUserid').val();
    var pw = $('#pwPassword').val();
    var newpw = $('#pwNew').val();
    var oldpw = $('#pwNew2').val();
    var sid = global_sessionid;

    var obj;
    var phpurl = global_php_url + 'chgPw.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);
    var d = new Date();
    var n = d.getTime();

    if ( oldpw != newpw ) {
        alert("The proposed password is not equal to the retyped password, returning.");
        return;
    }

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    //$userid = $argv[1];
    //$pw = $argv[2];
    //$oldpw = $argv[3];
    //$newpw = $argv[4];
    //$sid = $argv[5];

    fd.append("userid", userid);
    fd.append("pw", pw);
    fd.append("oldpw", oldpw);
    fd.append("newpw", newpw);
    fd.append("sessionid", sid);

    xhr.onreadystatechange = function () {
        if ( pwDebug == 1 ) console.log('00 loginMember');
        if ( pwDebug == 1 ) console.log('xhr.status:', xhr.status);
        if ( pwDebug == 1 ) console.log('xhr.readyState: ', xhr.readyState);

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var jsonStr = xhr.responseText;

            //************************************************************
            if ( pwDebug == 1 ) console.log('jsonStr BEFORE : ', jsonStr);
            var xJson = 0;
            if ( xJson == 1 ) {
                jsonStr = extractJson(jsonStr);
                if ( pwDebug == 1 ) console.log('jsonStr AFTER: ', jsonStr);
            }
            //************************************************************

            if ( !jsonStr ) {
                alert('Login FAILED !');
                return;
            }
            else {
                var obj = JSON.parse(jsonStr);
                var pwChanged = 0;

                var n = jsonStr.indexOf('"SUCCESS":"1"');
                if ( n >= 0 ) {
                    alert("SUCCESSFUL LOGIN");
                    console.log("***** LOGIN SUCCESS *****");
                    pwChanged = 1;
                }

                jQuery.each(obj, function (ikey, val) {
                    if ( pwDebug == 1 ) {
                        console.log('jsonStr: ', ikey, val);
                    }
                    if ( ikey == 'memberhash' ) {
                        if ( pwDebug == 1 ) console.log('NEW memberhash: ', val);
                    }
                    if ( ikey == 'pwhash' ) {
                        if ( pwDebug == 1 ) console.log('NEW PWHASH: ', val);
                    }
                    if ( ikey == 'ERROR' ) {
                        alert("Failed password change: " + val);
                        $('#lblStatus').text('Failed login for: ' + userid);
                        pwChanged = 0;
                    }
                    if ( ikey == 'SUCCESS' && val == 0 ) {
                        $('#lblStatus').text('Failed to change password! ');
                        pwChanged = 0;
                    }
                    if ( ikey == 'SUCCESS' && val == 1 ) {
                        $('#lblStatus').text('LOGGED in as ' + userid);
                        if ( pwDebug == 1 )
                            console.log('**** SUCCESS ****   : ', ikey, val);
                        pwChanged = 1;
                    }
                });

                if ( pwDebug == 1 ) console.log('pwChanged = ', pwChanged);
                if ( pwChanged == 1 ) {
                    alert("Password successfully changed!");
                    systemRestart();
                    return;
                }
                else {
                    alert("FAILED to change Password !");
                }
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( pwDebug == 1 ) console.log('load change pw - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0X1 - Error xhr.send : ", e); // pass exception object to error handler
        alert('Change PW ERROR!', e.toString());
    }
}