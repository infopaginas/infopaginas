$( document ).ready(function() {
    var hasSamePhones = false;
    var phonesCheckComplete = false;
    var phonesRow = $( '#phones' );
    var phones = phonesRow.data( 'phones' );
    var businessProfileId = phonesRow.data( 'bp-id' );

    $( document ).on( 'click', function( event ) {
        if ( !$( '#rejectReasonModal' ).is(':visible') && $( '#rejectReasonModal textarea').prop('required') ) {
            $( '#rejectReasonModal textarea' ).removeAttr( 'required' );
        }

        if ( event.target.id == 'rejectTaskButton' ) {
            $( '#rejectReasonModal textarea' ).attr( 'required', 'required' );
        }
    } );

    $( '#task-form' ).on( 'submit', function( event ) {
        if ( !phonesCheckComplete ) {
            if ( !confirm( messages.phonesCheckNotComplete ) ) {
                return false;
            }
        }
        if ( hasSamePhones ) {
            if ( !confirm( messages.phoneNotUnique ) ) {
                return false;
            }
        }
        if ( $( '#rejectReasonModal textarea').prop('required') && $( '#rejectReasonModal textarea' ).val().trim() == '' ) {
            $( '#rejectReasonModal textarea').closest( '.form-group' ).addClass( 'has-error' );
            $( '#rejectReasonModal textarea').closest( '.form-group' ).find( '.help-block' ).remove();
            $( '#rejectReasonModal textarea').closest( '.form-group').append( '<div class="help-block">Field should not be empty.</div>' );

            return false;
        }
    });

    if ( phones && phones.length > 0 && businessProfileId ) {
        var data = {
            phones: phones,
        };

        $.ajax( {
            url: Routing.generate( 'domain_business_admin_validation_business_phone', { id: businessProfileId } ),
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success: handleBusinessPhonesValidation
        } );
    } else {
        phonesCheckComplete = true;
    }

    function handleBusinessPhonesValidation( response ) {
        var matches = response.matches;
        var message = response.message;
        phonesCheckComplete = true;

        if ( matches.length ) {
            hasSamePhones = true;

            var notificationHtml = '<div class="help-inline sonata-ba-field-error-messages"><ul class="list-unstyled">';
            notificationHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + message + '</li>';

            $.each( matches, function ( index, value ) {
                var link = '<a href="' + value.url + '" target="_blank">' + value.name + '</a>';
                notificationHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + value.id + ': ' + link + '</li>';
            } );

            notificationHtml += '</ul></div>';

            phonesRow.find( 'td' ).first().append( notificationHtml );
        }
    }
});
