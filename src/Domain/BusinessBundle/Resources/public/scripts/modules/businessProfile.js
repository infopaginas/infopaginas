define(['jquery', 'bootstrap', 'business/tools/form', 'tools/spin', 'tools/select', 'business/tools/businessProfileClose', 'tools/mapSpin', 'business/tools/phones', 'business/tools/workingHours', 'selectize'], function( $, bootstrap, FormHandler, Spin, select, businessProfileClose, MapSpin ) {
    'use strict';

    //init businessProfile object variables
    var businessProfile = function() {
        this.urls = {
            saveBusinessProfile: Routing.generate( 'domain_business_profile_save' ),
            categoryAutoComplete: Routing.generate( 'domain_business_category_autocomplite' ),
            getLocalityByCoord: Routing.generate( 'domain_search_closest_locality_by_coord' ),
        };

        this.serviceAreasAreaChoiceValue = 'area';

        this.freeProfileFormName = 'domain_business_bundle_business_profile_form_type';

        this.html = {
            buttons: {
                geocodeButtonId: '#geocodeButton',
                newProfileSaveButtonId: '#newProfileRequestButton',
                fileUploadButton: '.file-upload-button'
            },
            forms: {
                newProfileRequestFormId: '#businessProfileRequestForm'
            },
            fields: {
                countrySelectId: '#' + this.freeProfileFormName + '_country',
                stateInputId: '#' + this.freeProfileFormName + '_state',
                cityInputId: '#' + this.freeProfileFormName + '_city',
                zipInputId: '#' + this.freeProfileFormName + '_zipCode',
                addressInputId: '#' + this.freeProfileFormName + '_streetAddress',
                latitudeInputId: '#' + this.freeProfileFormName + '_latitude',
                longitudeInputId: '#' + this.freeProfileFormName + '_longitude',
                withinMilesOfMyBusinessFieldId: '#' + this.freeProfileFormName + '_milesOfMyBusiness',
                localitiesFieldId: '#' + this.freeProfileFormName + '_localities',
                catalogLocalityId: '#' + this.freeProfileFormName + '_catalogLocality',
                serviceAreaRadioName: '[serviceAreasType]',
                categoriesId: '#' + this.freeProfileFormName + '_categories',
                categoryOptions: '#category_options'
            },
            pageHeader: 'header.header',
            scrollBlock: 'html, body',
            mainTabsSelector: 'div.tab-pane',
            closeBusinessProfileLoadingSpinnerContainerId: 'close-business-profile-spinner-container',
            loadingSpinnerContainerClass: '.spinner-container',
            mapContainerId: 'google-map',
            newProfileRequestSpinnerContainerId: 'new-profile-loading-spinner-container-id',
            languageSelectorClass: '.language-selector',
            imagesTable: '.table-media-image',
            milesOfMyBusinessSpan: '.miles-of-business',
            localitiesFieldSpan: '.locality-field',
            areasFieldSpan: '.area-field',
            asteriskClass: 'i.fa-asterisk',
            asteriskTag: '<i class="fa fa-asterisk" aria-hidden="true"></i>',
            imageValidationErrors: '#imageValidationErrors',
            videoValidationErrors: '#videoValidationErrors'
        };

        this.newProfileRequestFormHandler = new FormHandler({
            formId: this.html.forms.newProfileRequestFormId,
            spinnerId: this.html.newProfileRequestSpinnerContainerId
        });

        this.selectizeOptions = {
            plugins: ['remove_button'],
            delimiter: ',',
            persist: true
        };

        this.geocoder = new google.maps.Geocoder();
        this.businessProfileClose = new businessProfileClose;
        this.mapSpinner = new MapSpin( this.html.mapContainerId );

        this.spinner = new Spin();

        this.isDirty = false;
        this.formSubmitting = false;

        this.currentLocale = $( this.html.languageSelectorClass + '.selected' ).data('locale');

        this.run();
    };

    businessProfile.prototype.getBusinessAddress = function() {
        var country = $( this.html.fields.countrySelectId + ' option:selected' ).text();
        var state = $( this.html.fields.stateInputId ).val();
        var city = $( this.html.fields.cityInputId ).val();
        var zip = $( this.html.fields.zipInputId ).val();
        var address = $( this.html.fields.addressInputId ).val();

        return country + ' ' + state + ' ' + city + ' ' + zip + ' ' + address;
    };

    businessProfile.prototype.getLatLngByAddress = function(address, callback) {
        this.geocoder.geocode({
            "address": address
        }, function(results) {
            if (results[0]) {
                callback(results[0].geometry.location);
            } else {
                console.log('results[0] is empty');
            }
        });
    };

    businessProfile.prototype.updateAddressByLatLng = function(latlng) {
        var self = this;

        this.geocoder.geocode({
            'location': latlng
        }, function(results) {
            if (results[0]) {
                self.updateAddress(results[0].address_components);
            } else {
                console.log('results[0] is empty');
            }
        });
    };

    businessProfile.prototype.updateLocality = function (localityId) {
        $( this.html.fields.catalogLocalityId ).val( localityId ).trigger('change');
    };

    businessProfile.prototype.updateAddress = function(address)
    {
        var streetNumber = '';
        var street = '';
        var city = '';
        var state = '';
        var zip = '';

        $.each(address, function (i, addressComponent) {
            if( addressComponent.types[0] == "route") {
                street = addressComponent.long_name;
            }

            if( addressComponent.types[0] == 'locality' ) {
                city = addressComponent.long_name;
            }

            if( addressComponent.types[0] == 'sublocality' ) {
                state = addressComponent.long_name;
            }

            if( addressComponent.types[0] == "postal_code" ) {
                zip = addressComponent.long_name;
            }

            if( addressComponent.types[0] == "street_number" ) {
                streetNumber = addressComponent.long_name;
            }
        });

        $( this.html.fields.addressInputId ).val( streetNumber + ' ' + street );
        $( this.html.fields.cityInputId ).val( city );
        $( this.html.fields.stateInputId ).val( state );
        $( this.html.fields.zipInputId ).val( zip );
    };

    businessProfile.prototype.updateLatLngFields = function(lat, lng) {
        $( this.html.fields.latitudeInputId ).val( lat );
        $( this.html.fields.longitudeInputId ).val( lng );
    };

    businessProfile.prototype.onMarkerPositionChange = function(event) {
        var that = this;

        var latlng = event.latLng;

        $.ajax({
            type: 'POST',
            url: this.urls.getLocalityByCoord,
            data: {'clt': latlng.lat(), 'clg': latlng.lng()},
            success: function(data){
                that.updateLocality(data['localityId']);
            }
        });

        this.geocoder.geocode({
            'location': latlng
        }, function(results) {
            that.updateAddress(results[0].address_components);
            that.updateLatLngFields( latlng.lat(), latlng.lng() );
            that.updateFieldSelectionFocus();
        });
    };

    businessProfile.prototype.getGoogleMapObject = function(lat, lng)
    {
        var mapOptions = {
            zoom: 15,
            center: new google.maps.LatLng(lat, lng),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var mapContainer = document.getElementById(this.html.mapContainerId);

        return new google.maps.Map(mapContainer, mapOptions);
    };

    businessProfile.prototype.initGoogleMap = function() {
        var address = this.getBusinessAddress();
        var that = this;

        // convert decimal delimiter
        // see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/parseFloat
        var lat = parseFloat( $( that.html.fields.latitudeInputId ).val().replace( ',', '.' ) );
        var lng = parseFloat( $( that.html.fields.longitudeInputId ).val().replace( ',', '.' ) );

        if ( !lat ) {
            lat = MAP_LAT;
        }

        if ( !lng ) {
            lng = MAP_LNG;
        }

        var map = that.getGoogleMapObject( lat, lng );

        google.maps.event.trigger( map, 'resize' );

        var marker = new google.maps.Marker({
            position: {
                lat: lat,
                lng: lng
            },
            map: map,
            title: address,
            draggable: true
        });

        // change address value when marker is dragged
        marker.addListener( 'dragend', $.proxy( that.onMarkerPositionChange, that ) );

        that.updateLatLngFields( lat, lng );
        that.updateFieldSelectionFocus();
    };

    businessProfile.prototype.handleGeocodeSearch = function() {
        var that = this;

        $( this.html.buttons.geocodeButtonId ).on('click', function( event ) {
            that.initGoogleMap();
            event.preventDefault();
        });
    };

    /**
     * Move marker correspond of coordinates
     */
    businessProfile.prototype.moveMarker = function() {
        var self = this;

        $.each( [this.html.fields.latitudeInputId, this.html.fields.longitudeInputId], function( index, fieldId ) {
            $( fieldId ).change( function( event ) {
                var lat = function() {
                    return parseFloat( $( self.html.fields.latitudeInputId ).val() );
                };

                var lng = function() {
                    return parseFloat( $( self.html.fields.longitudeInputId ).val() );
                };

                var Latlng = new google.maps.LatLng(lat(), lng());

                self.updateAddressByLatLng(Latlng);
                self.initGoogleMap();
                setTimeout(
                    function() {
                        $( self.html.buttons.geocodeButtonId ).click();
                    }, 100
                );
            });
        } );
    };

    businessProfile.prototype.validateImages = function() {
        var that = this;
        var descriptions = $( '#gallery' ).find( 'textarea[ id *= "_description"]' );
        var notEmptyString = /\S/;
        var hasError = false;

        descriptions.each(function() {
            var field       = $( this );
            var fieldParent = field.parent();

            fieldParent.removeClass( 'field--not-valid' );
            fieldParent.find( 'span[data-error-message]' ).remove();

            if ( !notEmptyString.test( field.val() )) {
                fieldParent.addClass( 'field--not-valid' );
                field.after( '<span data-error-message class="error">' + $( that.html.imageValidationErrors ).data( 'required' ) + '</span>' );

                hasError = true;
            }
        });

        return hasError;
    };

    businessProfile.prototype.validateVideo = function() {
        var that = this;
        var fields = $( '#video' ).find( 'textarea[ id *= "_description"], input[ id *= "_title"]' );
        var notEmptyString = /\S/;
        var hasError = false;

        fields.each(function() {
            var field       = $( this );
            var fieldParent = field.parent();

            fieldParent.removeClass( 'field--not-valid' );
            fieldParent.find( 'span[data-error-message]' ).remove();

            if ( !notEmptyString.test( field.val() )) {
                fieldParent.addClass( 'field--not-valid' );
                field.after( '<span data-error-message class="error">' + $( that.html.videoValidationErrors ).data( 'required' ) + '</span>' );

                hasError = true;
            }
        });

        return hasError;
    };

    businessProfile.prototype.scrollToError = function() {
        var errorBlock = $( 'span[data-error-message]' ).first().parent();
        var errorTab   = errorBlock.parents( 'div.tab-pane' );
        var errorTabId = errorTab.attr( 'id' );

        if ( errorBlock.length && errorTabId ) {
            var headerHeight = $( this.html.pageHeader ).height();

            $( 'li a[href = "#' + errorTabId + '"]' ).click();

            if ( errorBlock.length ) {
                $( this.html.scrollBlock ).animate({
                    scrollTop: errorBlock.offset().top - headerHeight
                }, 1000);
            }
        }
    };

    businessProfile.prototype.handleProfileSave = function() {
        var that = this;

        $( document ).on( 'submit' , this.html.forms.newProfileRequestFormId , function( event ) {
            that.formSubmitting = true;
            event.preventDefault();

            if ( that.validateImages() || that.validateVideo() ) {
                that.scrollToError();
                that.formSubmitting = false;

                return false;
            }

            var data = [{
                name: 'locale',
                value: $( that.html.languageSelectorClass + '.selected' ).data( 'locale' )
            }];

            var profileId = $( this ) .data( 'id' );

            if( profileId.length !== 0 ) {
                data.push({
                    name: 'businessProfileId',
                    value: profileId
                });
            }

            if ( $( that.html.imagesTable ).length ) {
                var imageName, images, regexp;

                $.each ( $( that.html.imagesTable + ' .hidden-media' ), function ( outerIndex ) {
                    imageName = that.freeProfileFormName + '[images][' + outerIndex + '][media]';
                    images = $( that.html.imagesTable + ' input[name="' + imageName + '"]' );

                    if ( images.length > 1 ) {
                        regexp = new RegExp(outerIndex, 'gi');

                        $.each( images, function ( index, value ) {
                            this.setAttribute( 'name', imageName.replace( regexp, ( index + outerIndex ) ) );
                        });
                    }
                });
            }

            that.newProfileRequestFormHandler.doRequest( that.urls.saveBusinessProfile, data );

            event.preventDefault();
        });
    };

    businessProfile.prototype.handleServiceAreaChange = function() {
        var that = this;

        var serviceAreasRadioName = this.freeProfileFormName + this.html.fields.serviceAreaRadioName;

        $( document ).on( 'change' , 'input[name="' + serviceAreasRadioName + '"]', function() {
            var $self = $(this);
            var milesOfMyBusinessAsteriskClass = that.html.milesOfMyBusinessSpan + ' ' + that.html.asteriskClass;
            var localitiesFieldAsteriskClass = that.html.localitiesFieldSpan + ' ' + that.html.asteriskClass;
            var html = '';

            var withinMiles = $( that.html.fields.withinMilesOfMyBusinessFieldId );
            var localities = $( that.html.fields.localitiesFieldId );

            var localitiesSelectize     = localities.selectize( that.selectizeOptions )[0].selectize;

            if ( $self.val() == that.serviceAreasAreaChoiceValue ) {
                withinMiles.removeAttr( 'disabled' );
                localities.attr( 'disabled', 'disabled' );

                localitiesSelectize.disable();

                if ( !$( milesOfMyBusinessAsteriskClass ).length ) {
                    html = $( that.html.milesOfMyBusinessSpan ).text();
                    var pos = html.indexOf( ':', 1 );
                    html = html.slice( 0, pos ) + that.html.asteriskTag + ' ' + html.slice( pos );
                    $( that.html.milesOfMyBusinessSpan ).html( html );
                }
                withinMiles.attr('required', 'required');
                $( milesOfMyBusinessAsteriskClass ).show();
                $( localitiesFieldAsteriskClass ).hide();
            } else {
                areas.removeAttr( 'disabled' );
                localities.removeAttr( 'disabled' );
                withinMiles.attr( 'disabled', 'disabled' );

                localitiesSelectize.enable();

                withinMiles.removeAttr( 'required' );
                if ( $( localitiesFieldAsteriskClass ).length ) {
                    localities.attr('required', 'required');
                    $( localitiesFieldAsteriskClass ).show();
                } else {
                    html = $( that.html.localitiesFieldSpan ).text();
                    var pos = html.indexOf( ':', 1 );
                    html = html.slice( 0, pos ) + that.html.asteriskTag + ' ' + html.slice( pos );
                    $( that.html.localitiesFieldSpan ).html( html );
                }
                $( milesOfMyBusinessAsteriskClass ).hide();
            }

            new select();
        });
    };

    businessProfile.prototype.getFormData = function() {
        var $form = document.getElementById('businessProfileRequestForm');
        var formData = new FormData($form);

        var images = this.getUploadedFiles();

        for (var i in images) {
            formData.append('img', images[i]);
        }

        return formData;
    };

    businessProfile.prototype.getUploadedFiles = function() {
        var $field = $('#domain_business_bundle_business_profile_form_type_files');

        var images = new FormData;

        var files = $field.prop('files');

        return files;
    };

    businessProfile.prototype.handleFormChange = function () {
        var self = this;

        $( document ).on( 'change' , '#businessProfileRequestForm', function() {
            self.isDirty = true;
        });
    };

    businessProfile.prototype.beforeUnload = function ( e ) {
        if (this.formSubmitting || !this.isDirty) {
            return undefined;
        }

        var confirmationMessage = 'Changes that you made may not be saved.';

        return confirm( confirmationMessage );
    };

    //build form field id
    businessProfile.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    businessProfile.prototype.updateFieldSelectionFocus = function () {
        $( '.form input, .form textarea' ).each(function() {
            var $this;

            $this = $( this );
            if ($this.prop( 'value' ).length !== 0){
                $this.parent().addClass( 'field-active' );
            } else {
                $this.parent().removeClass( 'field-active field-filled' );
                $this.parent().find( 'label' ).removeClass( 'label-active' );
            }
        });
    };

    businessProfile.prototype.initAutoCompleteCategoriesField = function () {
        var that = this;
        var optionsData = $( this.html.fields.categoryOptions ).data( 'category-ids' );
        var options = $.map( optionsData, function( value, index ) {
            return [ value ];
        });
        var optionIds = $.map( optionsData, function( value, index ) {
            return [ value.id ];
        });

        var categories = $( '#domain_business_bundle_business_profile_form_type_categoryIds' ).selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: true,
            create: false,
            options: options,
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: that.urls.categoryAutoComplete,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        q: query,
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res);
                    }
                });
            }
        });

        categories[0].selectize.setValue( optionIds );
    };

    //setup required "listeners"
    businessProfile.prototype.run = function() {
        this.handleGeocodeSearch();
        this.moveMarker();
        this.handleProfileSave();
        this.handleServiceAreaChange();
        this.handleFormChange();
        this.initAutoCompleteCategoriesField();

        var that = this;

        $( 'a[href="#businessAddress"]').on('shown.bs.tab', function(){
            that.initGoogleMap();
        } );

        $( this.html.buttons.fileUploadButton ).on( 'click', function() {
            $( this ).parent().find( 'input' ).click();
        });

        new select();
    };

    return businessProfile;
});
