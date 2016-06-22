define(['jquery', 'alertify', 'tools/spin'], function( $, alertify, Spin ) {
    'use strict'

    var registration = function() {
        this.urls = {
            registration: Routing.generate('domain_site_auth_registration')
        };

        this.formFieldsPrefix = 'domain_site_registration';

        this.formId = '#registrationForm';

        this.registrationButtonId = '#registrationButton';

        this.registrationModalId = '#regModal';
        this.loginModalId = '#loginModal';

        this.homepageLocationFieldId = '#searchLocation';

        this.registrationLocationFieldId = 'location';

        this.spinner = new Spin();
    };

    registration.prototype.getSerializedFormData = function() {
        return $( this.formId ).serialize();
    };

    registration.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    registration.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $form = $( this.formId );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if (typeof prefix === 'undefined') {
            prefix =  '#' + this.formFieldsPrefix;
        }

        if (typeof errors !== 'undefined') {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray(errors[field])) {
                    var $field = $(this.getFormFieldId( prefix, field ));
                    $field.addClass( 'error' );

                    var $errorSection = $field.next('.help-block');

                    for (var key in errors[field]) {
                        $errorSection.append(errors[field][key]);
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId(prefix, field) );
                }
            }
        }
    };

    registration.prototype.disableFieldsHighlight = function() {
        var $form = $( this.formId );
        $form.find( 'input' ).removeClass('error');
        $form.find( '.form-group' ).removeClass('has-error');
        $form.find( '.help-block' ).html('');
    };

    registration.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();
        this.spinner.show( 'spin-container' );
    };

    registration.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    registration.prototype.successHandler = function( response ) {
        if ( response.success ) {
            alertify.success( response.message );
            $( this.registrationModalId ).modal( 'hide' );
            $( this.loginModalId ).modal( 'show' );
        } else {
            this.enableFieldsHighlight( response.errors );
            alertify.error( response.message );
        }
    };

    registration.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        alertify.error( errorThrown );
    };

    registration.prototype.doRequest = function ( ajaxURL, data ) {
        $.ajax({
            url: ajaxURL,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: $.proxy(this.beforeRequestHandler, this),
            complete: $.proxy(this.completeHandler, this),
            success: $.proxy(this.successHandler, this),
            error: $.proxy(this.errorHandler, this)
        });
    };

    registration.prototype.handleRegistration = function() {
        var $registrationButton = $( this.registrationButtonId );
        var that = this;

        $registrationButton.on('click', function( event ) {
            var serializedData = that.getSerializedFormData();
            that.doRequest( that.urls.registration, serializedData );

            event.preventDefault();
        });
    };

    //fill location field (just copy value from homepage)
    registration.prototype.catchUserLocation = function() {
        var that = this;
        $( this.registrationModalId ).on('show.bs.modal', function (event) {
            var locationFieldId = '#' + that.getFormFieldId( that.formFieldsPrefix , that.registrationLocationFieldId );
            $( locationFieldId ).val( $(that.homepageLocationFieldId).val() );
        });
    };

    registration.prototype.run = function() {
        this.catchUserLocation();
        this.handleRegistration();
    };

    $( function () {
        var controller = new registration();
        controller.run();
    });
});
