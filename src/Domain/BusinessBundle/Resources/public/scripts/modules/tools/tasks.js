$( document ).ready(function() {
    $( document ).on( 'click', function( event ) {
        if ( !$( '#rejectReasonModal' ).is(':visible') && $( '#rejectReasonModal textarea').prop('required') ) {
            $( '#rejectReasonModal textarea' ).removeAttr( 'required' );
        }

        if ( event.target.id == 'rejectTaskButton' ) {
            $( '#rejectReasonModal textarea' ).attr( 'required', 'required' );
        }
    } );
});
