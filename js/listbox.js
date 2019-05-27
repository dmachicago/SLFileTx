/*
 * Copyright (@) 2017. D. Miller & Associates, Limited, Illinois, USA.
 * ALL rights reserved.
 */

var expanded = false;
var expandedToBox = false;

function selectFirstItem (SelectListId) {
    debug = 0;
    if ( debug == 1 ) console.log('selectFirstItem: ', SelectListId);

    // remove "selected" from any options that might already be selected
    var tgt = '#' + SelectListId;

    if ( debug == 1 ) console.log('tgt: ', tgt);
    if ( debug == 1 ) console.log('selectFirstItem 00: ');

    $(tgt + ' option[selected="selected"]').each(
        function () {
            $(this).removeAttr('selected');
        }
    );
    if ( debug == 1 ) console.log('selectFirstItem 01: ');

    // mark the first option as selected
    $(tgt + " option:first").attr('selected', 'selected');
    if ( debug == 1 ) console.log('selectFirstItem 02: ');
    /*var selectTags = document.getElementById(SelectListId);
     for ( var i = 0; i < selectTags.length; i++ ) {
     console.log('selectFirstItem', i);
     selectTags[i].selectedIndex = 0;
     }*/
    if ( debug == 1 ) console.log('selectFirstItem DONE: ');
}

function showCheckboxes () {
    var checkboxes = document.getElementById("checkboxes");
    if ( !expanded ) {
        checkboxes.style.display = "block";
        expanded = true;
    }
    else {
        checkboxes.style.display = "none";
        expanded = false;
    }
}

function showToBoxes () {
    var checkboxes = document.getElementById("lbMyGroupMembers");
    if ( !expandedToBox ) {
        checkboxes.style.display = "block";
        expandedToBox = true;
    }
    else {
        checkboxes.style.display = "none";
        expandedToBox = false;
    }
}

function getSelectedUsers () {
    var UserArray = new Array();
    var myArr = $('#lbMyGroupMembers').find('li')

    $("#lbMyGroupMembers > option").each(function () {
        //var selected = $(this).find(":selected").text();
        if ( this.selected == true ) {
            myArr.push(this.text);
            UserArray.push(this.text);
        }
    });
    //console.log('getSelectedUsers UserArray', UserArray.toString());
    return UserArray;
}

function getSelectedGroupV2 (TgtID) {
    var getSelectedGroupDebug = 1;
    var selectedgroup = '';
    var SelID = "#" + TgtID + " > option";
    //"#lbMyGroups > option"
    $(SelID).each(function () {
        if ( getSelectedGroupDebug == 1 )
            console.log(' getSelectedGroup list item : <' + this.innerText + '>');

        if ( this.selected == true ) {
            selectedgroup = this.innerText;
            if ( getSelectedGroupDebug == 1 )
                console.log(' getSelectedGroup Selected: <' + selectedgroup + '>');
        }
    });
    return selectedgroup;
}