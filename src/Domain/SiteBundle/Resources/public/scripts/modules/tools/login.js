define(['jquery', 'alertify'], function( $, alertify ) {
    'use strict'

    var login = function() {
        this.urls = {
            login_check: Routing.generate('fos_user_security_check'),
            home: Routing.generate('domain_site_home_index')
        };

        this.formId = '#loginForm';

        this.emailInputId = '#_username';
        this.passwordInputId = '#_password';
    };

    login.prototype.enableFieldsHighlight = function() {
        $( this.emailInputId ).addClass( 'error' );
        $( this.passwordInputId ).addClass( 'error' );
    };

    login.prototype.disableFieldsHighlight = function() {
        $( this.emailInputId ).removeClass( 'error' );
        $( this.passwordInputId ).removeClass( 'error' );
    };

    login.prototype.getSerializedFormData = function() {
        return $( this.formId ).serialize();
    };

    login.prototype.successHandler = function( response ) {
        if( !response.success ) {
            this.enableFieldsHighlight();
            alertify.error( response.message );
        } else {
            document.location.href( this.urls.home );
        }
    };

    login.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        this.enableFieldsHighlight();
        alertify.error( errorThrown );
    };

    login.prototype.doRequest = function ( ajaxURL, data ) {
        $.ajax({
            url: ajaxURL,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success: $.proxy(this.successHandler, this),
            error: $.proxy(this.errorHandler, this)
        });
    };

    login.prototype.handleLogin = function() {
        var $loginButton = $( '#loginButton' );
        var that = this;

        $loginButton.on( 'click', function( event ) {
            that.disableFieldsHighlight();

            var serializedData = that.getSerializedFormData();
            that.doRequest( that.urls.login_check, serializedData );

            event.preventDefault();
        } );
    };

    login.prototype.run = function() {
        this.handleLogin();
    };

    $( function () {
        var controller = new login();
        controller.run();
    });
});
