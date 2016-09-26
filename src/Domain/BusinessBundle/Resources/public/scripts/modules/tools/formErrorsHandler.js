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
                if (Array.isArray(errors[field]) || field == 'phones') {
                    var fieldId = this.getFormFieldId( prefix, field );

                    if (Array.isArray(errors[field][0])) {
                        fieldId = fieldId + '_0';
                        errors[field] = errors[field][0];
                    }

                    //dirty workaround for phones field
                    if (fieldId == '#domain_business_bundle_business_profile_form_type_phones') {
                        fieldId = '#domain_business_bundle_business_profile_form_type_phones_0_phone';
                    }

                    var $field = $( fieldId );

                    $formGroupElement = $field.closest( '.form-group' );

                    if (!$formGroupElement.hasClass('has-error')) {
                        $formGroupElement.addClass('has-error');
                    }

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

                    for( var key in errors[field] ) {
                        if( errors[field][key]['phone'] !== undefined ) {
                            for( var index in errors[field][key]['phone'] ) {
                                var $errorSection = $('.phone-error-section-' + key);
                                $errorSection.append(errors[field][key]['phone'][index]);
                            }
                        } else {
                            $errorSection.append(errors[field][key]);
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
