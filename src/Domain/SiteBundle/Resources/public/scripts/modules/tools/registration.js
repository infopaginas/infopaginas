define(['jquery', 'tools/spin', 'tools/login'], function( $, Spin, Login ) {
    'use strict';

    //init registration object variables
    var registration = function() {
        this.urls = {
            registration: Routing.generate('domain_site_auth_registration')
        };

        this.modals = {
            registrationModalId: '#regModal',
            loginModalId: '#loginModal'
        };

        this.html = {
            buttons: {
                registrationButtonId: '#registrationButton'
            },
            forms: {
                registrationFormPrefix: 'registration',
                registrationFormId: '#registrationForm'
            },
            fields: {
                registrationLocationFieldId: 'location',
                homepageLocationFieldId: '#searchLocation'
            }
        };

        this.spinner = new Spin();
    };

    //get serialized form data
    registration.prototype.getSerializedFormData = function() {
        return $( this.html.forms.registrationFormId ).serialize();
    };

    //build form field id
    registration.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //"error" fields highlighting
    registration.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $form = $( this.html.forms.registrationFormId );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if (typeof prefix === 'undefined') {
            prefix =  '#' + this.html.forms.registrationFormPrefix;
        }

        if (typeof errors !== 'undefined') {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray(errors[field])) {
                    var $field = $(this.getFormFieldId( prefix, field ));

                    $field.parent().addClass( 'field--not-valid' );

                    for (var key in errors[field]) {
                        $field.after( "<span data-error-message class='error'>" + errors[field][key] + "</span>" );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId(prefix, field) );
                }
            }
        }
    };

    //remove "error" highlighting
    registration.prototype.disableFieldsHighlight = function() {
        var $form = $( this.html.forms.registrationFormId );
        $form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        $form.find( '.form-group' ).removeClass('has-error');

        $form.find( 'span[data-error-message]' ).remove();
    };

    //actions before ajax send
    registration.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();
        this.spinner.show( 'spin-container' );
    };

    //actions then ajax request done
    registration.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    registration.prototype.successHandler = function( response ) {
        if ( response.success ) {
            $( this.modals.registrationModalId ).modal( 'hide' );

            var email = this.getUriItem(this.registerData, 'email');
            var password = this.getUriItem(this.registerData, 'plainPassword');
            var token = this.getUriItem(this.registerData, '_token');
            var loginData = '_username=' + email + '&_password=' + password + '&_token=' + token;

            var login = new Login();
            login.doRequest( login.urls.login_check, loginData );

            $( this.html.forms.registrationFormId )[0].reset();
        } else {
            if ( !$.isEmptyObject( response.errors ) ) {
                this.enableFieldsHighlight( response.errors );
            } else {
                this.enableFieldsHighlight( { 'firstname': [response.message] } );
            }
        }
    };

    //actions on ajax failure
    registration.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        this.enableFieldsHighlight( { 'firstname': [errorThrown] } );
    };

    registration.prototype.getUriComponents = function ( data ) {
        return decodeURIComponent( data ).split( '&' );
    };

    registration.prototype.getUriItem = function ( data, search ) {
        var uriComponents = this.getUriComponents( data );
        var uriItemArray = _.filter( uriComponents, function ( item ) {
            var regexp = new RegExp(search, 'gi');
            return item.match(regexp)
        });

        return uriItemArray[0].split('=')[1];
    };

    //ajax request
    registration.prototype.doRequest = function ( ajaxURL, data ) {
        this.registerData = data;

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

    registration.prototype.submitRegistration = function ( event ) {
        var serializedData = this.getSerializedFormData();
        this.doRequest( this.urls.registration, serializedData );

        event.preventDefault();
    };

    //registration handling
    registration.prototype.handleRegistration = function() {
        var $registrationButton = $( this.html.buttons.registrationButtonId );
        var that = this;

        $( this.html.forms.registrationFormId ).keypress( function ( event ) {
            if ( (event.which && event.which == 13) || (event.keyCode && event.keyCode == 13) ) {
                that.submitRegistration( event );

                return false;
            }

            return true;
        });

        $registrationButton.on('click', function( event ) {
            that.submitRegistration( event );
        });
    };

    //fill location field (just copy value from homepage)
    registration.prototype.catchUserLocation = function() {
        var that = this;
        $( this.modals.registrationModalId ).on('show.bs.modal', function () {
            var locationFieldId = '#' + that.getFormFieldId(
                that.html.forms.registrationFormPrefix,
                that.html.fields.registrationLocationFieldId
            );
            $( locationFieldId ).val( $(that.html.fields.homepageLocationFieldId).val() );
        });
    };

    //setup required "listeners"
    registration.prototype.run = function() {
        this.catchUserLocation();
        this.handleRegistration();
    };

    //self-run
    $( function () {
        var controller = new registration();
        controller.run();
    });
});
