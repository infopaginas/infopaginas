define(['jquery', 'alertify', 'tools/spin'], function( $, alertify, Spin ) {
    'use strict'

    var login = function() {
        this.urls = {
            login_check: Routing.generate('fos_user_security_check'),
            home: Routing.generate('domain_site_home_index')
        };

        this.formId = '#loginForm';

        this.emailInputId = '#_username';
        this.passwordInputId = '#_password';

        this.spinner = new Spin();
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

    login.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();
        this.spinner.show( 'login-spin-container' );
    };

    login.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    login.prototype.successHandler = function( response ) {
        if( response.success ) {
            alertify.success( response.message );
            document.location.href = this.urls.home;
        } else {
            this.enableFieldsHighlight();
            alertify.error( response.message );
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
            beforeSend: $.proxy(this.beforeRequestHandler, this),
            complete: $.proxy(this.completeHandler, this),
            success: $.proxy(this.successHandler, this),
            error: $.proxy(this.errorHandler, this)
        });
    };

    login.prototype.handleLogin = function() {
        var $loginButton = $( '#loginButton' );
        var that = this;

        $loginButton.on( 'click', function( event ) {
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
