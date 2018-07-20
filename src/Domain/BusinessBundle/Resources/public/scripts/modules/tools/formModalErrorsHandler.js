define(['jquery'], function( $ ) {
    'use strict';

    var formErrorsHandler = function ( $form ) {
        this.form = $form;
    };

    formErrorsHandler.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $formGroupElement = this.form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if ( typeof prefix === 'undefined' ) {
            prefix =  '#' + this.form.attr( 'name' );
        }

        if (typeof errors !== 'undefined') {
            for ( var field in errors ) {
                //check for "repeated" fields or embed forms
                if ( Array.isArray( errors[field] ) ) {
                    var $field;
                    var fieldId = this.getFormFieldId( prefix, field );

                    $field = $( fieldId );

                    $field.parent().addClass( 'field--not-valid' );

                    for( var key in errors[field] ) {
                        $field.after( "<span data-error-message class='error'>" + errors[field][key] + "</span>" );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId( prefix, field ) );
                }
            }
        }
    };

    //remove form errors (after click on submit button)
    formErrorsHandler.prototype.disableFieldsHighlight = function() {
        this.form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        this.form.find( '.form-group' ).removeClass('has-error');
        this.form.find( 'span[data-error-message]' ).remove();
    };

    //build form field id
    formErrorsHandler.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    return formErrorsHandler;
});
