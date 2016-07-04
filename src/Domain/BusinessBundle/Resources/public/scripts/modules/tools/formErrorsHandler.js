define(['jquery', 'bootstrap'], function( $, bootstrap ) {
    'use strict';

    var formErrorsHandler = function ($form) {
        this.form = $form;
        this.visibleErrorsExists = false;
        this.invisibleErrosExists = false;
    };

    formErrorsHandler.prototype.resetErrorsCounter = function() {
        this.visibleErrorsExists = false;
        this.invisibleErrosExists = false;
    };

    formErrorsHandler.prototype.tabSwitchRequired = function() {
        return !this.visibleErrorsExists && this.invisibleErrosExists;
    };

    formErrorsHandler.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $formGroupElement = this.form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if (typeof prefix === 'undefined') {
            prefix =  '#' + this.form.attr('name');
        }

        if (typeof errors !== 'undefined') {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray(errors[field])) {
                    var $field = $(this.getFormFieldId( prefix, field ));

                    if ($field.is(':visible')) {
                        this.visibleErrorsExists = true;
                    } else {
                        this.invisibleErrosExists = true;
                    }

                    if ($field.hasClass('select-control')) {
                        var $container = $field.parents('.dropdown');
                        $container.css('border-color', 'red');
                        var $errorSection = $container.next('.help-block');
                    } else {
                        $field.addClass( 'error' );
                        var $errorSection = $field.next('.help-block');
                    }

                    for (var key in errors[field]) {
                        $errorSection.append(errors[field][key]);
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId(prefix, field) );
                }
            }
        }
    };

    //remove form errors (after click on submit button)
    formErrorsHandler.prototype.disableFieldsHighlight = function() {
        this.form.find( 'input' ).removeClass('error');
        this.form.find( '.form-group' ).removeClass('has-error');
        this.form.find( '.help-block' ).html('');
        this.form.find( '.dropdown' ).removeAttr('style');
        this.resetErrorsCounter();
    };

    //build form field id
    formErrorsHandler.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    return formErrorsHandler;
});
