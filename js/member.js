/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function getGroupMembersAdmin () {
    var GMA_debug = 0;
    var url = 'php/' + 'getMemberList.php';
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;
    var adminsid = global_adminsid;
    var obj;
    var phpurl = global_php_url + 'getMemberList.php';

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", txtuserid);
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("adminsid", global_adminsid);

    if ( GMA_debug == 1 ) console.log('**** 00 getGroupMembersAdmin / starting...');

    xhr.onreadystatechange = function () {
        if ( GMA_debug == 1 ) console.log('00 getGroupMembersAdmin / ' + xhr.readyState + "/" + xhr.status);
        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            if ( GMA_debug == 1 ) console.log('01 getGroupMembersAdmin');
            var i = 0;
            var str = "";

            //var results = $.parseJSON(xhr.responseText);
            var results = xhr.responseText;

            if ( GMA_debug == 1 ) console.log('getGroupMembersAdmin - xhr.responseText: ' + xhr.responseText);
            if ( GMA_debug == 1 ) console.log('getGroupMembersAdmin - results: ', results);

            $("#lbAdminUser").empty();
            //$( "#divAdminGroupMembers" ).empty();

            $('#lbAdminUser').append($("<option/>", {value: 0, text: "*NONE Selected"}));

            //*******************************************************************
            var A1 = new Array();
            A1 = build2dArray(results, 0);
            if ( GMA_debug == 1 ) print2dArray(A1);
            //*******************************************************************

            var fLen = A1.length;
            var n = 0;
            var str = "";
            //***********************
            for ( i = 0; i < fLen; i++ ) {
                var ikey = A1[i].itemname;
                var MemberID = A1[i].itemval;
                if ( MemberID != undefined ) {
                    if ( MemberID.length > 0 ) {
                        MemberID = cleanTxtStr(MemberID, 0);
                        //str = '    <label><input id ="mb' + MemberID + '" type="checkbox" name="option[]" value="' + MemberID + '" onclick = "memberClicked(\'divAdminGroupMembers\', $(this), \'' + MemberID.toUpperCase() + '\');"' + ' />' + MemberID + '</label>'
                        //str = '    <label><input id ="mb' + MemberID + '" type="checkbox" name="AdminMember" value="' + MemberID + '" onclick = "memberClicked(\'divAdminGroupMembers\', $(this), \'' + MemberID.toUpperCase() + '\');"' + ' />' + MemberID + '</label>'
                        str = '    <option id= "mb' + MemberID + '" value="' + MemberID + '">' + MemberID + '</option> ';
                        if ( GMA_debug == 1 ) console.log('*** Append STR: ' + str);
                        //$( "#divAdminGroupMembers" ).append( str );
                        $("#lbAdminUser").append(str);
                        if ( GMA_debug == 1 ) console.log('** APPENDED: ' + ikey + " / " + MemberID);
                    }
                }
            }
            $("#lbAdminUser").selectmenu('refresh', true);
        }
    }

    xhr.addEventListener("load", function (evt) {
        console.log('NOTICE: getGroupMembersAdmin - Listener Active: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0DC - Error xhr.send : ", e); // pass exception object to error handler
    }
}

function PopulateDivAdminGroupMembers () {
    var PDGM_debug = 0;
    //var url = 'php/' + 'getMemberList.php';
    var url = 'php/' + 'getGroupsMarkMembers.php';
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;
    var groupid = localStorage.getItem("global_SelectedGroup");
    var obj;
    //var phpurl = global_php_url + 'getMemberList.php';
    var phpurl = global_php_url + 'getGroupsMarkMembers.php';

    var xhr;
    var fd;

    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", txtuserid);
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("groupid", groupid);

    if ( PDGM_debug == 1 ) console.log('**** 00 PopulateDivAdminGroupMembers / starting...');

    xhr.onreadystatechange = function () {
        if ( PDGM_debug == 1 ) console.log('00 PopulateDivAdminGroupMembers / ' + xhr.readyState + "/" + xhr.status);
        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            if ( PDGM_debug == 1 ) console.log('01 PopulateDivAdminGroupMembers');
            var i = 0;
            var str = "";

            //var results = $.parseJSON(xhr.responseText);
            var results = xhr.responseText;

            $("#divAdminGroupMembers").empty();
            var json_obj = null;
            try {
                json_obj = $.parseJSON(results);//parse JSON
                for ( var i in json_obj ) {
                    var MemberID = json_obj[i].FromEmail;
                    var ischecked = json_obj[i].b;

                    if (ischecked == '1') {
                        str = '    <label id="lbl' + MemberID + '"><input id ="mb' + MemberID + '" type="checkbox" name="option[]" checked="checked" value="' + MemberID + '" />' + MemberID + '</label>'
                    }
                    else {
                        str = '    <label id="lbl' + MemberID + '"><input id ="mb' + MemberID + '" type="checkbox" name="option[]" value="' + MemberID + '" />' + MemberID + '</label>'
                    }
                    $("#divAdminGroupMembers").append(str);
                }
            }
            catch ( e ) {
                console.log("0FA - WARNING xhr.receive : ", e); // pass exception object to error handler
                console.log('0FA - member results: ', results);
            }
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( PDGM_debug == 1 ) console.log('PopulateDivAdminGroupMembers - Listener Active: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0DC - Error xhr.send : ", e); // pass exception object to error handler
    }
}

//****************************************************************************************
function getMyMemberList () {
    var debug = 1;
    var url = 'php/' + 'getMyMemberList.php';
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var RoomName = $('#idRoomName').val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'getMyMemberList.php';

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", txtuserid);
    fd.append("pw", pw);
    fd.append("pwhash", CryptoJS.SHA1(pw));
    fd.append("sessionid", sid);

    xhr.onreadystatechange = function () {
        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var i = 0;
            var str = "";
            var results = xhr.responseText;

            $("#lbMyGroupMembers").empty();

            $.each(results, function (k, v) {
                str = '    <input type="checkbox" id="Member' + i + '" name="Member' + i + '"/>' + v + '</label>';
                $("#lbMyGroupMembers").append(str);
                i++;
            });
            $("#lbMyGroupMembers").selectmenu('refresh', true);
        }
    }

    xhr.addEventListener("load", function (evt) {
        console.log('getMyMemberList - xhr.addEventListener: ', xhr.readyState);
    }, false);
    xhr.open("POST", url, true);
    try {
        xhr.send(fd);
    }
    catch ( e ) {
        console.log("0C - Error xhr.send : ", e); // pass exception object to error handler
    }
}

//****************************************************************************************
function processMemberList (arr) {
    console.log('00 processMemberList arr: ', arr);
    //var out = "";

    for ( var key in arr ) {
        if ( arr.hasOwnProperty(key) ) {
            console.log(key + " -> " + arr[key]);
        }
    }

    console.log('01 processMemberList arr: ');

    var i = 0;
    for ( i = 0; i < arr.length; i++ ) {
        console.log('processMemberList arr(' + i + ') = ' + arr[i]);
    }
    //document.getElementById( "id01" ).innerHTML = out;
    //console.log(out);
}

//**********************************************************************************
function insertGroupMember (GroupID, MemberID) {
    event.preventDefault();

    var AM_debug = 0;
    var url = 'php/' + 'insertGroupMember.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'insertGroupMember.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    if ( AM_debug == 1 ) console.log("=======================================");
    if ( AM_debug == 1 ) console.log("insertGroupMember GroupID " + GroupID);
    if ( AM_debug == 1 ) console.log("insertGroupMember for MemberID " + MemberID);
    if ( AM_debug == 1 ) console.log("=======================================");

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("Members", MemberID);
    fd.append("GroupID", GroupID);

    if ( AM_debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("MemberID", MemberID);
        console.log("GroupID", GroupID);
    }

    xhr.onreadystatechange = function () {
        if ( AM_debug == 1 ) console.log('00 insertGroupMember: status' + xhr.readyState + " / " + xhr.status);

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( AM_debug == 1 ) {
                console.log('**************************************************************');
                console.log('insertGroupMember Results type: ', jQuery.type(results));
                console.log('insertGroupMember responseText type: ', jQuery.type(xhr.responseText));
                console.log('insertGroupMember results: ', results);
                console.log('**************************************************************');
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

                if ( AM_debug == 1 ) {
                    console.log("---------------------------------------------------------");
                    console.log("results:", results);
                    console.log("---------------------------------------------------------");
                    console.log("addMemberToGroup jstr: ");
                    console.log(jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( AM_debug == 1 ) print2dArray(A1);
                //*******************************************************************
                var fLen = A1.length;
                var n = 0;
                global_adminsid = "";
                bSuccess = 0;
                var arr = [];
                var msg = "";
                var tGroup = "";
                var tmember = "";
                var MemID = "";
                var bPrintmsg = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname.toUpperCase();
                    var val = A1[i].itemval;

                    //console.log( ">> FROM addMemberToGroup : ", ikey, val );

                    if ( ikey == "_GROUP" ) {
                        tGroup = " @Group:" + val;
                    }
                    if ( ikey == "_MEMBER" ) {
                        MemID = val;
                        tmember = " @Member:" + val;
                    }

                    if ( ikey == "SUCCESS" & val == "1" ) {
                        bPrintmsg = 1;
                        bSuccess = 1;
                        insertAdminUserToListbox(MemID);
                    }
                }
                if ( bPrintmsg == 1 ) {
                    console.log(">> ADDED: " + tmember + " to group " + tGroup + ".");
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( AM_debug == 1 ) console.log('getMyGroups - xhr.addEventListener: ', xhr.readyState);
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

function addMemberToGroup () {
    event.preventDefault();

    var AM_debug = 0;
    var url = 'php/' + 'insertMember.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'insertMember.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var MemberID = $('#txtUserName').val();

    var Groups = getSelectedMemberGroups();

    Groups = Groups.trim();

    if ( Groups.length == 0 ) {
        alert("Please, one or more groups must be selected.");
        return;
    }

    if ( AM_debug == 1 ) console.log("List of selected groups for user " + Groups);

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
    fd.append("Members", MemberID);
    fd.append("Groups", Groups);

    if ( AM_debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("MemberID", MemberID);
        console.log("Groups", Groups);
    }

    xhr.onreadystatechange = function () {
        if ( AM_debug == 1 ) console.log('00 addMemberToGroup');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( AM_debug == 1 ) {
                console.log('**************************************************************');
                console.log('addMemberToGroup Results type: ', jQuery.type(results));
                console.log('addMemberToGroup responseText type: ', jQuery.type(xhr.responseText));
                console.log('addMemberToGroup results: ', results);
                console.log('**************************************************************');
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

                if ( AM_debug == 1 ) {
                    console.log("---------------------------------------------------------");
                    console.log("results:", results);
                    console.log("---------------------------------------------------------");
                    console.log("addMemberToGroup jstr: ");
                    console.log(jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( AM_debug == 1 ) print2dArray(A1);
                print2dArray(A1);
                //*******************************************************************
                var fLen = A1.length;
                var n = 0;
                global_adminsid = "";
                bSuccess = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname.toUpperCase();
                    var val = A1[i].itemval;

                    try {
                        val = val.trim();
                    }
                    catch ( err ) {
                        val = "";
                        console.log("XX Val has zero length, skipping...");
                    }

                    if ( val.length > 0 ) {
                        if ( AM_debug == 1 ) {
                            console.log("---------------------------------------------------------");
                            console.log("ikey:" + ikey + " / val: " + val);
                        }

                        var ckText = "NEW RECORD CREATED SUCCESSFULLY";
                        if ( ikey.indexOf(ckText) >= 0 ) {
                            bSuccess = 1;
                            //insertAdminUserToListbox();
                        }
                        if ( val.indexOf("New record created successfully") >= 0 ) {
                            bSuccess = 1;
                            //insertAdminUserToListbox();
                        }
                        if ( val.indexOf(ckText) >= 0 ) {
                            bSuccess = 1;
                            //insertAdminUserToListbox();
                        }

                        if ( ikey == "SUCCESS" & val == "1" ) {
                            bSuccess = 1;
                            //insertAdminUserToListbox();
                        }
                    }
                    else {
                        if ( AM_debug == 1 ) console.log("XX2 Val has zero length, skipping...");
                    }
                }
                if ( bSuccess == 1 ) {
                    insertAdminUserToListbox(MemberID);
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( AM_debug == 1 ) console.log('getMyGroups - xhr.addEventListener: ', xhr.readyState);
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

function addMemberToGroupMGT () {
    event.preventDefault();

    var AMG_debug = 0;

    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var sid = global_sessionid;
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var GroupID = localStorage.getItem("global_SelectedGroup");

    if ( AMG_debug == 1 )
        console.log("GroupID: ", GroupID);

    var members = getMembersOfGroupMGT();
    if ( AMG_debug == 1 )
        console.log("=> members: ", members);

    //*****************************************************
    processGroupMembers(GroupID, members);
    //*****************************************************

    event.stopPropagation();
}

function addGroupMember (GroupID, MemberID) {
    event.preventDefault();

    var AM_debug = 1;
    var url = 'php/' + 'insertMember.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'insertMember.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    if ( AM_debug == 1 ) console.log("Adding user " + MemberID + " to Group " + GroupID);

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
    fd.append("Members", MemberID);
    fd.append("Groups", GroupID);

    if ( AM_debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("MemberID", MemberID);
        console.log("Groups", GroupID);
    }

    xhr.onreadystatechange = function () {
        if ( AM_debug == 1 ) console.log('00 addGroupMember');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( AM_debug == 1 ) {
                console.log('*******************************');
                console.log('addGroupMember Results type: ', jQuery.type(results));
                console.log('addGroupMember responseText type: ', jQuery.type(xhr.responseText));
                console.log('addGroupMember results: ', results);
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

                if ( AM_debug == 1 ) {
                    console.log("---------------------------------------------------------");
                    console.log("results:", results);
                    console.log("---------------------------------------------------------");
                    console.log("addGroupMember jstr: ");
                    console.log(jstr);
                    console.log("---------------------------------------------------------");
                }

                //*******************************************************************
                var A1 = new Array();
                A1 = build2dArray(jstr, 0);
                if ( AM_debug == 1 ) print2dArray(A1);
                //*******************************************************************
                var fLen = A1.length;
                var n = 0;
                global_adminsid = "";
                bSuccess = 0;
                for ( i = 0; i < fLen; i++ ) {
                    var ikey = A1[i].itemname.toUpperCase();
                    var val = A1[i].itemval;
                    if ( ikey == "SUCCESS" & val == "1" ) {
                        bSuccess = 1;
                    }
                }
            }
            event.stopPropagation();
            if ( bSuccess == 1 ) {
                alert("addGroupMember Added user " + MemberID + " to Group " + GroupID)
            }
            else
                alert("ERROR: addGroupMember Failed to add user " + MemberID + " to Group " + GroupID)
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( AM_debug == 1 ) console.log('addGroupMember - xhr.addEventListener: ', xhr.readyState);
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

//************************************************************************
function processGroupMembers (GroupID, members) {
    event.preventDefault();
    var PGM_debug = 0;
    var url = 'php/' + 'purgeMembersFromGroup.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'purgeMembersFromGroup.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    //var bDelete = ConfirmDelete();
    //if ( bDelete == false )
    //{
    //    alert( "Delete cancelled..." );
    //    return;
    //}

    var xhr;
    var fd;
    xhr = new XMLHttpRequest();
    fd = new FormData();

    fd.append("userid", userid);
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("GroupID", GroupID);

    if ( PGM_debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("GroupID", GroupID);
    }

    xhr.onreadystatechange = function () {
        if ( PGM_debug == 1 ) console.log('00 processGroupMembers');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( PGM_debug == 1 ) {
                console.log('*************** processGroupMembers ****************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('deleteMember 10', results);
                console.log('*******************************');
            }

            if ( !results ) {
                console.log('No processGroupMembers.');
                event.stopPropagation();
                return;
            }
            else {
                //var jsonresults = extractJson( results )
                var key1 = "";
                var val = "";
                var tgtGroup = '';
                var bSuccess = 0;
                var obj = JSON.parse(results);
                //*******************************************************************
                jQuery.each(obj, function (key1, val) {
                    if ( key1.toUpperCase() == '_GROUP' ) tgtGroup = val;
                    if ( key1.toUpperCase() == 'SUCCESS' ) bSuccess = 1;
                    console.log("processGroupMembers", key1, val);
                });
                //*******************************************************************

                if ( bSuccess == 1 && tgtGroup.length > 0 ) {
                    var strings = members.split('|');
                    var fLen = strings.length;
                    var n = 0;

                    for ( i = 0; i < fLen; i++ ) {
                        var MemberID = strings[i];
                        if ( MemberID.length > 0 ) {
                            console.log("Adding MEMBER: " + MemberID + " to : " + tgtGroup + ".");
                            insertGroupMember(tgtGroup, MemberID);
                        }
                    }

                    console.log("group: " + tgtGroup + ", successfully cleaned...");
                }
                else {
                    console.log("group: " + tgtGroup + ", FAILED to clean...");
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( PGM_debug == 1 ) console.log('deleteMember - xhr.addEventListener: ', xhr.readyState);
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
//************************************************************************
function deleteMemberFromGroup (GroupID, MemberID) {
    event.preventDefault();
    var debug = 0;
    var url = 'php/' + 'deleteMemberFromGroup.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'deleteMemberFromGroup.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

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
    fd.append("MemberID", MemberID);
    fd.append("GroupID", GroupID);

    if ( debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("MemberID", MemberID);
    }

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 deleteMember');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('deleteMember 10', results);
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

                //console.log( "jstr:", jstr );

                if ( jstr.indexOf('{"SUCCESS":1}') >= 0 ) {
                    alert("SUCCESSFUL DELETE");
                }
                else {
                    console.log("@@@@@ NO FIND, could not delete.");
                }
                var ii = 1
                if ( ii == 1 ) {
                    //*******************************************************************
                    var A1 = new Array();
                    A1 = build2dArray(jstr, 0);
                    if ( debug == 1 ) print2dArray(A1);
                    print2dArray(A1);
                    //*******************************************************************
                    var fLen = A1.length;
                    var n = 0;
                    global_adminsid = "";
                    bSuccess = 0;
                    for ( i = 0; i < fLen; i++ ) {
                        var ikey = A1[i].itemname.toUpperCase();
                        var val = A1[i].itemval;
                        if ( ikey == "SUCCESS" & val == "1" ) {
                            bSuccess = 1;
                            removeDeletedMemberFromList();
                        }
                    }
                    if ( bSuccess == 1 ) {
                        $('#lblStatus').text = '** SUCCESSFUL DELETE of ' + MemberID;
                    }
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('deleteMember - xhr.addEventListener: ', xhr.readyState);
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

function deleteMember () {
    event.preventDefault();
    var debug = 0;
    var url = 'php/' + 'deleteMember.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'deleteMember.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);

    var MemberID = getSelectedMember();

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
    fd.append("pw", pw);
    //fd.append( "pwhash", CryptoJS.SHA1( pw ) );
    //fd.append( "pwhash", CryptoJS.SHA1( pwhashed ) );
    fd.append("pwhash", pw);
    fd.append("sessionid", sid);
    fd.append("Members", MemberID);

    if ( debug == 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("MemberID", MemberID);
    }

    xhr.onreadystatechange = function () {
        if ( debug == 1 ) console.log('00 deleteMember');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug == 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('deleteMember 10', results);
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

                //console.log( "jstr:", jstr );

                if ( jstr.indexOf('{"SUCCESS":1}') >= 0 ) {
                    //removeDeletedMemberFromList();
                    alert("SUCCESSFUL DELETE");
                }
                else {
                    console.log("@@@@@ NO FIND, could not delete.");
                }
                var ii = 1
                if ( ii == 1 ) {
                    //*******************************************************************
                    var A1 = new Array();
                    A1 = build2dArray(jstr, 0);
                    if ( debug == 1 ) print2dArray(A1);
                    print2dArray(A1);
                    //*******************************************************************
                    var fLen = A1.length;
                    var n = 0;
                    global_adminsid = "";
                    bSuccess = 0;
                    for ( i = 0; i < fLen; i++ ) {
                        var ikey = A1[i].itemname.toUpperCase();
                        var val = A1[i].itemval;
                        if ( ikey == "SUCCESS" & val == "1" ) {
                            bSuccess = 1;
                            removeDeletedMemberFromList();
                        }
                    }
                    if ( bSuccess == 1 ) {
                        //removeDeletedMemberFromList( MemberID );
                        $('#lblStatus').text = '** SUCCESSFUL DELETE of ' + MemberID;
                    }
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('deleteMember - xhr.addEventListener: ', xhr.readyState);
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


function resetPw () {

    event.preventDefault();
    var debug = 1;
    var url = 'php/' + 'resetPw.php';
    var userid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    //var RoomName = $( '#idRoomName' ).val();
    var sid = global_sessionid;
    var obj;
    var phpurl = global_php_url + 'resetPw.php';
    var pwhashed = CryptoJS.SHA1(userid + pw);
    var MemberID = getSelectedMember();

    //$userid = $argv[1];
    //$pw = $argv[2];
    //$MemberID = $argv[3];
    //$sid = $argv[5];

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
    fd.append("MemberID", MemberID);

    if ( debug === 1 ) {
        console.log("userid", userid);
        console.log("pw", pw);
        console.log("pwhash", pw);
        console.log("sessionid", sid);
        console.log("MemberID", MemberID);
    }

    xhr.onreadystatechange = function () {
        if ( debug === 1 ) console.log('00 resetPw');

        if ( xhr.readyState == 4 && xhr.status == 200 ) {
            var results = xhr.responseText;

            if ( debug === 1 ) {
                console.log('*******************************');
                console.log('Results type: ', jQuery.type(results));
                console.log('responseText type: ', jQuery.type(xhr.responseText));
                console.log('results: ', results);
                console.log('resetPw 10', results);
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
                results = xhr.responseText;
                var jstr = JSON.stringify(xhr.responseText, null, 4); // (Optional) beautiful indented output.

                if ( debug === 1 ) {
                    console.log("*******************");
                    console.log("results:", results);
                    console.log("-------------------");
                    console.log("-------------------");
                    console.log("jstr:", jstr);
                    console.log("*******************");
                }

                var ii = 1
                if ( ii === 1 ) {
                    //*******************************************************************
                    var A1 = new Array();
                    A1 = build2dArray(jstr, 0);
                    if ( debug == 1 ) print2dArray(A1);
                    print2dArray(A1);
                    //*******************************************************************
                    var fLen = A1.length;
                    var n = 0;
                    global_adminsid = "";
                    bSuccess = 0;
                    for ( i = 0; i < fLen; i++ ) {
                        var ikey = A1[i].itemname.toUpperCase();
                        var val = A1[i].itemval;
                        if ( ikey == "SUCCESS" & val == "1" ) {
                            bSuccess = 1;
                            //removeDeletedMemberFromList();
                        }
                    }
                    if ( bSuccess == 1 ) {
                        $('#lblStatus').val = '** SUCCESSFUL PW RESET for ' + MemberID;
                        alert('** SUCCESSFUL PW RESET for ' + MemberID);
                    }
                    else {
                        $('#lblStatus').val = '** ERROR: Failed PW RESET for ' + MemberID;
                        alert('** ERROR: Failed PW RESET for ' + MemberID);
                    }
                }
            }
            event.stopPropagation();
        }
    }

    xhr.addEventListener("load", function (evt) {
        if ( debug == 1 ) console.log('resetPw - xhr.addEventListener: ', xhr.readyState);
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

function insertAdminUserToListbox (MemberID) {
    IA_debug = 0;
    if ( IA_debug == 1 ) console.log("$$$ Inside : insertAdminUserToListbox");

    $('#lbAdminUser').append($("<option/>", {
        value: MemberID,
        text: MemberID
    }));

    if ( IA_debug == 1 ) console.log("New Member Added: " + MemberID);

    $("#lbAdminUser").selectmenu("refresh");
}

function removeDeletedMemberFromList () {
    var rmv_debug = 0;
    var MemberID = getSelectedMember();

    if ( rmv_debug == 1 ) {
        console.log("removeDeletedMemberFromList selcted Member: " + MemberID);
    }

    if ( MemberID != null ) {
        $("#lbAdminUser option[value='" + MemberID + "']").remove();
        $("#lbAdminUser").selectmenu("refresh");
    }
}
function getSelectedMember () {
    return global_SelectedMemberID;
}

function getSelectedMemberGroups () {
    event.preventDefault();

    var SMG_debug = 0;
    var selected = [];
    var i = 0;

    if ( SMG_debug == 1 ) console.log('getSelectedMemberGroups STARTING');

    $('#divGroupMembers input:checked').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        item = item.substring(2);
        if ( SMG_debug == 1 ) console.log('getSelectedMemberGroups Checked : ', item, i);
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

function getMembersOfGroupMGT () {
    event.preventDefault();

    var MOG_debug = 0;
    var selected = [];
    var i = 0;

    if ( MOG_debug == 1 ) console.log('getSelectedMemberGroups STARTING');

    $('#divAdminGroupMembers input').each(function () {
        i = i + 1;
        var item = $(this).attr('id');
        item = item.substring(2);
        if ( MOG_debug == 1 ) console.log('getMembersOfGroupMGT ITEMS : ', item, i);
    });

    i = 0;
    $('#divAdminGroupMembers input:checked').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        item = item.substring(2);
        if ( MOG_debug == 1 ) console.log('getMembersOfGroupMGT Checked : ', item, i);
    });

    var l = selected.length;
    var items = "";
    for ( i = 0; i < l; i++ ) {
        str1 = selected[i];
        items += str1.substring(2) + "|";
        if ( MOG_debug == 1 ) console.log('Build Items: ', items);
    }

    if ( MOG_debug == 1 ) console.log('getMembersOfGroupMGT CALL SELECT PROCESS ON : ', items);
    if ( MOG_debug == 1 ) console.log('getMembersOfGroupMGT processGroups: COMPLETE', i);

    event.stopPropagation();

    return items;
}