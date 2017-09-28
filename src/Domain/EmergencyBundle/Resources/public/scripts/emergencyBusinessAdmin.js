$( document ).ready( function() {
    var openAllTimeCheckboxes = $( '[ id *= "_openAllTime" ]' );

    applyPhoneMask();

    function applyPhoneMask() {
        var phones = $( 'input[ id $= "_phone" ]' );

        phones.mask( '999-999-9999' );
        phones.bind( 'paste', function () {
            $( this ).val( '' );
        });
    }

    $( 'body' ).on( 'ifChecked ifUnchecked', '[ id *= "_openAllTime" ]', function( index, openAllTimeCheckbox ) {
        checkCollectionWorkingHours( this );
    });

    checkAllCollectionWorkingHours( openAllTimeCheckboxes );

    function checkAllCollectionWorkingHours( openAllTimeCheckboxes ) {
        $.each( openAllTimeCheckboxes, function( index, openAllTimeCheckbox ) {
            checkCollectionWorkingHours( openAllTimeCheckbox );
        });
    }

    function checkCollectionWorkingHours( openAllTimeCheckbox ) {
        var workingHourBlock = $( openAllTimeCheckbox ).parents( 'tr' ).first();
        var timeStart = workingHourBlock.find( '[ class *= "_collectionWorkingHours-timeStart" ]').find( 'input' );
        var timeEnd = workingHourBlock.find( '[ class *= "_collectionWorkingHours-timeEnd" ]' ).find( 'input' );

        if ( $( openAllTimeCheckbox ).prop( 'checked' ) ) {
            timeStart.prop( 'readonly', true );
            timeEnd.prop( 'readonly', true );
        } else {
            timeStart.prop( 'readonly', false );
            timeEnd.prop( 'readonly', false );
        }
    }

    $( 'body' ).on( 'focus', '.working-hours-time-start', function() {
        $( this ).datetimepicker({
            format: 'hh:mm a',
            pickDate: false,
            pickSeconds: false,
            pick12HourFormat: false
        });
    });

    addCheckAllButton();

    $( document ).on( 'click', 'button.checkAll', function( e ) {
        e.preventDefault();

        $( this ).parent().find( 'input[ type = "checkbox" ]').each(function() {
            if ( !$( this ).prop( 'checked' ) ) {
                $( this ).iCheck( 'toggle' );
            }
        });
    });

    function addCheckAllButton() {
        $( '#sonata-ba-field-container-' + formId + '_paymentMethods' ).append( '<button class="btn btn-primary checkAll">Check All</button>' );
    }
});
