$( document ).ready( function() {

    if ( parentId && !businessProfileId ) {
        businessProfileId = parentId
    }

    var localityAjaxCall = {};
    var neighborhoodAjaxCall;
    var addExtraSearchLock = false;

    var categoryField       = $( '#' + formId + '_categories' );
    var areasField          = $( '#' + formId + '_areas' );
    var localitiesField     = $( '#' + formId + '_localities' );
    var neighborhoodsField  = $( '#' + formId + '_neighborhoods' );
    var milesOfMyBusinessField = $( '#' + formId + '_milesOfMyBusiness' );

    var openAllTimeCheckboxes = $( '[ id *= "_openAllTime" ]' );

    $( 'input[ id *= "_serviceAreasType_" ]' ).each(function() {
        if ( $( this ).prop( 'checked' ) ) {
            handleServiceAreaTypeChange( this );
        }
    });

    $( document ).on( 'ifChecked ifUnchecked', 'input[ id *= "_serviceAreasType_" ]', function() {
        if ( $( this ).prop( 'checked' ) ) {
            handleServiceAreaTypeChange( this );
        }
    });

    function handleServiceAreaTypeChange( elem ) {
        var isMainBlock = checkServiceAreaTypeBlockMain( elem );
        var serviceAreaType = $( elem ).val();

        setServiceAreaTypeValidation( elem, serviceAreaType, isMainBlock );
    }

    function checkServiceAreaTypeBlockMain( elem ) {
        var input = $( elem ).parent().find( 'input[ id *= "_extraSearches_" ]' );

        if ( input.length ) {
            return false;
        }

        return true;
    }

    function setServiceAreaTypeValidation(elem, serviceAreaType, isMainBlock) {
        var areas, localities, neighborhoods, milesOfMyBusiness;

        if ( !isMainBlock ) {
            var extraSearchBlock = $( elem )
                .parents( '.sonata-ba-td-' + formId + '_extraSearches-serviceAreasType')
                .parent();

            areas          = extraSearchBlock.find( 'select[ id *= "_areas" ]' );
            localities     = extraSearchBlock.find( 'select[ id *= "_localities" ]' );
            neighborhoods  = extraSearchBlock.find( 'select[ id *= "_neighborhoods" ]' );
            milesOfMyBusiness = extraSearchBlock.find( 'input[ id *= "_milesOfMyBusiness" ]' );
        } else {
            areas          = areasField;
            localities     = localitiesField;
            neighborhoods  = neighborhoodsField;
            milesOfMyBusiness = milesOfMyBusinessField;
        }

        if ( serviceAreaType == 'area' ) {
            areas.attr( 'disabled', 'disabled' );
            localities.attr( 'disabled', 'disabled' );
            neighborhoods.attr( 'disabled', 'disabled' );

            milesOfMyBusiness.removeAttr( 'disabled' );
            milesOfMyBusiness.attr('required', 'required');

            if ( !milesOfMyBusiness.hasClass( 'required' ) ) {
                milesOfMyBusiness.addClass( 'required' );
            }
        } else {
            areas.removeAttr( 'disabled' );
            localities.removeAttr( 'disabled' );
            neighborhoods.removeAttr( 'disabled' );

            milesOfMyBusiness.attr( 'disabled', 'disabled' );
            milesOfMyBusiness.removeAttr( 'required' );
            milesOfMyBusiness.removeClass( 'required' );
        }
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

    var useMapAddress = $( '#' + formId + '_useMapAddress' );

    $.each( [ useMapAddress.parent().find('ins'), useMapAddress.parent().parent().parent().find('label') ], function( index, fieldId ) {
        $( fieldId ).on( 'click', function() {
            setUseMapAddress();
        } );
    } );

    $( document ).on( 'change', 'select[ id *= "_areas" ]', function() {
        updatedLocalitiesBlock( this );
    });

    $( 'select[ id *= "_areas" ]').each(function() {
        updatedLocalitiesBlock( this );
    });

    updatedNeighborhoods();

    localitiesField.on( 'change', function() {
        updatedNeighborhoods();
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

    function updatedLocalitiesBlock( elem ) {
        var isMainBlock = checkLocalityBlockMain( elem );
        var elementId = $( elem ).attr( 'id' );
        var areas, localities;
        var areasData = $( elem ).val();

        if ( !isMainBlock ) {
            var extraSearchBlock = $( elem )
                .parents( '.sonata-ba-td-' + formId + '_extraSearches-areas' )
                .parent();

            localities = extraSearchBlock.find( 'select[ id *= "_localities" ]' );
        } else {
            localities = localitiesField;
        }

        if ( areasData ) {
            var data = {
                'currentLocale': currentLocale,
                'areas':         areasData
            };

            if ( localityAjaxCall.hasOwnProperty( elementId ) ) {
                localityAjaxCall[ elementId ].abort();
            }

            localityAjaxCall[ elementId ] = $.post( Routing.generate( 'domain_business_get_localities', { businessProfileId: businessProfileId } ), data, function( response ) {
                updateSelect2FieldValues( localities, response.data );
            });
        } else {
            updateSelect2FieldValues(localities, []);
        }
    }

    function checkLocalityBlockMain( elem ) {
        var select = $( elem ).parent().find( 'select[ id *= "_extraSearches_" ]' );

        if ( select.length ) {
            return false;
        }

        return true;
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

    function updateSelect2FieldValues( field, data ) {
        var html = '';
        var previousData = field.val();

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
            } else if ( previousData && $.inArray( value.id.toString(), previousData ) > -1 ) {
                selectedValues.push( value.id );
            }
        });

        field.select2( 'val', selectedValues );

        field.trigger( 'change' );
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

    $('#sonata-ba-field-container-' + formId + '_extraSearches').on('sonata.add_element', function( event ) {
        if ( !addExtraSearchLock ) {
            addExtraSearchLock = true;

            setTimeout(function() {
                $( 'input[ id *= "_serviceAreasType_" ]' ).each(function() {
                    if ( $( this ).prop( 'checked' ) ) {
                        handleServiceAreaTypeChange( this );
                    }
                });

                $( '#field_container_' + formId + '_extraSearches select[ id *= "_areas" ]' ).each(function() {
                    updatedLocalitiesBlock( this );
                });

                addExtraSearchLock = false;
            }, 100);
        }
    });
} );
