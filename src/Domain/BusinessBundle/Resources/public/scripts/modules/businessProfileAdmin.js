$( document ).ready( function() {
    var currentId = businessProfileId;

    if ( parentId && !businessProfileId ) {
        businessProfileId = parentId
    }

    var localityAjaxCall = {};
    var neighborhoodAjaxCall;
    var addExtraSearchLock = false;
    var addRadioButtonCollection = false;
    var addListCollection = false;

    var nameField           = $( '#' + formId + '_name' );
    var cityField           = $( '#' + formId + '_city' );
    var categoryField       = $( '#' + formId + '_categories' );
    var areasField          = $( '#' + formId + '_areas' );
    var localitiesField     = $( '#' + formId + '_localities' );
    var neighborhoodsField  = $( '#' + formId + '_neighborhoods' );
    var milesOfMyBusinessField = $( '#' + formId + '_milesOfMyBusiness' );
    var keywordSelectors = '#sonata-ba-field-container-' + formId + '_keywords input[ id *= "_value" ]';

    var openAllTimeCheckboxes = $( '[ id *= "_openAllTime" ]' );

    var businessNameAjax = {
        request: null,
        queue: false,
        delay: 500,
    };

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

    $( 'div[ id $= "' + formId + '_phones" ]' ).on( 'sonata.add_element', function( event ) {
        handleBusinessProfilePhoneTypeChange();
        applyPhoneMask();
    });

    $( document ).on( 'ifChecked ifUnchecked', 'input[ id *= "_phones_" ]', function() {
        handleBusinessProfilePhoneTypeChange();
    });

    $( document ).on( 'submit', 'form', function( e ) {
        if ( handleBusinessProfilePhoneTypeChange() ) {
            $( 'html, body' ).animate({
                scrollTop: $( 'div[ id $= "' + formId + '_phones" ]' ).first().offset().top
            }, 2000);

            return false;
        }
    });

    validateBusinessName();

    nameField.on( 'input', function() {
        validateBusinessName();
    });

    cityField.on( 'input', function() {
        validateBusinessName();
    });

    function validateBusinessName() {
        if ( businessNameAjax.queue ) {
            clearTimeout( businessNameAjax.queue );
        }

        if ( businessNameAjax.request ) {
            businessNameAjax.request.abort();
        }

        if ( !nameField.val() ) {
            return;
        }

        var id = 0;

        if (currentId) {
            id = currentId;
        }

        businessNameAjax.queue = setTimeout(function() {
            var data = {
                businessName: nameField.val(),
                businessCity: cityField.val()
            };

            businessNameAjax.request = $.ajax({
                url: Routing.generate( 'domain_business_admin_validation_business_name', { id: id } ),
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success: handleBusinessNameError
            });
        }, businessNameAjax.delay);
    }

    applyPhoneMask();

    function applyPhoneMask() {
        var phones = $( 'input[ id $= "_phone" ]' );

        phones.mask( '999-999-9999' );
        phones.bind( 'paste', function () {
            $( this ).val( '' );
        });
    }

    function handleBusinessProfilePhoneTypeChange() {
        var mainCheckBoxes = $( 'input[id *= "_phones_"][type = "radio"][value = "main"]' );
        var errorBlock = $( '#' + formId + '_phoneCollection' );
        var hasMainPhone = false;
        var errors = [];
        var phoneCount = 0;

        $.each( mainCheckBoxes, function( index, item ) {
            var checkbox        = $( item );
            var deletedCheckbox = checkbox.parents( 'tr' ).first().find( 'input[id *= "__delete" ]' );

            if ( !deletedCheckbox.prop( 'checked' ) ) {
                if ( checkbox.prop( 'checked' ) ) {

                    if ( hasMainPhone ) {
                        errors.push( errorList.phones.not_unique );

                        return false;
                    }

                    hasMainPhone = true;
                }

                phoneCount++;
            }
        });

        if ( !hasMainPhone && phoneCount ) {
            errors.push( errorList.phones.no_main );
        }

        handlePhoneValidationError( errorBlock, errors );

        return errors.length;
    }

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

    function insertRadioButton( ids )
    {
        $.ajax({
            type: 'POST',
            url: Routing.generate( 'domain_business_get_radio_button_values' ),
            data: { ids: ids },
            dataType: 'JSON',
            success: function( response ) {
                $.each(response, function( key, value ) {
                    var parent = document.getElementById(formId + '_radioButtonCollection_' + key + '_value' ).parentElement;
                    var theForm = document.createElement( 'form' );
                    theForm.setAttribute( 'name', 'radioButtonCustomForm' + key );
                    theForm.setAttribute( 'id', 'radioButtonCustomForm' + key );

                    value.forEach( function( item, childKey ) {
                        theInput = document.createElement( 'input' );
                        theInput.setAttribute( 'type', 'radio' );
                        theInput.setAttribute( 'name', 'radioButtonCustom' + key );
                        theInput.setAttribute( 'id', 'radioButtonCustom' + key );
                        theInput.setAttribute( 'value', item['id'] );
                        var label = document.createElement( 'label');
                        label.appendChild( theInput );
                        label.setAttribute( 'class', 'custom-radio' );
                        label.innerHTML += '<span>' + item['title'] + '</span>';
                        theForm.appendChild( label );
                    });

                    parent.appendChild( theForm );

                    jQuery( "input[type='radio']" ).iCheck({
                        radioClass: 'iradio_square-blue'
                    });
                });

                $.each(response, function( key, value ) {
                    var valueIds = [];

                    $.each(value, function( key, item ) {
                        valueIds.push(item['id']);
                    });

                    var object = document.getElementById( formId + '_radioButtonCollection_' + key + '_value' );
                    var form = $( document.getElementById( 'radioButtonCustomForm' + key ) );

                    if (object.value) {
                        form.find( 'input' ).each( function () {
                            if ( this.value == object.value ) {
                                $( this.parentElement )[0].className += ' checked';
                            }
                        });
                    }

                    form.find( 'label' ).each(function () {
                        $( this ).click( function(e)
                        {
                            var object = document.getElementById( formId + '_radioButtonCollection_' + key + '_value' );
                            object.value = $( this ).find( 'input' )[0].value;
                        });
                    });

                    form.find( 'ins' ).each( function () {
                        $( this ).click( function( e )
                        {
                            var label = $( this.closest( 'label' ) );
                            var input = label.find( 'input' );
                            var object = document.getElementById( formId + '_radioButtonCollection_' + key + '_value' );
                            object.value = input[0].value;
                        });
                    });
                });
            }
        });
    }

    function addRadioButtons()
    {
        var ids = [];
        var radioValues = document.getElementsByClassName('radio-value');

        if (radioValues) {
            for (var i = 0; i < radioValues.length; i++) {
                var radioButton = $(document.getElementById(
                    'field_widget_' + formId + '_radioButtonCollection_' + i + '_radioButtons'
                ));
                var radioButtonInput = $('#' + formId + '_radioButtonCollection_' + i + '_radioButtons')[0];

                if (!radioButton.length) {
                    continue;
                }

                if (radioButtonInput.value) {
                    ids[i] = radioButtonInput.value;
                }

                $('#' + formId + '_radioButtonCollection_' + i + '_radioButtons').on('change', function () {
                    for (var i = 0; i < radioValues.length; i++) {
                        var radioButton = $(document.getElementById(
                            'field_widget_' + formId + '_radioButtonCollection_' + i + '_radioButtons'
                        ));
                        var radioButtonInput = $('#' + formId + '_radioButtonCollection_' + i + '_radioButtons')[0];

                        if (!radioButton.length) {
                            continue;
                        }

                        if (radioButtonInput.value) {
                            ids[i] = radioButtonInput.value;
                        }

                        var oldForm = document.getElementById('radioButtonCustomForm' + i);

                        if (oldForm) {
                            oldForm.parentNode.removeChild(oldForm);
                        }
                    }

                    var parts = this.id.split('_');

                    var object = document.getElementById(formId + '_radioButtonCollection_' + parts[2] + '_value');
                    object.value = '';

                    if (ids.length) {
                        insertRadioButton(ids);
                    }
                });
            }
        }

        if ( ids.length ) {
            insertRadioButton( ids );
        }
    }

    $( '#sonata-ba-field-container-' + formId + '_radioButtonCollection' ).on( 'sonata.add_element', function( event ) {
        if ( !addRadioButtonCollection ) {
            addRadioButtonCollection = true;

            setTimeout( function() {
                addRadioButtons();

                addRadioButtonCollection = false;
            }, 100 );
        }

    });

    addRadioButtons();

    function insertList( ids )
    {
        $.ajax({
            type: 'POST',
            url: Routing.generate( 'domain_business_get_list_values' ),
            data: { ids: ids },
            dataType: 'JSON',
            success: function( response ) {
                $.each(response, function( key, value ) {
                    var parent = document.getElementById( formId + '_listCollection_' + key + '_value' ).parentElement;
                    var select = document.createElement( 'select' );
                    var theForm = document.createElement( 'form' );
                    var isSelected = false;
                    theForm.setAttribute( 'name', 'listCustomForm' + key );
                    theForm.setAttribute( 'id', 'listCustomForm' + key );
                    select.setAttribute( 'id', 'listCustomSelect' + key );

                    value.forEach( function( item, childKey ) {
                        var option = document.createElement( 'option' );
                        option.setAttribute( 'value', item['id'] );
                        option.innerText = item.title;

                        if ( document.getElementById( formId + '_listCollection_' + key + '_value' ).value == item['id'] ) {
                            option.setAttribute( 'selected', 'selected' );
                            isSelected = true;
                        }

                        select.appendChild( option );
                    });

                    theForm.appendChild( select );
                    parent.appendChild( theForm );

                    if ( !isSelected ) {
                        document.getElementById( formId + '_listCollection_' + key + '_value' ).value
                            = $( '#listCustomSelect' + key + ' option:first' ).val();
                    }

                    $( '#listCustomSelect' + key ).select2( { minimumResultsForSearch: -1 } );

                    $( '#listCustomSelect' + key ).on( 'change', function() {
                        document.getElementById( formId + '_listCollection_' + key + '_value' ).value = this.value;
                    });
                });
            }
        });
    }

    function addLists()
    {
        var ids = [];
        var listValues = document.getElementsByClassName('list-value');

        if (listValues) {
            for (var i = 0; i < listValues.length; i++) {
                var list = $(document.getElementById('field_widget_' + formId + '_listCollection_' + i + '_lists'));
                var listInput = $('#' + formId + '_listCollection_' + i + '_lists')[0];

                if (!list.length) {
                    continue;
                }

                if (listInput.value) {
                    ids[i] = listInput.value;
                }

                $('#' + formId + '_listCollection_' + i + '_lists').on('change', function () {
                    for (var i = 0; i < listValues.length; i++) {
                        var list = $(document.getElementById('field_widget_' + formId + '_listCollection_' + i + '_lists'));
                        var listInput = $('#' + formId + '_listCollection_' + i + '_lists')[0];

                        if (!list.length) {
                            continue;
                        }

                        if (listInput.value) {
                            ids[i] = listInput.value;
                        }

                        var oldForm = document.getElementById('listCustomForm' + i);

                        if (oldForm) {
                            oldForm.parentNode.removeChild(oldForm);
                        }
                    }

                    var parts = this.id.split('_');

                    var object = document.getElementById(formId + '_listCollection_' + parts[2] + '_value');
                    object.value = '';

                    if (ids.length) {
                        insertList(ids);
                    }
                });
            }
        }

        if ( ids.length ) {
            insertList( ids );
        }
    }

    $( '#sonata-ba-field-container-' + formId + '_listCollection' ).on( 'sonata.add_element', function( event ) {
        if ( !addListCollection ) {
            addListCollection = true;

            setTimeout( function() {
                addLists();

                addListCollection = false;
            }, 100 );
        }

    });

    addLists();

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

    $( document ).on( 'change', 'select[ id *= "_areas" ]', function() {
        updatedLocalitiesBlock( this );
    });

    $( 'select[ id *= "_areas" ]').each(function() {
        updatedLocalitiesBlock( this );
    });

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

    function updateSelect2FieldValues( field, data ) {
        var html = '';
        var previousData = field.val();
        var previousOptions = $.map(field.find( 'option' ), function( option ) {
            return option.value;
        });

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
            } else if ( $.inArray( value.id.toString(), previousOptions ) < 1 ) {
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

    addCheckAllButton();

    $( document ).on( 'click', 'button.checkAll', function( e ) {
        e.preventDefault();

        $( this ).parent().find( 'input[ type = "checkbox" ]').each(function() {
            if ( !$( this ).prop( 'checked' ) ) {
                $( this ).iCheck( 'toggle' );
            }
        });
    });

    applySelectizePlugin();

    function applySelectizePlugin() {
        var elements = $( 'input.selectize-control:not(.selectized)' );

        elements.removeClass( 'form-control' );

        elements.selectize({
            plugins: [
                'restore_on_backspace',
                'remove_button'
            ],
            persist: false,
            create: true,
            createFilter: keywordValidator
        });
    }

    function addCheckAllButton() {
        $( '#sonata-ba-field-container-' + formId + '_paymentMethods' ).append( '<button class="btn btn-primary checkAll">Check All</button>' );
    }

    function keywordValidator() {
        var value = this.lastQuery;
        var errors = [];

        if ( !value ) {
            errors.push( errorList.keyword.notBlank );
        }

        if ( value.length > 255 ) {
            errors.push( errorList.keyword.maxLength );
        }

        if ( value && value.length < 2 ) {
            errors.push( errorList.keyword.minLength );
        }

        var validateOneWord = validators.keyword.oneWord;

        if ( !validateOneWord.test( value )) {
            errors.push( errorList.keyword.oneWord );
        }

        handleKeywordValidationError( errors );

        return !errors.length;
    }

    function handleKeywordValidationError( errors ) {
        var parent = $( '#sonata-ba-field-container-' + formId + '_keywordText' );

        parent.find( '.sonata-ba-field-error-messages').remove();

        if ( errors.length ) {
            var errorHtml = '<div class="help-inline sonata-ba-field-error-messages"><ul class="list-unstyled">';

            $.each(errors, function( index, value ) {
                errorHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + value + '</li>';
            });

            errorHtml += '</ul></div>';

            parent.append( errorHtml );
        }
    }

    function handlePhoneValidationError( input, errors ) {
        var parent = input.parent();

        parent.find( '.sonata-ba-field-error-messages' ).remove();
        parent.removeClass( 'has-error' );

        if ( errors.length ) {
            var errorHtml = '<div class="help-inline sonata-ba-field-error-messages"><ul class="list-unstyled">';

            $.each(errors, function( index, value ) {
                errorHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + value + '</li>';
            });

            errorHtml += '</ul></div>';

            parent.addClass( 'has-error' );
            input.after( errorHtml );
        }
    }

    function handleBusinessNameError( response ) {
        var errors = response.errors;
        var message = response.message;

        handleBusinessNotUniqueError( errors, message, nameField );
        handleBusinessNotUniqueError( errors, message, cityField );
    }

    function handleBusinessNotUniqueError( errors, message, input ) {
        var parent = input.parent();

        parent.find( '.sonata-ba-field-error-messages' ).remove();
        parent.removeClass( 'has-warning' );

        if ( errors.length ) {
            var errorHtml = '<div class="help-inline sonata-ba-field-error-messages"><ul class="list-unstyled">';
            errorHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + message + '</li>';

            $.each(errors, function( index, value ) {
                var link = '<a href="' + value.url + '" target="_blank">' + value.name + ', ' + value.city + '</a>';
                errorHtml += '<li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + value.id + ': ' + link + '</li>';
            });

            errorHtml += '</ul></div>';

            parent.addClass( 'has-warning' );
            input.after( errorHtml );
        }
    }
} );
