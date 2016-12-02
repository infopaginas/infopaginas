define(['jquery', 'tools/spin', 'jquery-ui'], function( $, Spin ) {
    'use strict';

    //init resetPassword object variables
    var resetPassword = function() {
        this.urls = {
            reset_password_request: Routing.generate('domain_site_auth_reset_password_request'),
            reset_password: Routing.generate('domain_site_auth_reset_password')
        };

        this.modals = {
            resetModalId: '#resetPasswordModal',
            resetRequesModalId: '#forgottenPasswordModal',
            loginModalId: '#loginModal'
        };

        this.html = {
            forms: {
                resetPasswordRequestFormId: '#forgottenPasswordForm',
                resetPasswordFormId: '#resetPasswordForm'
            },
            fields: {
                emailInputId: '#domain_site_reset_password_request_email'
            },
            buttons: {
                resetPasswordRequestButtonId: '#resetPasswordRequestButton',
                resetPasswordButtonId: '#resetPasswordButton'
            },
            resetPasswordRequestSpinContainerId: 'resetPasswordRequestSpinContainer',
            resetPasswordSpinContainerId: 'resetPasswordSpinContainer',

            loadingSpinnerContainerClass: '.spinner-container'
        };

        this.spinner = new Spin();
        this.run();
    };

    //build form field id
    resetPassword.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //highlight form errors (if required)
    resetPassword.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $modal = this.getActiveModal();

        var $form = $modal.find('form');
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if (typeof prefix === 'undefined') {
            prefix =  '#' + $form.attr('name');
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

    //remove form errors (after click on submit button)
    resetPassword.prototype.disableFieldsHighlight = function() {
        var $modal = this.getActiveModal();
        var $form = $modal.find( 'form' );
        $form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        $form.find( '.form-group' ).removeClass('has-error');

        $form.find( 'span[data-error-message]' ).remove();
    };

    //serialize form data
    resetPassword.prototype.getSerializedFormData = function(formId) {
        return $( formId ).serializeArray();
    };

    //return object of current active modal window
    resetPassword.prototype.getActiveModal = function() {
        return $( '#' + $('.modal.in').attr('id') );
    };

    //action before ajax send
    resetPassword.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();

        var spinnerId = this.getActiveModal().find( this.html.loadingSpinnerContainerClass).attr('id');

        this.spinner.show( spinnerId );
    };

    //actions then ajax request done
    resetPassword.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    resetPassword.prototype.successHandler = function( response ) {
        if( response.success ) {

            //if current form == reset password form
            var activeModal = '#' + this.getActiveModal().find('form').attr('id');

            if ( activeModal == this.html.forms.resetPasswordFormId ) {
                $( this.modals.resetModalId ).modal( 'hide' );
                $( this.modals.loginModalId ).modal( 'show' );
            } else if ( activeModal == this.html.forms.resetPasswordRequestFormId ) {
                $( this.modals.resetRequesModalId ).modal( 'hide' );
                $( this.modals.loginModalId ).modal( 'show' );
            }
        } else {
            if ( !$.isEmptyObject( response.errors ) ) {
                this.enableFieldsHighlight( response.errors );
            } else {
                this.enableFieldsHighlight( { 'plainPassword': [response.message] } );
            }
        }
    };

    //actions on ajax failure
    resetPassword.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        this.enableFieldsHighlight( { 'plainPassword': [errorThrown] } );
    };

    //ajax request
    resetPassword.prototype.doRequest = function ( ajaxURL, data ) {
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

    //handle 'reset request' form
    resetPassword.prototype.handlePasswordRequestForm = function() {
        var $requestButton = $( this.html.buttons.resetPasswordRequestButtonId );
        var that = this;

        $requestButton.on( 'click', function(event) {
            var serializedData = that.getSerializedFormData( that.html.forms.resetPasswordRequestFormId );
            that.doRequest( that.urls.reset_password_request, serializedData );

            event.preventDefault();
        });
    };

    //handle 'reset' form
    resetPassword.prototype.handleResetPasswordForm = function() {
        var $requestButton = $( this.html.buttons.resetPasswordButtonId );
        var that = this;

        $requestButton.on( 'click', function(event) {
            var serializedData = that.getSerializedFormData( that.html.forms.resetPasswordFormId );
            serializedData.push({name: 'token', value: that.getResetToken()});

            that.doRequest( that.urls.reset_password, serializedData );

            event.preventDefault();
        });
    };

    //get reset token from URL path
    resetPassword.prototype.getResetToken = function() {
        if( window.location.pathname.indexOf('password_reset') !== -1 ) {
            var paths =  window.location.pathname.split('/');
            var tokenPath = paths.indexOf('password_reset') + 1;

            return paths[tokenPath];
        }

        return '';
    };

    //check token existance in URL. If exists - show 'reset password' modal
    resetPassword.prototype.checkPasswordResetToken = function() {
        if( window.location.pathname.indexOf('password_reset') !== -1 ) {
            $( this.modals.resetModalId ).modal( 'show' );
        }
    };

    //setup required "listeners"
    resetPassword.prototype.run = function() {
        this.handlePasswordRequestForm();
        this.handleResetPasswordForm();
        this.checkPasswordResetToken();
    };

    return resetPassword;
});
