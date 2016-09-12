$( document ).ready( function() {
    $.each( ['#' + formId + '_serviceAreasType label', '#' + formId + '_serviceAreasType label ins'], function( index, fieldId ) {
        $( fieldId ).on( 'click', function() {
            //var $self = $( 'input[name="' + formId + '[serviceAreasType]"]' );
            var $self = $( this).parent().find( 'input[name="' + formId + '[serviceAreasType]"]' );
            var withinMilesOfMyBusinessField = $( '#' + formId + '_milesOfMyBusiness' );
            var withinMilesOfMyBusinessLabel = $( '#sonata-ba-field-container-' + formId + '_milesOfMyBusiness label' );
            var localitiesField = $( '#' + formId + '_areas' );
            var localitiesLabel = $( '#sonata-ba-field-container-' + formId + '_areas label' );
            var localitiesDropdown = localitiesField.parent( '.sonata-ba-field' ).find( '.select2-container-multi' );

            if ( $self.val() == 'area' ) {
                withinMilesOfMyBusinessField.removeAttr( 'disabled' );
                localitiesField.attr('disabled', 'disabled');

                localitiesField.removeAttr( 'required' );
                withinMilesOfMyBusinessField.attr('required', 'required');
                localitiesLabel.removeClass( 'required' );

                if ( !withinMilesOfMyBusinessLabel.hasClass( 'required' ) ) {
                    withinMilesOfMyBusinessLabel.addClass( 'required' );
                }
            } else {
                localitiesField.removeAttr( 'disabled' );
                withinMilesOfMyBusinessField.attr( 'disabled', 'disabled' );

                localitiesField.attr('required', 'required');
                withinMilesOfMyBusinessField.removeAttr( 'required' );
                localitiesDropdown.removeClass( 'select2-container-disabled' );
                withinMilesOfMyBusinessLabel.removeClass( 'required' );

                if ( !localitiesLabel.hasClass( 'required' ) ) {
                    localitiesLabel.addClass( 'required' );
                }
            }
        } );
    } );
} );
