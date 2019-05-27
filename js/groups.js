/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function getMyGroups () {
    var debug = 0;
    var url = 'php/' + 'getGroups.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getGroups.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    //console.log( "*************************************************");
    //console.log( "sessionid", sid );
    //console.log( "*************************************************" );

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

    if ( debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
    }

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 getMyGroups');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('getMyGroups 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( debug == 1 ) {
                    console.log("@getMyGroups:");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( debug == 1 ) print2dArray(A1);
                //*******************************************************************

                if ( debug == 1 ) console.log('getMyGroups 11')
                var str = "";
                var selectname = '';
                $("#lbMyGroups").empty();
                $('#lbMyGroups').append($("<option/>", {value: 0, text: "*NONE Selected"}));

                var fLen = A1.length;
                var n = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname;
                    var GroupName = A1[i].itemval;
                    if ( debug == 1 ) console.log('==> Adding to list: ' + GroupName + ' for user: ' + userid);
                    $('#lbMyGroups').append($("<option/>", {
                        value: GroupName,
                        text: GroupName
                    }));

                    //console.log( '** APPENDED: ' + ikey + " / " + GroupName );
                }

                $("#lbMyGroups").selectmenu("refresh");
                selectFirstItem("lbMyGroups");
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('getMyGroups - xhr.addEventListener: ', xhr.readyState);
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

function getGroupsMgt () {
    var GGM_debug = 0;
    var url = 'php/' + 'getGroupsAdmin.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getGroups.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var CurrentlySelectedGroup = localStorage.getItem("global_SelectedGroup");

    if ( CurrentlySelectedGroup == undefined ) {
        localStorage.setItem("global_SelectedGroup", "US");
        console.log("CurrentlySelectedGroup set  to US by default...");
    }

    //console.log( "*************************************************");
    //console.log( "sessionid", sid );
    //console.log( "*************************************************" );

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
    fd.append("adminsid", global_adminsid);

    if ( GGM_debug > 0 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("global_adminsid", global_adminsid);
    }

    xhr.onreadystatechange = function () {
        if ( GGM_debug == 1 ) console.log('00 getGroupsAdmin');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( GGM_debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('getGroupsMgt 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( GGM_debug == 1 ) {
                    console.log("@getGroupsMgt:");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( GGM_debug == 1 ) print2dArray(A1);
                //*******************************************************************

                if ( GGM_debug == 1 ) console.log('getGroupsMgt 11')
                var str = "";
                var selectname = '';

                $("#lbAdminGroup").empty();
                $('#lbAdminGroup').append($("<option/>", {id: 00, value: 0, text: "*NONE Selected"}));

                var str = "";
                var fLen = A1.length;
                var n = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname;
                    var GroupName = A1[i].itemval;

                    GroupName = GroupName.trim();

                    if ( GGM_debug == 1 ) console.log('** PROCESSING: ' + ikey + " / " + GroupName);

                    if ( GroupName.length == 0 ) {
                        if ( GGM_debug == 1 ) console.log('ikey = ' + i + ' is zero length.');
                    }
                    else {
                        GroupName = cleanTxtStr(GroupName, 0);
                        $('#lbAdminGroup').append($("<option/>", {
                            id: GroupName,
                            value: GroupName,
                            text: GroupName
                        }));

                        if ( GGM_debug == 1 ) console.log('** APPENDED: ' + ikey + " / " + GroupName);
                    }
                }

                //$( "#lbAdminGroup" ).selectmenu( "refresh" );
                //selectFirstItem( "lbAdminGroup" );

                if ( CurrentlySelectedGroup != null && CurrentlySelectedGroup != undefined ) {
                    $("#lbAdminGroup").selectmenu("refresh");
                    var tgtcb = '#' + CurrentlySelectedGroup;
                    var tgtid = "cb" + CurrentlySelectedGroup;
                    var tgtid2 = "mb" + CurrentlySelectedGroup;

                    if ( GGM_debug == 1 ) {
                        var elementExists = document.getElementById(tgtid);
                        console.log('** 0 elementExists : ', elementExists, tgtid);
                        elementExists = document.getElementById(tgtid);
                        console.log('** 1 elementExists : ', elementExists, CurrentlySelectedGroup);
                        elementExists = document.getElementById(tgtid2);
                        console.log('** 2 elementExists : ', elementExists, tgtid2);
                        elementExists = $(tgtcb).selected;
                        console.log('** 3 elementExists : ', elementExists, tgtcb);
                    }

                    try {
                        //console.log( "try 01: " + tgtcb );
                        document.getElementById(tgtcb).selected = true;
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 01 SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("01 - NOTICE Selection Set : ", e);
                    }

                    try {
                        //console.log( "try 02:" + tgtcb );
                        $(tgtcb).prop('selected', true);
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 01 SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("02 - NOTICE Selection Set : ", e);
                    }
                    try {
                        //console.log( "try 03: " + tgtid );
                        $(tgtid).prop('selected', true);
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 02 SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("03 - NOTICE Selection Set : ", e);
                    }
                    try {
                        //console.log( "try 04: " + tgtid2 );
                        //$( tgtid2 ).prop( 'selected', true );
                        document.getElementById(tgtid2).selected = true;
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 04 SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("04 - NOTICE Selection Set : ", e);
                    }
                    try {
                        //console.log( "try 04a: " + tgtid2 );
                        $("#" + tgtid2).prop('selected', true);
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 04a SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("04a - NOTICE Selection Set : ", e);
                    }
                    try {
                        //console.log( "try 05" );
                        document.getElementById(tgtid).selected = true;
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 05 SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("05 - NOTICE Selection Set : ", e);
                    }
                    try {
                        //console.log( "try 06" );
                        document.getElementsByName(CurrentlySelectedGroup).selected = true;
                        $("#lbAdminGroup").selectmenu("refresh");
                        //console.log( "try 06 SUCCESSFUL: " + tgtid2 );
                    }
                    catch ( e ) {
                        console.log("06 - NOTICE Selection Set : ", e);
                    }
                }
                else {
                    $("#lbAdminGroup option:first").attr('selected', 'selected');
                    $("#lbAdminGroup").selectmenu("refresh");
                }

                if ( GGM_debug == 1 ) console.log('** APPEND COMPLETE for lbUserGroups');
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( GGM_debug == 1 ) console.log('getGroupsAdmin - xhr.addEventListener: ', xhr.readyState);
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

function getGroupsAdmin () {
    var GA_debug = 0;
    var url = 'php/' + 'getGroupsAdmin.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getGroups.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    //console.log( "*************************************************");
    //console.log( "sessionid", sid );
    //console.log( "*************************************************" );

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
    fd.append("adminsid", global_adminsid);

    if ( GA_debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("global_adminsid", global_adminsid);
    }

    xhr.onreadystatechange = function () {
        if ( GA_debug == 1 ) console.log('00 getGroupsAdmin');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( GA_debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('getGroupsAdmin 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( GA_debug == 1 ) {
                    console.log("@getGroupsAdmin:");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( GA_debug == 1 ) print2dArray(A1);
                //*******************************************************************

                if ( GA_debug == 1 ) console.log('getGroupsAdmin 11')
                var str = "";
                var selectname = '';

                $("#lbAdminGroup").empty();
                $('#lbAdminGroup').append($("<option/>", {value: 0, text: "*NONE Selected"}));

                $("#divGroupMembers").empty();

                var str = "";
                var fLen = A1.length;
                var n = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname;
                    var GroupName = A1[i].itemval;

                    GroupName = GroupName.trim();

                    if ( GA_debug == 1 ) console.log('** PROCESSING: ' + ikey + " / " + GroupName);

                    if ( GroupName.length == 0 ) {
                        if ( GA_debug == 1 ) console.log('ikey = ' + i + ' is zero length.');
                    }
                    else {
                        GroupName = cleanTxtStr(GroupName, 0);
                        str = '    <label><input id ="cb' + GroupName + '" type="checkbox" name="option[]" value="' + GroupName + '" />' + GroupName + '</label>'
                        $("#divGroupMembers").append(str);

                        $('#lbAdminGroup').append($("<option/>", {
                            value: GroupName,
                            text: GroupName
                        }));

                        if ( GA_debug == 1 ) console.log('** APPENDED: ' + ikey + " / " + GroupName);
                    }
                }

                $("#lbAdminGroup").selectmenu("refresh");
                //selectFirstItem( "lbAdminGroup" );
                $("#lbAdminGroup option:first").attr('selected', 'selected');
                $("#lbAdminGroup").selectmenu("refresh");

                if ( GA_debug == 1 ) console.log('** APPEND COMPLETE for lbUserGroups');
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( GA_debug == 1 ) console.log('getGroupsAdmin - xhr.addEventListener: ', xhr.readyState);
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

function showGroupMembersOnly (TgtDivID, ListID) {
    var debug = 0;
    var url = 'php/' + 'getAllGroupMembers.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getAllGroupMembers.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);
    var xhr;
    var fd;
    var SelectedGroup = localStorage.getItem("global_SelectedGroup");

    //console.log( '**************************************************************' );
    if ( debug == 1 ) {
        console.log("showGroupMembersOnly TgtDivID", TgtDivID);
        console.log("showGroupMembersOnly SelectedGroup", SelectedGroup);
    }

    if ( SelectedGroup == 'undefined' ) {
        alert("ERROR 001d: showGroupMembersOnly TgtDivID undefined, aborting...");
        return;
    }

    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    //fd.append( "pw", pw );
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("SelectedGroup", SelectedGroup);

    if ( debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("global_adminsid", global_adminsid);
    }

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 showGroupMembersOnly');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('showGroupMembersOnly 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( debug == 1 ) {
                    console.log("@showGroupMembersOnly:");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( debug == 1 ) print2dArray(A1);
                //*******************************************************************

                if ( debug == 1 ) console.log('showGroupMembersOnly EMPTY: ', "<#" + TgtDivID + ">");
                var str = "";
                var selectname = '';

                $("#" + TgtDivID).empty();
                $("#" + TgtDivID).append($("<option/>", {value: 0, text: "*NONE Selected"}));

                //$( "#divGroupMembers" ).empty();

                var str = "";
                var fLen = A1.length;
                var n = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname;
                    var MemberID = A1[i].itemval;

                    if ( debug == 1 ) console.log('showGroupMembersOnly ikey:', ikey, " / MemberID: ", MemberID);

                    if ( typeof MemberID !== 'undefined' ) {
                        if ( debug == 1 ) console.log('showGroupMembersOnly PROCESSING ikey:', ikey, " / MemberID: ", MemberID);

                        MemberID = MemberID.trim();

                        if ( MemberID.length == 0 ) {
                            if ( debug == 1 ) console.log('ikey = ' + i + ' is zero length.');
                        }
                        else {
                            MemberID = cleanTxtStr(MemberID, 0);
                            str = '    <label id="lbl' + MemberID + '"><input id ="mb' + MemberID + '" type="checkbox" name="option[]" value="' + MemberID + '" onclick = "memberClicked(\'divAdminGroupMembers\', $(this), \'' + MemberID + '\');"' + ' />' + MemberID + '</label>'
                            //str = '    <label id="lbl' + MemberID + '"><input id ="mb' + MemberID + '" type="checkbox" name="option[]" checked="checked" value="' + MemberID + '" />' + MemberID + '</label>'
                            $("#" + TgtDivID).append(str);

                            if ( debug == 1 ) {
                                console.log('** TgtDivID: ', TgtDivID);
                                console.log('** APPENDING: ', MemberID);
                                console.log('** STR: ', str);
                            }

                            if ( debug == 1 ) console.log('** APPENDED: ' + ikey + " / " + MemberID);
                        }
                    }
                    else {
                        if ( debug == 1 ) {
                            console.log('** ikey: ' + ikey + " found to be undefined, skipping.");
                        }
                    }
                }
                $("#" + ListID).selectmenu("refresh");
                if ( debug == 1 ) console.log('** APPEND COMPLETE for lbUserGroups');
                $('#' + TgtDivID).find('input[type=checkbox]').each(function () {
                    this.checked = true;
                });

                if ( SelectedGroup.length > 0 ) {
                    var tgtcb = '#' + SelectedGroup;
                    var tgtid = "cb" + SelectedGroup;
                    $(tgtcb).prop('selected', true);

                    var elementExists = document.getElementById(tgtid);
                    //console.log( '** elementExists : ', elementExists, tgtid );
                    elementExists = document.getElementById(tgtid);
                    //console.log( '** elementExists : ', elementExists, SelectedGroup );
                }
                else
                    $("#lbAdminGroup option:first").attr('selected', 'selected');

                $("#lbAdminGroup").selectmenu("refresh");
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('showGroupMembersOnly - xhr.addEventListener: ', xhr.readyState);
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

function checkGroupMembers () {
    var debug = 1;
    var url = 'php/' + 'getAllGroupMembers.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getAllGroupMembers.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);
    var xhr;
    var fd;
    var SelectedGroup = localStorage.getItem("global_SelectedGroup");

    console.log('**************************************************************');
    if ( debug == 1 ) {
        console.log("checkGroupMembers SelectedGroup", SelectedGroup);
    }

    if ( SelectedGroup == 'undefined' ) {
        alert("ERROR 001d: checkGroupMembers GROUP undefined, aborting...");
        return;
    }

    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    //fd.append( "pw", pw );
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("SelectedGroup", SelectedGroup);

    if ( debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("global_adminsid", global_adminsid);
    }

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 checkGroupMembers');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('checkGroupMembers 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( debug == 1 ) {
                    console.log("@checkGroupMembers:");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( debug == 1 ) print2dArray(A1);
                //*******************************************************************

                var str = "";
                var selectname = '';
                var fLen = A1.length;
                var n = 0;

                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname;
                    var MemberID = A1[i].itemval;

                    if ( ikey.length > 1 && MemberID.length > 1 ) {
                        if ( debug == 1 ) console.log('checkGroupMembers ikey:', ikey, " / MemberID: ", MemberID);

                        MemberID = MemberID.trim();
                        MemberID = 'mb' + MemberID;
                        if ( MemberID.length == 0 ) {
                            if ( debug == 1 ) console.log('ikey = ' + i + ' is zero length.');
                        }
                        else {
                            MemberID = cleanTxtStr(MemberID, 0);
                            if ( debug == 1 ) console.log('@!!@ SET CHECKED: ' + ikey + " / " + MemberID);
                            $("#" + MemberID).prop('checked', true);
                        }
                    }
                }
                //$( "#" + ListID ).selectmenu( "refresh" );
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('checkGroupMembers - xhr.addEventListener: ', xhr.readyState);
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

function sortMemberShipGroups () {
    $('#divAdminGroupMembers label').sort(function (a, b) {
        var $a = $(a).find(':checkbox'),
            $b = $(b).find(':checkbox');

        if ( $a.hasClass('default') && !$b.hasClass('default') )
            return -1;
        else if ( !$a.hasClass('default') && $b.hasClass('default') )
            return 1;

        if ( $a.is(':checked') && !$b.is(':checked') )
            return -1;
        else if ( !$a.is(':checked') && $b.is(':checked') )
            return 1;

        if ( $a.val() < $b.val() )
            return -1;
        else if ( $a.val() > $b.val() )
            return 1;

        return 0;
    }).appendTo('#divAdminGroupMembers');

    $('#divAdminGroupMembers .default:last, #divAdminGroupMembers :checked:last').closest('label').after('<hr />');
}

function markAdminGroupMembers () {
    //console.log( "Entering markAdminGroupMembers" );
    var GM_debug = 0;
    var url = 'php/' + 'getGroupMembers.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getGroupMembers.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var SelectedGroup = localStorage.getItem("global_SelectedGroup");

    if ( GM_debug == 1 ) console.log('markAdminGroupMembers SelectedGroup: ', SelectedGroup);

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("SelectedGroup", SelectedGroup);
    fd.append("userid", userid);
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);

    xhr.onreadystatechange = function () {
        if ( GM_debug == 1 ) console.log('00 markAdminGroupMembers');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;
            results = extractJson(xhr.responseText);

            if ( GM_debug == 1 ) {
                console.log('....................................................');
                console.log('xhr.responseText: ', xhr.responseText);
                console.log('....................................................');
                console.log('results: ', results);
                console.log('....................................................');
            }

            if ( !results ) {
                console.log('No group members  found.');
                return;
            }
            else {
                if ( GM_debug == 1 ) console.log('markAdminGroupMembers results: ', results);
                var obj = JSON.parse(results);

                if ( results.indexOf('{') < 0 ) {
                    $('#lblStatus').html('No members found in group: ' + SelectedGroup);
                    console.log('No members found in group: ' + SelectedGroup);
                    return;
                }

                if ( GM_debug == 1 ) console.log('markAdminGroupMembers 11')
                var str = "";
                var selectname = '';

                $('#divAdminGroupMembers').find('input[type=checkbox]:checked').removeAttr('checked');

                $("input:checkbox").each(function () {
                    var $this = $(this);

                    if ( $this.is(":checked") ) {
                        console.log('Checked' + $this.attr("id"));
                    }
                    else {
                        console.log('NOT Checked' + $this.attr("id"));
                    }
                });

                jQuery.each(obj, function (key1, val) {
                    //*******************************************************************
                    var jstr = JSON.stringify(val); // (Optional) beautiful indented output.
                    var A1 = new Array();
                    A1 = build2dArray(jstr, 0);

                    //if ( GM_debug == 1 ) console.log( "A1 to string: ", A1.toString() );
                    if ( GM_debug == 1 ) print2dArray(A1);

                    var fLen = A1.length;
                    var n = 0;
                    for ( i = 0; i < fLen; i++ ) {
                        if ( typeof ( A1[i].itemval ) !== "undefined" ) {
                            var ikey = A1[i].itemname;
                            var MemberID = A1[i].itemval;
                            if ( MemberID.length > 0 ) {
                                val2 = 'mb' + MemberID;
                                var lblid = 'lbl' + MemberID;
                                $('#' + lblid).remove();

                                if ( GM_debug == 1 ) {
                                    console.log("REMOVING : ", lblid);
                                    var x = $('#' + val2).prop('outerHTML');
                                    console.log("outerHTML VAL2: ", x);
                                }

                                val2 = cleanTxtStr(val2, 0);

                                if ( debug == 1 ) console.log('@!!@ SET CHECKED: ' + ikey + " / " + val2);

                                //$( "#" + val2 ).prop( 'checked', true );
                                MemberID = cleanTxtStr(MemberID, 0);
                                str = '    <label id="lbl' + MemberID + '"><input id ="mb' + MemberID + '" type="checkbox" name="option[]" value="' + MemberID + '" onclick = "memberClicked(\'divAdminGroupMembers\', $(this), \'' + MemberID + '\');"' + ' checked="checked" />' + MemberID + '</label>'
                                //str = '    <label><input id ="mb' + MemberID + '" type="checkbox" name="option[]" value="' + MemberID + '" onclick = "memberClicked(\'divAdminGroupMembers\', $(this), \'' + MemberID.toUpperCase() + '\');"' + ' />' + MemberID + '</label>'
                                //str = '    <label><input id ="mb' + MemberID + '" type="checkbox" name="option[]" value="' + MemberID + '" />' + MemberID + '</label>'
                                $("#divAdminGroupMembers").append(str);

                                //$( 'input.type_checkbox[value="' + MemberID + '"]' ).prop( 'checked', true );
                            }
                            else if ( GM_debug == 1 ) console.log(' MemberID: ' + MemberID + ", is null, skipping...");
                        }
                        else {
                            console.log("A1[i].itemval is undefined...");
                        }
                        //$( "#divAdminGroupMembers" ).refresh
                    }
                    //*******************************************************************
                });
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( GM_debug == 1 )
            console.log('markAdminGroupMembers - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("markAdminGroupMembers 0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('markAdminGroupMembers ERROR!', e.toString());
    }
}

function getMyGroupMembers () {
    //#lbMyGroupMembers
    var GM_debug = 0;
    var url = 'php/' + 'getGroupMembers.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getGroupMembers.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var SelectedGroup = getSelectedGroup();

    if ( GM_debug == 1 ) console.log('getMyGroupMembers SelectedGroup: ', SelectedGroup);

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("SelectedGroup", SelectedGroup);
    fd.append("userid", userid);
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);

    xhr.onreadystatechange = function () {
        if ( GM_debug == 1 ) console.log('00 getMyGroupMembers');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;
            results = extractJson(xhr.responseText);

            if ( GM_debug == 1 ) console.log('....................................................');
            if ( GM_debug == 1 ) console.log('xhr.responseText: ', xhr.responseText);
            if ( GM_debug == 1 ) console.log('....................................................');
            if ( GM_debug == 1 ) console.log('results: ', results);
            if ( GM_debug == 1 ) console.log('....................................................');

            if ( !results ) {
                console.log('No group members  found.');
                return;
            }
            else {
                if ( GM_debug == 1 ) console.log('getMyGroupMembers results: ', results);
                var obj = JSON.parse(results);

                if ( results.indexOf('{') < 0 ) {
                    $('#lblStatus').html('No members found in group: ' + SelectedGroup);
                    return;
                }

                if ( GM_debug == 1 ) console.log('getMyGroupMembers 11')
                var str = "";
                var selectname = '';

                $("#lbMyGroupMembers").empty();
                $("#lbMyGroupMembers").append($("<option/>", {value: 0, text: "*ALL"}));

                jQuery.each(obj, function (key1, val) {
                    if ( GM_debug == 1 ) console.log('key1: ', key1);
                    if ( GM_debug == 1 ) console.log('val: ', val);

                    var val2 = val['FromEmail'].toString();

                    if ( GM_debug == 1 ) console.log('Name: ', val2);
                    if ( val2.length > 0 ) {
                        $("#lbMyGroupMembers").append($("<option/>", {
                            value: val2,
                            text: val2
                        }));
                    }
                    if ( GM_debug == 1 ) console.log('Results: ', key1, val);
                });

                $("#lbMyGroupMembers").selectmenu("refresh");
                //selectFirstItem( "lbMyGroupMembers" );
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( GM_debug == 1 )
            console.log('getMyGroupMembers - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("getMyGroupMembers 0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('getMyGroupMembers ERROR!', e.toString());
    }
}

function getMemberGroups () {
    var tgtuserid = $('#lbAdminUser').val()
    var SelectedVal = $('#lbAdminUser').val()

    if ( SelectedVal == '0' ) {
        console.log('getMemberGroups: No user selected, returning..');
        return;
    }

    var GMG_debug = 0;
    var url = 'php/' + 'getMemberGroups.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getMemberGroups.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var SelectedGroup = getSelectedGroup();

    if ( GMG_debug == 1 ) console.log('getMemberGroups SelectedGroup: ', SelectedGroup);
    if ( GMG_debug == 1 ) console.log('getMemberGroups: Selected user = ' + SelectedVal);

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("SelectedGroup", SelectedGroup);
    fd.append("userid", userid);
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("tgtuserid", tgtuserid);

    xhr.onreadystatechange = function () {
        if ( GMG_debug == 1 ) console.log('00 getMemberGroups');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;
            results = extractJson(xhr.responseText);

            if ( GMG_debug == 1 ) console.log('....................................................');
            if ( GMG_debug == 1 ) console.log('getMemberGroups xhr.responseText: \n', xhr.responseText);
            if ( GMG_debug == 1 ) console.log('....................................................');
            if ( GMG_debug == 1 ) console.log('getMemberGroups results: \n', results);
            if ( GMG_debug == 1 ) console.log('....................................................');

            if ( !results ) {
                console.log('No group members  found.');
                return;
            }
            else {
                if ( GMG_debug == 1 ) console.log('getMemberGroups results: ', results);
                var obj = JSON.parse(results);

                if ( results.indexOf('{') < 0 ) {
                    $('#lblStatus').html('No members found in group: ' + SelectedGroup);
                    return;
                }

                if ( GMG_debug == 1 ) console.log('getMemberGroups 11')

                var str = "";
                var selectname = '';
                gMemberGroups = [];

                jQuery.each(obj, function (key1, val) {
                    if ( GMG_debug == 1 ) console.log('key1: ', key1);
                    if ( GMG_debug == 1 ) console.log('val: ', val);

                    var val2 = val['GroupName'];

                    if ( GMG_debug == 1 ) console.log('GroupName: ', val2);
                    if ( val2.length > 0 ) {
                        if ( gMemberGroups.includes(val2) ) {
                            if ( GMG_debug == 1 ) console.log('Member Already exists: ' + val2);
                        }
                        else {
                            gMemberGroups.push(val2);
                            if ( GMG_debug == 1 ) console.log('ADDED Member : ', val2);
                        }
                    }

                    if ( GMG_debug == 1 ) console.log('Results: ', key1, val);
                });

                var item = "";
                var itemid = "";
                var arrayLength = gMemberGroups.length;
                if ( arrayLength > 0 ) {
                    for ( var i = 0; i < arrayLength; i++ ) {
                        itemid = '#cb' + gMemberGroups[i];
                        if ( GMG_debug == 1 ) console.log("@@ itemid: " + gMemberGroups[i]);
                        $(itemid).prop('checked', true);
                        //$( '.myCheckbox' ).prop( 'checked', false );
                        if ( GMG_debug == 1 ) console.log('CHECKED : ', itemid);
                    }
                }
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( GMG_debug == 1 )
            console.log('getMemberGroups - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("getMemberGroups 0C - Error xhr.send : ", e); // pass exception object to error handler
        alert('getMemberGroups ERROR!', e.toString());
    }
}

function setUserBtns () {
    //var SelectedText = $( '#lbAdminUser:selected' ).val();
    var SelectedVal = $('#lbAdminUser').val()
    var SelectedText = $('#lbAdminUser').val()
    //console.log( 'SelectedVal = ' + SelectedVal );
    //console.log( 'SelectedText = ' + SelectedText );
    if ( SelectedVal == '0' ) {
        $('#btnResetPw').hide();
        $('#btnDeleteUser').hide();
        $('#txtUserName').show();
        $('#btnAddUser').show();
    }
    else {
        $('#btnResetPw').show();
        $('#btnDeleteUser').show();
        $('#txtUserName').hide();
        $('#btnAddUser').hide();
    }
}

//**********************************************************************************

function addNewGroup () {
    event.preventDefault();
    var debug = 0;
    var url = 'php/' + 'insertGroup.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'insertGroup.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var GroupName = $('#txtGroupName').val();

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
    fd.append("groups", GroupName);

    if ( debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("GroupName", GroupName);
    }

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 getMyGroups');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('getMyGroups 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                event.stopPropagation();
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( debug == 1 ) {
                    console.log("---------------------------------------------------------");
                    console.log("results:", results);
                    console.log("---------------------------------------------------------");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( debug == 1 ) print2dArray(A1);
                //*******************************************************************
                var fLen = A1.length;
                var n = 0;
                global_adminsid = "";
                $bSuccess = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname.toUpperCase();
                    var val = A1[i].itemval;
                    if ( ikey == "SUCCESS" & val == "1" ) {
                        $bSuccess = 1;
                    }
                }
                if ( $bSuccess == 1 ) {
                    alert('** SUCCESSFUL INSERT of ' + GroupName);
                    insertNewGroupToList();
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('getMyGroups - xhr.addEventListener: ', xhr.readyState);
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

function deleteGroup () {
    event.preventDefault();

    var DG_debug = 0;

    var url = 'php/' + 'deleteGroup.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'deleteGroup.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var GroupName = getSelectedGroup();
    var bDelete = ConfirmDelete();
    if ( bDelete == false ) {
        alert("Delete cancelled...");
        return;
    }
    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    //fd.append( "pw", pw );
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("groups", GroupName);

    if ( DG_debug == 1 ) console.log('00 deleteGroup: ', GroupName);

    if ( DG_debug == 1 ) {
        console.log("deleteGroup userid", userid);
        console.log("deleteGroup pw", pw);
        console.log("deleteGroup pwhash", pw);
        console.log("deleteGroup sessionid", sid);
        console.log("deleteGroup GroupName", GroupName);
    }

    xhr.onreadystatechange = function () {
        if ( DG_debug == 1 ) console.log('00 deleteGroup');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( DG_debug == 1 ) {
                console.log('************ deleteGroup *******************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('getMyGroups 10', results);
                console.log('********************************************');
            }

            if ( !results ) {
                console.log('No group membership found.');
                event.stopPropagation();
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                //var obj = JSON.parse( jsonresults );

                //*******************************************************************
                var key1 = "";
                var val = "";
                var obj = xhr.responseText;
                var results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( DG_debug == 1 ) {
                    console.log("---------------------------------------------------------");
                    console.log("results:", results);
                    console.log("---------------------------------------------------------");
                    console.log("---------------------------------------------------------");
                    console.log("jstr:", jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( DG_debug == 1 ) print2dArray(A1);
                //*******************************************************************
                var fLen = A1.length;
                var n = 0;
                global_adminsid = "";
                $bSuccess = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname.toUpperCase();
                    var val = A1[i].itemval;

                    if ( DG_debug == 1 ) console.log("ikey/val:" + ikey + "/" + val);

                    if ( ikey == "SUCCESS" & val == "1" ) {
                        $bSuccess = 1;
                        removeDeletedGroupFromList();
                    }
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( DG_debug == 1 ) console.log('getMyGroups - xhr.addEventListener: ', xhr.readyState);
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

function insertNewGroupToList () {
    var ING_debug = 0;
    var GroupName = $("#txtGroupName").val();
    if ( ING_debug == 1 ) console.log('insertNewGroupToList Adding <' + GroupName + '> to list.');
    $('#lbAdminGroup').append($("<option/>", {
        value: GroupName,
        text: GroupName
    }));
    $("#lbAdminGroup").selectmenu("refresh");

    $("#lbAdminGroup").html($('#lbAdminGroup option').sort(function (x, y) {
        return $(x).text() < $(y).text() ? -1 : 1;
    }))
    $("#lbAdminGroup").get(0).selectedIndex = 0;

    if ( ING_debug == 1 ) console.log('Added <' + GroupName + '> to list.');
}

function removeDeletedGroupFromList () {
    var RDG_debug = 0;
    var GroupName = getSelectedGroup();
    if ( RDG_debug == 1 ) console.log('removeDeletedGroupFromList removing: ', GroupName);
    if ( GroupName != null ) {
        $("#lbAdminGroup option[value='" + GroupName + "']").remove();
        $("#lbAdminGroup").selectmenu("refresh");
        alert("Group '" + GroupName + " deleted.")
    }
    else {
        alert("Group '" + GroupName + " MAY NOT BE Deleted.")
    }
}

function getSelectedGroup () {
    global_SelectedGroup = localStorage.getItem("global_SelectedGroup");
    return global_SelectedGroup;
}

function showSelectedGroupMembers () {
    event.preventDefault();

    var SMG_debug = 0;
    var selected = [];
    var i = 0;

    if ( SMG_debug == 1 ) console.log('showSelectedGroupMembers STARTING');

    $('#divAdminGroupMembers input:checked').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        item = item.substring(2);
        if ( SMG_debug == 1 ) console.log('showSelectedGroupMembers Checked : ', item, i);
    });

    var l = selected.length;
    var items = "";
    for ( i = 0; i < l; i++ ) {
        str1 = selected[i];
        items += str1.substring(2) + "|";
        if ( SMG_debug == 1 ) console.log('Build Items: ', items);
    }

    if ( SMG_debug == 1 ) console.log('CALL SELECT PROCESS ON : ', items);
    if ( SMG_debug == 1 ) console.log('processGroups: COMPLETE', i);

    event.stopPropagation();

    return items;
}

function showAllGroupMembers () {
    event.preventDefault();

    var SMG_debug = 0;
    var selected = [];
    var i = 0;

    if ( SMG_debug == 1 ) console.log('showAllGroupMembers STARTING');

    $('#divAdminGroupMembers input:checked').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        item = item.substring(2);
        if ( SMG_debug == 1 ) console.log('showAllGroupMembers Checked : ', item, i);
    });
    $('#divAdminGroupMembers input:checkbox:not(checked)').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        item = item.substring(2);
        if ( SMG_debug == 1 ) console.log('showAllGroupMembers unChecked : ', item, i);
    });

    var l = selected.length;
    var items = "";
    for ( i = 0; i < l; i++ ) {
        str1 = selected[i];
        items += str1.substring(2) + "|";
        if ( SMG_debug == 1 ) console.log('Build Items: ', items);
    }

    if ( SMG_debug == 1 ) console.log('CALL SELECT PROCESS ON : ', items);
    if ( SMG_debug == 1 ) console.log('processGroups: COMPLETE', i);

    event.stopPropagation();

    return items;
}

//onclick="memberClicked(DivID, $(this));"
function memberClicked (e, t, MemberID) {
    //var ii = 1;
    //if ( ii == 1 )
    //    return;

    var SelectedGroup = localStorage.getItem("global_SelectedGroup");
    if ( t.is(':checked') ) {
        alert(MemberID + "is checked");
        console.log(MemberID + " is checked");
        //insertGroupMember( SelectedGroup, MemberID );
    }
    else {
        alert(MemberID + "is unchecked");
        console.log(MemberID + " is NOT checked");
        //var b = ConfirmGroupMemberDelete();
        //if ( b == true )
        //{
        //    deleteMemberFromGroup( SelectedGroup, MemberID );
        //    //deleteMemberFromGroupV2( GroSelectedGroupupID, MemberID );
        //}
    }
}