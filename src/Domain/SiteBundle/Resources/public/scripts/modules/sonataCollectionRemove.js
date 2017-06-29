$( document ).ready( function() {

    if ( formId ) {
        var collections = [
            'keywords',
            'extraSearches',
            'subscriptions',
            'images',
            'phones'
        ];

        var inputsSelector = 'input[required = "required"], input[data-required = "required"], textarea[required = "required"], textarea[data-required = "required"]';

        $.each(collections, function( index, value ) {
            var selector = getRemoveCheckBoxSelector( formId, value );

            $( selector ).each(function() {
                if ( $( this ).prop( 'checked' ) ) {
                    var item   = $( this );
                    var parent = item.closest( 'tr' );
                    var inputs = parent.find( inputsSelector );

                    inputs.attr('readonly', 'readonly');
                    parent.find( '.sonata-ba-field-error-messages').remove();
                }
            });

            $( document ).on( 'ifChecked ifUnchecked', selector, function() {
                var item   = $( this );
                var parent = item.closest( 'tr' );
                var inputs = parent.find( inputsSelector );

                if ( item.prop( 'checked' ) ) {
                    inputs.attr('readonly', 'readonly');
                    parent.find( '.sonata-ba-field-error-messages').remove();
                } else {
                    inputs.removeAttr('readonly');
                }
            });
        });
    }

    function getRemoveCheckBoxSelector( formId, field ) {
        return '#field_widget_' + formId + '_' + field + ' td.sonata-ba-td-' + formId + '_' + field + '-_delete input[id *= "__delete"]';
    }
} );
