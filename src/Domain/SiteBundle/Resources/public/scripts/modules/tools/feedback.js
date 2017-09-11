define(['jquery', 'tools/spin', 'maskedInput'], function( $, Spin, mask ) {
    'use strict';

    var feedback = function() {
        this.urls = {
            feedback: Routing.generate('domain_page_feedback')
        };

        this.html = {
            buttons: {
                feedbackButtonId: '#feedbackButton'
            },
            forms: {
                feedbackFormPrefix: 'domain_page_bundle_feedback_form_type',
                feedbackFormId: '#feedbackForm'
            },
            inputs: {
                phoneInputs: 'input.phone-mask'
            },
            block: {
                message: '.feedback-message'
            }
        };

        this.spinner = new Spin({
            position: 'relative'
        });
        this.init();
    };

    feedback.prototype.init = function() {
        this.addFeedbackListeners();
        this.addPhoneMaskEvent();
    };

    //get serialized form data
    feedback.prototype.getSerializedFormData = function() {
        return $( this.html.forms.feedbackFormId ).serialize();
    };

    //build form field id
    feedback.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //"error" fields highlighting
    feedback.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $form = $( this.html.forms.feedbackFormId );
        var $formGroupElement = $form.find( '.form-group' );

        if ( !$formGroupElement.hasClass( 'has-error' ) ) {
            $formGroupElement.addClass( 'has-error' );
        }

        if ( typeof prefix === 'undefined' ) {
            prefix =  '#' + this.html.forms.feedbackFormPrefix;
        }

        if ( typeof errors !== 'undefined' ) {
            for ( var field in errors ) {
                //check for "repeated" fields or embed forms
                if ( Array.isArray( errors[field] ) ) {
                    var $field = $( this.getFormFieldId( prefix, field ) );

                    $field.parent().addClass( 'field--not-valid' );

                    for ( var key in errors[field] ) {
                        $field.after( '<span data-error-message class="error">' + errors[field][key] + '</span>' );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId( prefix, field ) );
                }
            }
        }
    };

    //remove "error" highlighting
    feedback.prototype.disableFieldsHighlight = function() {
        var $form = $( this.html.forms.feedbackFormId );

        $form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        $form.find( '.form-group' ).removeClass('has-error');

        $form.find( 'span[data-error-message]' ).remove();
    };

    //actions before ajax send
    feedback.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();
        this.spinner.show( 'feedbackForm' );
        $( this.html.block.message ).html( '' );
    };

    //actions then ajax request done
    feedback.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    feedback.prototype.successHandler = function( response ) {
        if ( response.success ) {
            $( this.html.block.message ).html( response.message );
            $( this.html.forms.feedbackFormId )[0].reset();
        } else {
            if ( !$.isEmptyObject( response.errors ) ) {
                this.enableFieldsHighlight( response.errors );
            } else {
                this.enableFieldsHighlight( { 'fullName': [response.message] } );
            }
        }
    };

    //actions on ajax failure
    feedback.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        this.enableFieldsHighlight( { 'fullName': [errorThrown] } );
    };

    //ajax request
    feedback.prototype.doRequest = function ( ajaxURL, data ) {
        $.ajax({
            url:        ajaxURL,
            type:       'POST',
            dataType:   'JSON',
            data:       data,
            beforeSend: $.proxy( this.beforeRequestHandler, this ),
            complete:   $.proxy( this.completeHandler, this ),
            success:    $.proxy( this.successHandler, this ),
            error:      $.proxy( this.errorHandler, this )
        });
    };

    feedback.prototype.submitFeedback = function ( event ) {
        var serializedData = this.getSerializedFormData();
        this.doRequest( this.urls.feedback, serializedData );

        event.preventDefault();
    };

    feedback.prototype.addFeedbackListeners = function() {
        var feedbackButton = $( this.html.buttons.feedbackButtonId );
        var that = this;

        $( this.html.forms.feedbackFormId ).keypress( function ( event ) {
            if ( (event.which && event.which == 13) || (event.keyCode && event.keyCode == 13) ) {
                that.submitFeedback( event );

                return false;
            }

            return true;
        });

        feedbackButton.on( 'click', function( event ) {
            that.submitFeedback( event );
        });
    };

    feedback.prototype.addPhoneMaskEvent = function() {
        var phones = $( this.html.inputs.phoneInputs );

        phones.mask( '999-999-9999' );
        phones.bind( 'paste', function () {
            $( this ).val( '' );
        });
    };

    return feedback;
});
