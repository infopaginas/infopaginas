$( document ).ready( function() {
    var categoryField = $( '#' + formId + '_categories' );

    $.each( ['#' + formId + '_serviceAreasType label', '#' + formId + '_serviceAreasType label ins'], function( index, fieldId ) {
        $( fieldId ).on( 'click', function() {
            var $self = $( this).parent().find( 'input[name="' + formId + '[serviceAreasType]"]' );
            var withinMilesOfMyBusinessField = $( '#' + formId + '_milesOfMyBusiness' );
            var withinMilesOfMyBusinessLabel = $( '#sonata-ba-field-container-' + formId + '_milesOfMyBusiness label' );
            var localitiesField = $( '#' + formId + '_localities' );
            var neighborhoodsField = $( '#' + formId + '_neighborhoods' );
            var localitiesLabel = $( '#sonata-ba-field-container-' + formId + '_localities label' );
            var localitiesDropdown = localitiesField.parent( '.sonata-ba-field' ).find( '.select2-container-multi' );

            if ( $self.val() == 'area' ) {
                withinMilesOfMyBusinessField.removeAttr( 'disabled' );
                localitiesField.attr('disabled', 'disabled');
                neighborhoodsField.attr('disabled', 'disabled');

                withinMilesOfMyBusinessField.attr('required', 'required');

                if ( !withinMilesOfMyBusinessLabel.hasClass( 'required' ) ) {
                    withinMilesOfMyBusinessLabel.addClass( 'required' );
                }
            } else {
                localitiesField.removeAttr( 'disabled' );
                neighborhoodsField.removeAttr( 'disabled' );
                withinMilesOfMyBusinessField.attr( 'disabled', 'disabled' );

                withinMilesOfMyBusinessField.removeAttr( 'required' );
                localitiesDropdown.removeClass( 'select2-container-disabled' );
                withinMilesOfMyBusinessLabel.removeClass( 'required' );
            }
        } );
    } );


    var useMapAddress = $( '#' + formId + '_useMapAddress' );

    $.each( [ useMapAddress.parent().find('ins'), useMapAddress.parent().parent().parent().find('label') ], function( index, fieldId ) {
        $( fieldId ).on( 'click', function() {
            setUseMapAddress();
        } );
    } );

    getSubcategories();

    categoryField.on( 'change', function() {
        getSubcategories();
    });

    function getSubcategories() {
        var categoryId = categoryField.val();
        var subcategories = $( '#' + formId + '_subcategories' );

        subcategories.html( '' );
        subcategories.val( null ).trigger('change.select2');
        subcategories.attr( 'disabled', 'disabled' );

        $.post( Routing.generate('domain_business_get_subcaregories', {categoryId: categoryId, businessProfileId: businessProfileId}), function( response ) {
            var html = '';

            if ( response.data ) {
                $.each( response.data, function ( key, value ) {
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
            }

            subcategories.html( html );

            if ( html ) {
                subcategories.attr( 'disabled', false );
            } else {
                subcategories.attr( 'disabled', 'disabled' );
            }

            subcategories.val( null ).trigger( 'change.select2' );

            var selectedValues = [];

            $.each( response.data, function ( key, value ) {
                if ( value.selected ) {
                    selectedValues.push( value.id );
                }
            });

            subcategories.select2( 'val', selectedValues );
        });
    }

    function setUseMapAddress() {
        $.each( [ 'country','state', 'city', 'zipCode', 'streetAddress', 'extendedAddress' ], function( targetIndex, targetFieldId ) {
            var input = $( '#' + formId + '_' + targetFieldId );

            if ( useMapAddress.prop( 'checked' ) ) {
                input.attr( 'disabled', 'disabled' );
            } else {
                input.removeAttr( 'disabled' );
            }
        } );
    }

    setUseMapAddress();
} );
