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
        var $formGroupElement;

        if (typeof prefix === 'undefined') {
            prefix =  '#' + this.form.attr('name');
        }

        if (typeof errors !== 'undefined') {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray(errors[field])) {
                    var $field;
                    var fieldId = this.getFormFieldId( prefix, field );

                    if ( field == 'phones' || field == 'collectionWorkingHours' ) {

                        for (var phoneField in errors[field]) {
                            var phoneFieldBaseId = fieldId + '_' + phoneField;

                            for (var phoneFieldItem in errors[field][phoneField]) {
                                var phoneFieldId = phoneFieldBaseId + '_' + phoneFieldItem;

                                $field = $( phoneFieldId );

                                $field.parent().addClass( 'field--not-valid' );

                                if ($field.is(':visible')) {
                                    this.visibleErrorsExists = true;
                                } else {
                                    this.invisibleErrosExists = true;
                                }

                                $field.parent().append( "<span data-error-message class='error'>" + errors[field][phoneField][phoneFieldItem] + "</span>" );
                            }
                        }
                    } else {
                        $field = $( fieldId );

                        $field.parent().addClass( 'field--not-valid' );

                        if ($field.is(':visible')) {
                            this.visibleErrorsExists = true;
                        } else {
                            this.invisibleErrosExists = true;
                        }

                        for( var key in errors[field] ) {
                            $field.parent().append( "<span data-error-message class='error'>" + errors[field][key] + "</span>" );
                        }
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId(prefix, field) );
                }
            }
        }
    };

    //remove form errors (after click on submit button)
    formErrorsHandler.prototype.disableFieldsHighlight = function() {
        this.form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        this.form.find( '.form-group' ).removeClass('has-error');
        this.form.find( 'span[data-error-message]' ).remove();

        this.resetErrorsCounter();
    };

    //build form field id
    formErrorsHandler.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    return formErrorsHandler;
});
