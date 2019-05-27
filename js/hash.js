/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

function sha1hash (str) {
    return CryptoJS.SHA1(str);
}
function sha256hash (str) {
    return CryptoJS.sha256(str);
}