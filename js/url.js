//********************************************************************
//USAGE:
//For example, if you have the URL:
//http://www.example.com/?me=Dale&name2=W Dale Miller
//    This code will return:
//{
//    "me"    : "Dale",
//    "name2" : "W Dale Miller"
//}
//and you can do:
//var me = getUrlVars()["me"];
//var name2 = getUrlVars()["name2"];
//************* ALSO creates JSON as follows: ************************
// ** and stores it in a session variable "urlJson".
//{"userid":"wmiller","pwh":"2ea241cf5871ef6693b247fac1152c4499ca3a3b","sid":"gtad9oru32ik8l35thliqtv010","asid":"343117e01366a9571c9d06796394697c","seccode":"Qay@LFV9iSZVbPvRr1WppQ94pCsnH9iIMfaC","hashcode":"937049bd7f980226d48adc6a9b57754e4443ea16","cod":"3d469216-4a87-4fb6-8fc3-6e80be3dd7f5","machid":"0Za2!!J6iIM0fP9@IU$y-mDt$g*JRMEpVvFE","roomname":"RM2017","isadmin":"1"}
//********************************************************************

//** SET GLOBALS
var parmJsonObj = {};
var ProcessParms = 0;

function getUrlVars () {
    if ( ProcessParms == 0 ) {
        console.log("*** >>> Processing Parms OFF");
        return;
    }
    else
        console.log("*** >>> Processing Parms ON");

    var UV_debug = 0;
    var key = "";
    var val = "";
    var hash = [];
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    console.log("Number of url parms: ", hashes.length);

    if ( hashes.length == 1 ) {
        console.log("No URL parms, exiting...");
        return;
    }

    for ( var i = 0; i < hashes.length; i++ ) {
        hash = hashes[i].split('=');
        key = hash[0];
        val = hash[1];
        if ( val != undefined ) {
            key = decodeURI(key);
            val = decodeURI(val);
            val = stripQuotes(val);
            if ( UV_debug == 1 ) {
                console.log("if (key == '" + key + "') { return sessionStorage.getItem( '" + key + "' );}");
            }
            sessionStorage.setItem(key, val);
            parmJsonObj[key] = val;
        }
    }

    if ( UV_debug == 1 )
        console.log("appid:", sessionStorage.getItem("appid"));

    s = sessionStorage.getItem('JsonDATA');
    s = s.replace(/`/g, '"');
    sessionStorage.setItem('JsonDATA', s);
    s = sessionStorage.getItem('JsonDATA');
    parmJsonObj['JsonDATA'] = '';

    var jresult = $.parseJSON(s);
    $.each(jresult, function (k, v) {
        var pkey = jresult[k].key;
        var xval = jresult[k].val;

        parmJsonObj[pkey] = xval;
        sessionStorage.setItem(pkey, xval);

        if ( UV_debug == 1 ) {
            console.log("if (key == '" + pkey + "') { return sessionStorage.getItem( '" + pkey + "' );}");
        }
    });

    if ( UV_debug == 1 ) {
        console.log("json: ", sessionStorage.getItem('JsonDATA'));
        var tempvar = sessionStorage.getItem('ISADMIN');
        console.log("jsongetJsonValue ISADMIN: ", tempvar);
        tempvar = sessionStorage.getItem('seccode');
        console.log("jsongetJsonValue seccode: ", tempvar);
        tempvar = sessionStorage.getItem('seccode');
        console.log("getSessionParm seccode: ", tempvar);
    }

    json = JSON.stringify(parmJsonObj);
    console.log("JSON@2.stringify:", json);

    return parmJsonObj;
}

function getUrlJson (key) {
    return parmJsonObj[key];
}

function stripQuotes (val) {
    var tempval = val.replace(/"/g, '');
    tempval = tempval.trim();
    return tempval;
}

function getSessionJson () {
    return sessionStorage.getItem('JsonDATA');
}
