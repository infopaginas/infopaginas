define(['jquery', 'bootstrap', 'business/tools/form', 'tools/spin', 'tools/select', 'business/tools/businessProfileClose', 'tools/mapSpin', 'tools/mapboxgl', 'business/tools/phones', 'business/tools/workingHours', 'selectize'], function( $, bootstrap, FormHandler, Spin, select, businessProfileClose, MapSpin, mapboxgl ) {
    'use strict';

    //init businessProfile object variables
    var businessProfile = function() {
        this.urls = {
            saveBusinessProfile: Routing.generate( 'domain_business_profile_save' ),
            categoryAutoComplete: Routing.generate( 'domain_business_category_autocomplite' ),
            getLocalityByCoord: Routing.generate( 'domain_search_closest_locality_by_coord' ),
        };

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
                latitudeInputId: '#' + this.freeProfileFormName + '_latitude',
                longitudeInputId: '#' + this.freeProfileFormName + '_longitude',
                catalogLocalityId: '#' + this.freeProfileFormName + '_catalogLocality',
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
            imageValidationErrors: '#imageValidationErrors',
            videoValidationErrors: '#videoValidationErrors',
            requiredTagSelector: '[data-required-indicator]',
            requiredTag: '<span data-required-indicator>*</span>'
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

        this.businessProfileClose = new businessProfileClose;
        this.mapSpinner = new MapSpin( this.html.mapContainerId );

        this.spinner = new Spin();

        this.isDirty = false;
        this.formSubmitting = false;

        this.currentLocale = $( this.html.languageSelectorClass + '.selected' ).data('locale');

        this.run();
    };

    businessProfile.prototype.updateLocality = function (localityId) {
        $( this.html.fields.catalogLocalityId ).val( localityId ).trigger('change');
    };

    businessProfile.prototype.updateLatLngFields = function(lat, lng) {
        $( this.html.fields.latitudeInputId ).val( lat );
        $( this.html.fields.longitudeInputId ).val( lng );
    };

    businessProfile.prototype.onMarkerPositionChange = function(event) {
        var that = this;

        var latlng = this.marker.getLngLat();

        $.ajax({
            type: 'POST',
            url: this.urls.getLocalityByCoord,
            data: {'clt': latlng.lat, 'clg': latlng.lng},
            success: function(data){
                that.updateLocality(data['localityId']);
            }
        });

        that.updateLatLngFields( latlng.lat, latlng.lng );
        that.updateFieldSelectionFocus();

    };

    businessProfile.prototype.getGoogleMapObject = function(lat, lng)
    {
        var mapContainer = document.getElementById(this.html.mapContainerId);
        mapboxgl.accessToken = API_KEY;
        var map =  new mapboxgl.Map({
            container: mapContainer,
            style: 'mapbox://styles/mapbox/basic-v9',
            center: {
                lat: lat,
                lng: lng
            },
            zoom: 15,
            attributionControl: false
        });

        return map;
    };

    businessProfile.prototype.initGoogleMap = function() {
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
        map.addControl(new mapboxgl.NavigationControl(), 'bottom-right');
        map.addControl(new mapboxgl.FullscreenControl());

        var marker = new mapboxgl.Marker()
            .setLngLat([lng, lat])
            .setDraggable(true)
            .addTo(map);

        this.marker = marker;
        marker.on( 'dragend', $.proxy( that.onMarkerPositionChange, that ) );
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

                var Latlng = new mapboxgl.LatLng(lat(), lng());

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

    businessProfile.prototype.handleFormChange = function () {
        var self = this;

        $( document ).on( 'change' , '#businessProfileRequestForm', function() {
            self.isDirty = true;
        });
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

    businessProfile.prototype.addRequiredTag = function( element ) {
        if ( !element.find( this.html.requiredTagSelector ).length ) {
            element.append( this.html.requiredTag );
        }
    };

    businessProfile.prototype.removeRequiredTag = function( element ) {
        element.find( this.html.requiredTagSelector ).remove();
    };

    //setup required "listeners"
    businessProfile.prototype.run = function() {
        this.handleGeocodeSearch();
        this.moveMarker();
        this.handleProfileSave();
        this.handleFormChange();
        this.initAutoCompleteCategoriesField();
        this.initGoogleMap();

        $( this.html.buttons.fileUploadButton ).on( 'click', function() {
            $( this ).parent().find( 'input' ).click();
        });

        new select();
    };

    return businessProfile;
});
