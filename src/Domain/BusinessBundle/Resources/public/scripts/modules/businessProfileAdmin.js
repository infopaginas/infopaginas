$( document ).ready( function() {

    if ( parentId && !businessProfileId ) {
        businessProfileId = parentId
    }

    var localityAjaxCall;
    var neighborhoodAjaxCall;

    var removeVideo         = $( '#' + formId + '_removeVideo' );
    var categoryField       = $( '#' + formId + '_categories' );
    var areasField          = $( '#' + formId + '_areas' );
    var localitiesField     = $( '#' + formId + '_localities' );
    var neighborhoodsField  = $( '#' + formId + '_neighborhoods' );

    var openAllTimeCheckboxes = $( '[ id *= "_openAllTime" ]' );

    $.each( ['#' + formId + '_serviceAreasType label', '#' + formId + '_serviceAreasType label ins'], function( index, fieldId ) {
        $( fieldId ).on( 'click', function() {
            var $self = $( this).parent().find( 'input[name="' + formId + '[serviceAreasType]"]' );
            var withinMilesOfMyBusinessField = $( '#' + formId + '_milesOfMyBusiness' );
            var withinMilesOfMyBusinessLabel = $( '#sonata-ba-field-container-' + formId + '_milesOfMyBusiness label' );

            if ( $self.val() == 'area' ) {
                disableServiceAreaTypeLocalityFields();

                withinMilesOfMyBusinessField.removeAttr( 'disabled' );
                withinMilesOfMyBusinessField.attr('required', 'required');

                if ( !withinMilesOfMyBusinessLabel.hasClass( 'required' ) ) {
                    withinMilesOfMyBusinessLabel.addClass( 'required' );
                }
            } else {
                enableServiceAreaTypeLocalityFields();

                withinMilesOfMyBusinessField.attr( 'disabled', 'disabled' );
                withinMilesOfMyBusinessField.removeAttr( 'required' );
                withinMilesOfMyBusinessLabel.removeClass( 'required' );
            }
        } );
    } );

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

    hideVideoAdd();

    removeVideo.on( 'ifChecked ifUnchecked' , function( e, aux ){
        if( removeVideo.prop( 'checked' ) ){
            showVideoAdd();
        }else{
            hideVideoAdd();
        }
    });

    var useMapAddress = $( '#' + formId + '_useMapAddress' );

    $.each( [ useMapAddress.parent().find('ins'), useMapAddress.parent().parent().parent().find('label') ], function( index, fieldId ) {
        $( fieldId ).on( 'click', function() {
            setUseMapAddress();
        } );
    } );

    updatedLocalities();
    updatedNeighborhoods();

    localitiesField.on( 'change', function() {
        updatedNeighborhoods();
    });

    areasField.on( 'change', function() {
        updatedLocalities();
    });

    function updatedNeighborhoods() {
        var localities = localitiesField.val();
        var data = {
            'currentLocale': currentLocale,
            'localities':    localities
        };

        if ( neighborhoodAjaxCall ) {
            neighborhoodAjaxCall.abort();
        }

        neighborhoodAjaxCall = $.post( Routing.generate( 'domain_business_get_neighborhoods', { businessProfileId: businessProfileId } ), data, function( response ) {
            updateSelect2FieldValues( neighborhoodsField, response.data );
        });
    }

    function updatedLocalities() {
        var areas = areasField.val();
        var data = {
            'currentLocale': currentLocale,
            'areas':         areas
        };

        if ( localityAjaxCall ) {
            localityAjaxCall.abort();
        }

        localityAjaxCall = $.post( Routing.generate( 'domain_business_get_localities', { businessProfileId: businessProfileId } ), data, function( response ) {
            updateSelect2FieldValues( localitiesField, response.data );
        });
    }

    function setUseMapAddress() {
        $.each( [ 'country','state', 'city', 'zipCode', 'streetAddress' ], function( targetIndex, targetFieldId ) {
            var input = $( '#' + formId + '_' + targetFieldId );

            if ( useMapAddress.prop( 'checked' ) ) {
                input.attr( 'disabled', 'disabled' );
            } else {
                input.removeAttr( 'disabled' );
            }
        } );
    }

    function hideVideoAdd() {
        $( '#' + formId + '_videoFile[data-hidden-field]').parent().parent().hide();
        $( '#' + formId + '_videoUrl[data-hidden-field]' ).parent().parent().hide();
        $( '#' + formId + '_videoName' ).parent().parent().show();
    }

    function showVideoAdd() {
        $( '#' + formId + '_videoFile[data-hidden-field]' ).parent().parent().show();
        $( '#' + formId + '_videoUrl[data-hidden-field]' ).parent().parent().show();
        $( '#' + formId + '_videoName' ).parent().parent().hide();
    }

    function updateSelect2FieldValues( field, data ) {
        var html = '';

        if ( data ) {
            $.each( data, function ( key, value ) {
                html += '<option value="' + value.id + '">' + value.name + '</option>';
            });
        }

        field.html( html );

        if ( html ) {
            field.attr( 'disabled', false );
        } else {
            field.attr( 'disabled', 'disabled' );
        }

        field.val( null ).trigger( 'change.select2' );

        var selectedValues = [];

        $.each( data, function ( key, value ) {
            if ( value.selected ) {
                selectedValues.push( value.id );
            }
        });

        field.select2( 'val', selectedValues );

        field.trigger( 'change' );
    }

    function disableServiceAreaTypeLocalityFields() {
        localitiesField.attr( 'disabled', 'disabled' );
        neighborhoodsField.attr( 'disabled', 'disabled' );
        areasField.attr( 'disabled', 'disabled' );
    }

    function enableServiceAreaTypeLocalityFields() {
        localitiesField.removeAttr( 'disabled' );
        neighborhoodsField.removeAttr( 'disabled' );
        areasField.removeAttr( 'disabled' );
    }

    $( 'select[data-select-all]' ).after( '<a class="select-all-button">Select all</a>' );

    $( 'body' ).on( 'click', 'a.select-all-button', function( e ) {
        e.preventDefault();

        var selectField = $( this ).parent().find( 'select' );

        if ( !selectField.attr( 'disabled' ) ) {
            var allOptions = selectField.find( 'option' );
            var selectedItems = [];

            allOptions.each(function() {
                selectedItems.push( $(this).val() );
            });

            selectField.select2( 'val', selectedItems );

            selectField.trigger( 'change' );
        }
    });

    setUseMapAddress();

    $( 'body' ).on( 'focus', '.working-hours-time-start', function(){
        $( this ).datetimepicker({
            format: 'hh:mm a',
            pickDate: false,
            pickSeconds: false,
            pick12HourFormat: false
        });
    });
} );
