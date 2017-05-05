$( document ).ready( function() {
    var localityField  = $( '#' + formId + '_locality' );
    var useAllLocation = $( '#' + formId + '_useAllLocation' );

    checkUseAllLocationCheckBox( useAllLocation );

    $( 'body' ).on( 'ifChecked ifUnchecked', '#' + formId + '_useAllLocation', function( e, aux ) {
        checkUseAllLocationCheckBox( this );
    });

    function checkUseAllLocationCheckBox( elem ) {
        if ( $( elem ).prop( 'checked' ) ) {
            disableLocationSelect();
        } else {
            enableLocationSelect();
        }
    }

    function enableLocationSelect() {
        localityField.removeAttr( 'disabled' );
        localityField.attr( 'required', 'required' );
    }

    function disableLocationSelect() {
        localityField.attr( 'disabled', 'disabled' );
        localityField.removeAttr( 'required' );
    }
} );
