/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

var global_ipaddr = '45.32.129.86';
var global_port = '8889';
var global_dbname = 'k3';
var global_ipaddr_port = global_ipaddr + ':' + global_port;
var global_app_dir = '/var/www/html/SLupload';
var global_app_url = 'HTTPS://' + global_ipaddr + global_app_dir;
//var global_php_url = global_app_url + '/php/';
var global_php_url = 'php/';
var global_exec_url = 'HTTPS://' + global_ipaddr + '/' + global_app_dir + '/';
var global_static_url = 'HTTPS://' + global_ipaddr + '/SLupload';
var global_login_attempts = 0;
var global_userhash = '';
var global_pwhash = '';
var global_sessionid = '';
var global_adminsid = '';
var global_isadmin = 0;
var global_SelectedGroup = '';
var global_SelectedMemberID = "";
var global_UploadDir = '/var/www/html/SLupload/uploads/';
var gFiLeArray = new Array();
var gMemberGroups = new Array();
var gRadioButton = "ShowMembers";

var gUserID = '';
var gPwID = '';
var gSessionID = '';
var arrVar = [];
var arrVal = [];

function systemRestart () {
    var myWindow = window.open(window.open('https://45.32.129.86/SLupload/index.html', '_self'));
}

function resetUserGroups () {
    $("#divGroupMembers").empty();
    getMemberGroups();
}

function cleanTxtStr (str, debug) {
    if ( str == undefined ) {
        console.log("NOTICE: cleanTxtStr str undefined, returning...");
        return;
    }
    if ( debug == 1 ) console.log("cleanTxtStr 01: " + str);
    try {
        while ( str.indexOf("\\") >= 0 ) {
            str = str.toString().replace("\\", "");
        }
        while ( str.indexOf("[") >= 0 ) {
            str = str.toString().replace("[", "");
        }
        while ( str.indexOf("]") >= 0 ) {
            str = str.toString().replace("]", "");
        }
        while ( str.indexOf("{") >= 0 ) {
            str = str.toString().replace("{", "");
        }
        while ( str.indexOf("}") >= 0 ) {
            str = str.toString().replace("}", "");
        }
        while ( str.indexOf("\"") >= 0 ) {
            str = str.toString().replace("\"", "");
        }
    }
    catch ( e ) {
        console.log("Error cleanTxtStr: ", e.toString(), str);
    }
    return str;
}

function buildArray (str, debug) {
    if ( debug == 1 ) str = "{\"SessionID\":\"s25h4qt868s5uiname5ov7tu50\",\"IV\":\"49c5703-02d2-425\",\"SK\":\"f9d2008-0e6e-412\",\"global_sessionID\":\"s25h4qt868s5uiname5ov7tu50\",\"generated_sessionID\":\"s25h4qt868s5uiname5ov7tu50\",\"$GuidID\":\"0c2cfc45-d4b1-4090-b5ad-2893677c76d0\",\"SUCCESS\":1,\"sessionid\":\"s25h4qt868s5uiname5ov7tu50\",\"time\":1510595574,\"memberid\":\"wmiller\",\"pwhash\":\"2ea241cf5871ef6693b247fac1152c4499ca3a3b\",\"memberhash\":\"c7ebb5e5a59124ba373f65da46526fb39a430ca5\"}"

    arrVar = [];
    arrVal = [];

    var strings = str.split(',');
    var fLen = strings.length;
    var n = 0;

    for ( i = 0; i < fLen; i++ ) {
        var items = strings[i].split(':');
        var varname = items[0];
        while ( varname.indexOf("{") >= 0 ) {
            varname = varname.toString().replace("{", "");
        }
        while ( varname.indexOf("}") >= 0 ) {
            varname = varname.toString().replace("}", "");
        }
        while ( varname.indexOf("\"") >= 0 ) {
            varname = varname.toString().replace("\"", "");
        }
        var varval = items[1];
        while ( varval.indexOf("{") >= 0 ) {
            varval = varval.toString().replace("{", "");
        }
        while ( varval.indexOf("}") >= 0 ) {
            varval = varval.toString().replace("}", "");
        }
        while ( varval.indexOf("\"") >= 0 ) {
            varval = varval.toString().replace("\"", "");
        }
        arrVar.push(varname);
        arrVal.push(varval);
        if ( debug == 1 ) console.log("i=", i, varname, varval);
    }
}

function print2dArray (A1) {
    var fLen = A1.length;
    var n = 0;
    for ( i = 0; i < fLen; i++ ) {
        var ikey = A1[i].itemname;
        var val = A1[i].itemval;
        console.log('PRINT2D PARMS: ' + ikey + " / " + val);
    }
    console.log('*************');
}

