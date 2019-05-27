class CheckBox {
    ListDivCheckboxes (DivID) {
        var tgtDiv = "#" + DivID;
        $('input', $(tgtDiv)).each(function () {
            var xtype = $(this).attr('type');
            console.log("attr Type:", xtype);

            var id = $(this).attr('id');
            console.log("attr id:", id);

            var itemid = $(this).attr('itemid');
            console.log("attr itemid:", itemid);

            if ( document.getElementById(id).checked ) {
                console.log("checked");
            }
            else {
                console.log("checked");
            }

            var tgtid = "#" + id;
            var ck = $(tgtid).is(':checked');
            console.log("attr ck isChecked:", ck);

            //var x = document.getElementById( id ).value;
            //console.log( "X Value:", val );

            var isChecked = $(this).attr('checked');
            console.log("attr isChecked:", isChecked);

            var val = $(this).attr('value');
            console.log("attr value:", val);

            //var defaultValue = $( this ).attr( 'defaultValue' );
            //console.log( "attr defaultValue:", defaultValue );

            var name = $(this).attr('name');
            console.log("attr name:", name);

            var disabled = $(this).attr('disabled');
            console.log("attr disabled:", disabled);
        });
    }

    CheckedON (CkBoxID) {
        $('#' + CkBoxID).prop('checked', true);
    }

    CheckedOFF (CkBoxID) {
        $('#' + CkBoxID).prop('checked', false);
    }

    SelectLbItemON (itemid) {
        $('#' + itemid).prop('selected', true);
    }

    SelectLbItemOFF (itemid) {
        $('#' + itemid).prop('selected', true);
    }

    getListBoxItems (lbId) {
        var opts = $('#' + lbId)[0].options;

        var array = $.map(opts, function (elem) {
            return ( elem.value || elem.text );
        });

        alert(array);
    }

    sortGiveNamesFilter () {
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
        }).appendTo('#subfilterNamesContainer');

        $('#subfilterNamesContainer .default:last, #subfilterNamesContainer :checked:last').closest('label').after('<hr />');
    }
}