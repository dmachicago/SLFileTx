function getScreenWidth () {
    var TotalWidth = screen.width;
    return TotalWidth;
}
function getScreenHeight () {
    var TotalHeight = screen.height;
    return TotalHeight;
}
function detectOs (action) {

    return;

    var Browser = window.ui.browser;
    var version = window.ui.version;
    var OperatingSystem = window.ui.os;
    var OperatingSystemVersion = window.ui.osversion;
    if ( action == 'OSV' ) {
        return OperatingSystemVersion;
    }
    else if ( action == 'OS' ) {
        return OperatingSystem;
    }
    else if ( action == 'V' ) {
        return version;
    }
    else if ( action == 'B' ) {
        return Browser;
    }
    else if ( action == 'L' ) {
        var msg = "Browser" + Browser + "\n";
        msg += "version" + version + "\n";
        msg += "OperatingSystem" + OperatingSystem + "\n";
        msg += "OperatingSystemVersion" + OperatingSystemVersion + "\n";
        console.log("Op Sys: ", msg);
    }
    else {
        var msg = "Browser" + Browser + "\n";
        msg += "version" + version + "\n";
        msg += "OperatingSystem" + OperatingSystem + "\n";
        msg += "OperatingSystemVersion" + OperatingSystemVersion + "\n";
        alert(msg);
    }
}