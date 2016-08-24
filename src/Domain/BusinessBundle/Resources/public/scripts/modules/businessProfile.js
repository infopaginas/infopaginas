define(['jquery', 'bootstrap', 'alertify', 'business/tools/form', 'tools/spin', 'tools/select', 'business/tools/phones'], function( $, bootstrap, alertify, FormHandler, Spin, select ) {
    'use strict';

    //init businessProfile object variables
    var businessProfile = function() {
        this.urls = {
            saveBusinessProfile: Routing.generate('domain_business_profile_save'),
            closeBusinessProfileURL: Routing.generate('domain_business_profile_close')
        };

        this.serviceAreasAreaChoiceValue = 'area';

        this.freeProfileFormName = 'domain_business_bundle_business_profile_form_type';

        this.html = {
            buttons: {
                geocodeButtonId: '#geocodeButton',
                newProfileSaveButtonId: '#newProfileRequestButton',
                closeBusinessProfileButtonId: '#closeBusinessProfileButton'
            },
            forms: {
                newProfileRequestFormId: '#businessProfileRequestForm',
                closeBusinessProfileFormId: '#closeBusinessProfileForm',
                closeBusinessProfileFormPrefix: 'domain_business_bundle_business_close_request_type'
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
                serviceAreaRadioName: '[serviceAreasType]'
            },
            modals: {
                closeBusinessProfileModalId: '#closeBusinessProfileModal'
            },
            closeBusinessProfileLoadingSpinnerContainerId: 'close-business-profile-spinner-container',
            loadingSpinnerContainerClass: '.spinner-container',
            mapContainerId: 'google-map',
            newProfileRequestSpinnerContainerId: 'new-profile-loading-spinner-container-id',
            languageSelectorClass: '.language-selector'
        };

        this.newProfileRequestFormHandler = new FormHandler({
            formId: this.html.forms.newProfileRequestFormId,
            spinnerId: this.html.newProfileRequestSpinnerContainerId
        });

        this.geocoder = new google.maps.Geocoder();

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
            callback(results[0].geometry.location);
        });
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

    businessProfile.prototype.updateLatLngFields = function(latLng) {
        $( this.html.fields.latitudeInputId ).val( latLng.lat() );
        $( this.html.fields.longitudeInputId ).val( latLng.lng() );
    };

    businessProfile.prototype.onMarkerPositionChange = function(event) {
        var that = this;

        var latlng = event.latLng;

        this.geocoder.geocode({
            'location': latlng
        }, function(results) {
            that.updateAddress(results[0].address_components);
            that.updateLatLngFields(latlng);
        });
    };

    businessProfile.prototype.getGoogleMapObject = function(latlng)
    {
        var mapOptions = {
            zoom: 15,
            center: new google.maps.LatLng(latlng.lat(), latlng.lng()),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var mapContainer = document.getElementById(this.html.mapContainerId);

        return new google.maps.Map(mapContainer, mapOptions);
    };

    businessProfile.prototype.initGoogleMap = function() {
        var address = this.getBusinessAddress();

        var that = this;

        this.getLatLngByAddress(address, function(latlng) {

            var map = that.getGoogleMapObject(latlng);

            google.maps.event.trigger(map, 'resize');

            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                title: address,
                draggable: true
            });

            // change address value when marker is dragged
            marker.addListener('dragend', $.proxy(that.onMarkerPositionChange, that));

            that.updateLatLngFields(latlng);
        });
    };

    businessProfile.prototype.handleGeocodeSearch = function() {
        var that = this;

        $( this.html.buttons.geocodeButtonId ).on('click', function( event ) {
            that.initGoogleMap();
            event.preventDefault();
        });
    };

    businessProfile.prototype.handleProfileSave = function() {
        var that = this;

        $( document ).on( 'submit' , this.html.forms.newProfileRequestFormId , function( event ) {
            that.formSubmitting = true;

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

            that.newProfileRequestFormHandler.doRequest( that.urls.saveBusinessProfile, data );

            event.preventDefault();
        });
    };

    businessProfile.prototype.handleLocaleChange = function() {
        var that = this;

        $( this.html.languageSelectorClass ).on( 'click', function( event ) {
            $( document).find( that.html.languageSelectorClass ).removeClass( 'selected' );
            $(this).addClass( 'selected' );

            var locale = $( that.html.languageSelectorClass + '.selected' ).data( 'locale' );
            var isLeave = that.beforeUnload();

            if ( isLeave || isLeave === undefined ) {
                that.isDirty = false;
                that.formSubmitting = false;
                that.currentLocale = locale;

                var businessProfileId = $( that.html.forms.newProfileRequestFormId ).data( 'id' );

                $.ajax({
                    url: Routing.generate( 'domain_business_profile_edit', {
                        id: businessProfileId
                    } ),
                    method: 'POST',
                    data: { 'locale': locale },
                    beforeSend: function() {
                        that.spinner.show( that.html.newProfileRequestSpinnerContainerId );
                    },
                    success: function( response ) {
                        $( that.html.forms.newProfileRequestFormId ).replaceWith( $( response ) );

                        new select();
                    },
                    error: function( jqXHR, textStatus, errorThrown ) {
                        alertify.error( errorThrown );
                    }
                });
            } else {
                $( document ).find( that.html.languageSelectorClass).not(this).addClass( 'selected' );
                $(this).removeClass( 'selected' );
            }

            event.preventDefault();
        } );
    };

    businessProfile.prototype.handleServiceAreaChange = function() {
        var that = this;

        var serviceAreasRadioName = this.freeProfileFormName + this.html.fields.serviceAreaRadioName;

        $( document ).on( 'change' , 'input[name="' + serviceAreasRadioName + '"]', function() {
            var $self = $(this);

            if ( $self.val() == that.serviceAreasAreaChoiceValue ) {
                $( that.html.fields.withinMilesOfMyBusinessFieldId ).removeAttr( 'disabled' );
                $( that.html.fields.localitiesFieldId ).attr('disabled', 'disabled');
            } else {
                $( that.html.fields.localitiesFieldId ).removeAttr( 'disabled' );
                $( that.html.fields.withinMilesOfMyBusinessFieldId ).attr( 'disabled', 'disabled' );
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

    //remove "error" highlighting
    businessProfile.prototype.disableFieldsHighlight = function( formId ) {
        var $form = $( formId );
        $form.find( 'input' ).removeClass('error');
        $form.find( '.form-group' ).removeClass('has-error');
        $form.find( '.help-block' ).html('');
    };

    //"error" fields highlighting
    businessProfile.prototype.enableFieldsHighlight = function( formId, errors, prefix ) {
        var $form = $( formId );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if ( typeof prefix === 'undefined' ) {
            prefix =  '#' + this.html.forms.closeBusinessProfileFormPrefix;
        }

        if ( typeof errors !== 'undefined' ) {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray( errors[field]) ) {
                    var $field = $( this.getFormFieldId( prefix, field ) );
                    $field.addClass( 'error' );

                    var $errorSection = $field.next( '.help-block' );

                    for (var key in errors[field]) {
                        $errorSection.append( errors[field][key] );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId( prefix, field ) );
                }
            }
        }
    };

    businessProfile.prototype.handleBusinessProfileClose = function () {
        var self = this;

        $( document ).on( 'click', this.html.buttons.closeBusinessProfileButtonId, function( event ) {

            var data = $( self.html.forms.closeBusinessProfileFormId ).serializeArray();
            data.push({
                'name': 'businessProfileId',
                'value': $(this).data('business-profile-id')
            });

            $.ajax({
                url: self.urls.closeBusinessProfileURL,
                method: 'POST',
                data: data,
                dataType: 'JSON',
                beforeSend: function() {
                    self.disableFieldsHighlight( self.html.forms.closeBusinessProfileFormId );
                    self.spinner.show( self.html.closeBusinessProfileLoadingSpinnerContainerId );
                },
                success: function( response ) {
                    if( response.success ) {
                        $( self.html.modals.closeBusinessProfileModalId ).modal('hide');
                        alertify.success( response.message );
                        $( self.html.forms.closeBusinessProfileFormId )[0].reset();
                    } else {
                        alertify.error( response.message );
                        self.enableFieldsHighlight( self.html.forms.closeBusinessProfileFormId, response.errors )
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    alertify.error( errorThrown );
                },
                complete: function() {
                    self.spinner.hide();
                }
            });

            event.preventDefault();
        });
    };

    //setup required "listeners"
    businessProfile.prototype.run = function() {
        this.handleGeocodeSearch();
        this.handleProfileSave();
        this.handleLocaleChange();
        this.handleServiceAreaChange();
        this.handleFormChange();
        this.handleBusinessProfileClose();

        var that = this;

        $( 'a[href="#businessAddress"]').on('shown.bs.tab', function(){
            that.initGoogleMap();
        } );

        new select();
    };

    return businessProfile;
});
