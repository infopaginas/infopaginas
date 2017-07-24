define(['jquery', 'tools/spin'], function( $, Spin ) {
    'use strict';

    var login = function() {
        this.urls = {
            login_check: Routing.generate('fos_user_security_check'),
            home: Routing.generate('domain_site_user_profile')
        };

        this.html = {
            forms: {
                loginFormId: '#loginForm'
            },
            fields: {
                emailInputId: '#_username',
                passwordInputId: '#_password'
            }
        };

        this.spinner = new Spin();
    };

    login.prototype.enableFieldsHighlight = function( message ) {
        $( this.html.fields.emailInputId ).parent().addClass( 'field--not-valid' );
        $( this.html.fields.passwordInputId ).parent().addClass( 'field--not-valid' );

        $( this.html.fields.emailInputId ).after( "<span data-error-message class='error'>" + message + "</span>" );
    };

    login.prototype.disableFieldsHighlight = function() {
        $( this.html.fields.emailInputId ).parent().removeClass( 'field--not-valid' );
        $( this.html.fields.passwordInputId ).parent().removeClass( 'field--not-valid' );

        $( this.html.forms.loginFormId ).find( 'span[data-error-message]' ).remove();
    };

    login.prototype.getSerializedFormData = function() {
        return $( this.html.forms.loginFormId ).serialize();
    };

    login.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();
        this.spinner.show( 'login-spin-container' );
    };

    login.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    login.prototype.successHandler = function( response ) {
        if( response.success ) {

            if ( response.redirect ) {
                document.location.href = response.redirect;
            } else {
                document.location.href = this.urls.home;
            }
        } else {
            this.enableFieldsHighlight( response.message );
        }
    };

    login.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        this.enableFieldsHighlight( errorThrown );
    };

    login.prototype.doRequest = function ( ajaxURL, data ) {
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

    login.prototype.submitLogin = function ( event ) {
        var serializedData = this.getSerializedFormData();
        this.doRequest( this.urls.login_check, serializedData );

        event.preventDefault();
    };

    login.prototype.handleLogin = function() {
        var $loginButton = $( '#loginButton' );
        var that = this;

        $( this.html.forms.loginFormId ).keypress( function ( event ) {
            if ( (event.which && event.which == 13) || (event.keyCode && event.keyCode == 13) ) {
                that.submitLogin( event );

                return false;
            }

            return true;
        });

        $loginButton.on( 'click', function( event ) {
            that.submitLogin( event );
        } );
    };

    login.prototype.run = function() {
        this.handleLogin();
    };

    $( function () {
        var controller = new login();
        controller.run();
    });

    return login;
});