function get2dItemValue (A1, ItemName) {
    ItemName = ItemName.toUpperCase();
    var vName = "";
    var vVal = "";
    var retval = "";
    // loop the outer array
    for ( var i = 0; i < A1.length; i++ ) {
        // get the size of the inner array
        var innerArray = A1[i];
        vName = innerArray.itemname;
        vVal = innerArray.itemval;

        if ( ItemName == vName.toUpperCase() )
            return vVal;
    }
    return retval;
}

function build2dArray (str, debug) {
    /// <summary>
    /// Builds a 2D array from a json stringified object.
    /// </summary>
    /// <param name="str">The json stringifiedstring.</param>
    /// <param name="debug">debug = set to 1 to see output in console.</param>
    /// <returns>a 2D array containing itemname and itemval in each row.</returns>
    var B2_debug = 0;
    var result = [{itemname: "", itemval: ""}];

    if ( debug == 1 ) str = "{\"SessionID\":\"s25h4qt868s5uiname5ov7tu50\",\"IV\":\"49c5703-02d2-425\",\"SK\":\"f9d2008-0e6e-412\",\"global_sessionID\":\"s25h4qt868s5uiname5ov7tu50\",\"generated_sessionID\":\"s25h4qt868s5uiname5ov7tu50\",\"$GuidID\":\"0c2cfc45-d4b1-4090-b5ad-2893677c76d0\",\"SUCCESS\":1,\"sessionid\":\"s25h4qt868s5uiname5ov7tu50\",\"time\":1510595574,\"memberid\":\"wmiller\",\"pwhash\":\"2ea241cf5871ef6693b247fac1152c4499ca3a3b\",\"memberhash\":\"c7ebb5e5a59124ba373f65da46526fb39a430ca5\"}"

    var varname = "";
    var varval = "";

    var strings = str.split(',');
    var fLen = strings.length;
    var n = 0;

    if ( B2_debug == 1 ) console.log('00 build2dArray: ', str);

    for ( i = 0; i < fLen; i++ ) {
        if ( strings[i].indexOf(":") ) {
            var items = strings[i].split(':');
            if ( items.length > 0 ) {
                var varname = items[0];
                if ( varname.indexOf("{") >= 0 )
                    while ( varname.indexOf("{") >= 0 ) {
                        varname = varname.toString().replace("{", "");
                    }
                if ( varname.indexOf("}") >= 0 )
                    while ( varname.indexOf("}") >= 0 ) {
                        varname = varname.toString().replace("}", "");
                    }
                if ( varname.indexOf("\"") >= 0 )
                    while ( varname.indexOf("\"") >= 0 ) {
                        varname = varname.toString().replace("\"", "");
                    }

                varval = items[1];
                if ( varname.indexOf("{") >= 0 )
                    while ( varname.indexOf("{") >= 0 ) {
                        varname = varname.toString().replace("{", "");
                    }
                if ( varname.indexOf("}") >= 0 )
                    while ( varname.indexOf("}") >= 0 ) {
                        varname = varname.toString().replace("}", "");
                    }
                if ( varname.indexOf("\"") >= 0 )
                    while ( varname.indexOf("\"") >= 0 ) {
                        varname = varname.toString().replace("\"", "");
                    }

                varname = cleanTxtStr(varname, 0);
                varval = cleanTxtStr(varval, 0);

                if ( B2_debug == 1 ) console.log('00 build2dArray result.push: ', varname, varval);

                result.push({itemname: varname, itemval: varval});
                if ( debug == 1 ) console.log("i=", i, varname, varval);
            }
        }
    }
    return result;
}

function extractJson (str) {
    var extractJson = 0;
    var results = str;
    if ( extractJson == 1 ) console.log("** extractJson ON");
    if ( results.indexOf('{') > -1 ) {
        if ( results.indexOf('}') > -1 ) {
            if ( extractJson == 1 ) console.log("extractJson", results);
            var x = results.indexOf('{');
            var y = results.lastIndexOf('}');
            results = results.substring(x, y + 1);
            if ( extractJson == 1 ) console.log("extractJson:");
            if ( extractJson == 1 ) console.log(results);
        }
    }
    return '[' + results + ']';
}
function extractEmbeddedJson (str) {
    var results = str;
    if ( results.indexOf('[{') > -1 ) {
        if ( results.indexOf('}]') > -1 ) {
            var x = results.indexOf('[');
            var y = results.lastIndexOf(']');
            results = results.substring(x, y + 1);
        }
    }
    return results;
}

function getLoc () {
    var debug = 0;
    $.getJSON('//freegeoip.net/json/?callback=?', function (data) {
        if ( debug == 1 )
            console.log(JSON.stringify(data, null, 2));
    });
}

function setstatus (stext) {
    var lblstatus = document.getElementById("percent");
    lblstatus.innerHTML = stext;
}
function setHashes () {
    var txtuserid = $('#idUserid').val();
    var pw = $('#idPassword').val();
    var global_userhash = CryptoJS.SHA1(txtuserid);
    var global_pwhash = CryptoJS.SHA1(pw);
    var global_loginhash = CryptoJS.SHA1(txtuserid + pw);
    sessionStorage.setItem('curruserhash', global_userhash);
    sessionStorage.setItem('currpwhash', global_pwhash);
    sessionStorage.setItem('global_loginhash', global_loginhash);
}

