define(['jquery'], function( $ ) {
    'use strict';

    //init businessProfile object variables
    var businessProfileClose = function() {
        this.urls = {
            closeBusinessProfileURL: Routing.generate('domain_business_profile_close')
        };

        this.html = {
            buttons: {
                closeBusinessProfileButtonId: '#closeBusinessProfileButton'
            },
            forms: {
                closeBusinessProfileFormId: '#closeBusinessProfileForm',
                closeBusinessProfileFormPrefix: 'domain_business_bundle_business_close_request_type'
            },
            modals: {
                closeBusinessProfileModalId: '#closeBusinessProfileModal'
            }
        };

        this.run();
    };

    //build form field id
    businessProfileClose.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //remove "error" highlighting
    businessProfileClose.prototype.disableFieldsHighlight = function( formId ) {
        var $form = $( formId );
        $form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        $form.find( '.form-group' ).removeClass('has-error');

        $form.find( 'span[data-error-message]' ).remove();
    };

    //"error" fields highlighting
    businessProfileClose.prototype.enableFieldsHighlight = function( formId, errors, prefix ) {
        var $form = $( formId );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if ( typeof prefix === 'undefined' ) {
            prefix =  '#' + this.html.forms.closeBusinessProfileFormPrefix;
        }

        if ( typeof errors !== 'undefined' ) {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray( errors[field]) ) {
                    var $field = $( this.getFormFieldId( prefix, field ) );

                    $field.parent().addClass( 'field--not-valid' );

                    for (var key in errors[field]) {
                        $field.after( "<span data-error-message class='error'>" + errors[field][key] + "</span>" );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId( prefix, field ) );
                }
            }
        }
    };

    businessProfileClose.prototype.handleBusinessProfileClose = function () {
        var self = this;

        $( document ).on( 'click', this.html.buttons.closeBusinessProfileButtonId, function( event ) {

            var data = $( self.html.forms.closeBusinessProfileFormId ).serializeArray();
            data.push({
                'name': 'businessProfileId',
                'value': $(this).data('business-profile-id')
            });

            $.ajax({
                url: self.urls.closeBusinessProfileURL,
                method: 'POST',
                data: data,
                dataType: 'JSON',
                beforeSend: function() {
                    self.disableFieldsHighlight( self.html.forms.closeBusinessProfileFormId );
                },
                success: function( response ) {
                    if( response.success ) {
                        $( self.html.modals.closeBusinessProfileModalId ).modalFunc({close: true});
                        $( self.html.forms.closeBusinessProfileFormId )[0].reset();
                    } else {
                        if ( !$.isEmptyObject( response.errors ) ) {
                            self.enableFieldsHighlight( self.html.forms.closeBusinessProfileFormId, response.errors );
                        } else {
                            self.enableFieldsHighlight( self.html.forms.closeBusinessProfileFormId, { 'reason': [response.message] } );
                        }
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    this.enableFieldsHighlight( self.html.forms.closeBusinessProfileFormId, { 'reason': [errorThrown] } );
                },
                complete: function() {}
            });

            event.preventDefault();
        });
    };

    //setup required "listeners"
    businessProfileClose.prototype.run = function() {
        this.handleBusinessProfileClose();

        var that = this;
    };

    return businessProfileClose;
});
