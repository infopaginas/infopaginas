$( document ).ready(function() {
    $( document ).on( 'click', function( event ) {
        if ( !$( '#rejectReasonModal' ).is(':visible') && $( '#rejectReasonModal textarea').prop('required') ) {
            $( '#rejectReasonModal textarea' ).removeAttr( 'required' );
        }

        if ( event.target.id == 'rejectTaskButton' ) {
            $( '#rejectReasonModal textarea' ).attr( 'required', 'required' );
        }
    } );

    $( '#task-form' ).on( 'submit', function( event ) {
        if ( $( '#rejectReasonModal textarea').prop('required') && $( '#rejectReasonModal textarea' ).val().trim() == '' ) {
            $( '#rejectReasonModal textarea').closest( '.form-group' ).addClass( 'has-error' );
            $( '#rejectReasonModal textarea').closest( '.form-group').append( '<div class="help-block">Field shoud not be empty.</div>' );

            return false;
        }
    });
});