function getObjvalue (obj, ObjKey) {
    var debug = 0;
    var val = null;
    var xval = null;
    for ( var key in obj ) {
        key += '';
        if ( obj.hasOwnProperty(key) ) {
            val = obj[key];
            if ( key == ObjKey ) {
                xval = val;
                break;
            }
        }
    }
    return xval;
}

function displayObj (obj) {
    console.log('displayObj Starting: ');
    var val = null;
    for ( var key in obj ) {
        if ( obj.hasOwnProperty(key) ) {
            val = obj[key];
            console.log('displayObj: ' + key + ' -> ' + val);
        }
    }
    console.log('displayObj Ending: ');
}

function popTestLogin () {
    $('#idUserid').val('dale');
    $('#idPassword').val('Junebug@01');
    //$( '#idRoomName' ).val( 'WDM' );
}

function ckRb (rbId) {
    $("input[type='radio']:first").attr("checked", "checked");
    $("input[type='radio']").checkboxradio("refresh");
}

function getSelGroupVal (sel) {
    var tval = $('#lbAdminGroup').val();
    global_SelectedGroup = sel.value;
    console.log("getSelGroupVal: " + global_SelectedGroup);
    console.log("00 SELECTED Group: " + sel.value);
}

function getSelMemVal (sel) {
    global_SelectedMemberID = sel.value;
    $('#txtUserName').val(global_SelectedMemberID);
    uncheckAll('divGroupMembers');
    getMemberGroups();
}

function setSelGroupVal (sel) {
    var SSS_debug = 1;
    localStorage.setItem("global_SelectedGroup", sel.value);
    global_SelectedGroup = sel.value;
    if ( SSS_debug == 1 ) console.log("--- setSelGroupVal set to: ", global_SelectedGroup);
}

function uncheckMemberGroups () {
    getMemberGroups();
    $('#lbAdminUser').find('input[type=checkbox]:checked').removeAttr('checked');
}

function uncheckAll (divid) {
    $('#' + divid + ' :checkbox:enabled').prop('checked', false);
}

function ConfirmDelete () {
    var x;
    if ( confirm("You will delete this item and all dependent items, ARE YOU SURE?") == true ) {
        x = true;
    }
    else {
        x = false;
    }
    return x;
}

function ConfirmGroupMemberDelete () {
    var x;
    if ( confirm("You will delete this member from the selected group, ARE YOU SURE?") == true ) {
        x = true;
    }
    else {
        x = false;
    }
    return x;
}

function showDivCheckBoxes (DivID) {
    event.preventDefault();

    var SMG_debug = 1;
    var selected = [];
    var i = 0;

    if ( SMG_debug == 1 ) console.log('showDivCheckBoxes STARTING');

    $('#' + DivID + ' input:checked').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        //item = item.substring( 2 );
        if ( SMG_debug == 1 ) console.log('Checked ID: ', item, i);
    });
    //$("input:checkbox:not(:checked)")
    $('#' + DivID + ' input:not(:checked)').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        //item = item.substring( 2 );
        if ( SMG_debug == 1 ) console.log('NOT Checked ID: ', item, i);
    });

    event.stopPropagation();

    //return items;
}

function getAllDivCheckBoxes (DivID) {
    event.preventDefault();

    var SMG_debug = 1;
    var selected = [];
    var i = 0;

    if ( SMG_debug == 1 ) console.log('showDivCheckBoxes STARTING');

    $('#' + DivID + ' input:checked').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        //item = item.substring( 2 );
        if ( SMG_debug == 1 ) console.log('Checked ID: ', item, i);
    });
    //$("input:checkbox:not(:checked)")
    $('#' + DivID + ' input:not(:checked)').each(function () {
        i = i + 1;
        selected.push($(this).attr('id'));
        var item = $(this).attr('id');
        //item = item.substring( 2 );
        if ( SMG_debug == 1 ) console.log('NOT Checked ID: ', item, i);
    });

    selected.sort();

    event.stopPropagation();

    return selected;
}

function SortListBoxAscending (LB_ID) {
    //Sort the listbox
    var lbid = "#" + LB_ID;
    $(lbid).html($(lbid + ' option').sort(function (x, y) {
        return $(x).text() < $(y).text() ? -1 : 1;
    }))
    $(lbid).get(0).selectedIndex = 0;
}

function testCode () {
    const cb = new CheckBox();
    cb.ListDivCheckboxes('divAdminGroupMembers');
}

function showListBoxItems (lbId) {
    const cb = new CheckBox();
    cb.getListBoxItems(lbId);
}

function ckElementByID (ID) {
    var elementExists = document.getElementById(ID);
    return elementExists;
}