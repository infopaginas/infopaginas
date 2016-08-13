define( ['jquery', 'bootstrap', 'tools/select', 'slick', 'lightbox', 'tools/slider', 'tools/directions', 'tools/star-rating', 'alertify', 'tools/spin'], function( $, bootstrap, select, slick, lightbox, slider, directions, rating, alertify, Spin ) {
    'use strict';

    var businessProfileView = function() {
        this.html = {
            buttons: {
                createReviewButtonId: '#createReviewButton'
            },
            forms: {
                createReviewFormId: '#createReviewForm',
                createReviewFormPrefix: 'domain_business_bundle_business_review_type'
            },
            modals: {
                createReviewModalId: '#writeReviewModal'
            },
            loadingSpinnerContainerId: 'create-review-spinner-container'
        };

        this.urls = {
            createReviewURL: Routing.generate('domain_business_review_save')
        };

        this.spinner = new Spin();

        this.run();
    };

    //setup required "listeners"
    businessProfileView.prototype.run = function() {
        new select();
        new directions();

        this.handleReviewCreation();
    };

    //build form field id
    businessProfileView.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //"error" fields highlighting
    businessProfileView.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $form = $( this.html.forms.createReviewFormId );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if ( typeof prefix === 'undefined' ) {
            prefix =  '#' + this.html.forms.createReviewFormPrefix;
        }

        if ( typeof errors !== 'undefined' ) {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray( errors[field]) ) {
                    var $field = $( this.getFormFieldId( prefix, field ) );
                    $field.addClass( 'error' );

                    var $errorSection = $field.next( '.help-block' );

                    for (var key in errors[field]) {
                        $errorSection.append( errors[field][key] );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId( prefix, field ) );
                }
            }
        }
    };

    //remove "error" highlighting
    businessProfileView.prototype.disableFieldsHighlight = function() {
        var $form = $( this.html.forms.registrationFormId );
        $form.find( 'input' ).removeClass('error');
        $form.find( '.form-group' ).removeClass('has-error');
        $form.find( '.help-block' ).html('');
    };


    businessProfileView.prototype.handleReviewCreation = function() {
        var self = this;

        $(document).on( 'click', this.html.buttons.createReviewButtonId, function( event ) {

            var data = $( self.html.forms.createReviewFormId ).serializeArray();
            data.push({
                'name': 'businessProfileId',
                'value': $(this).data('business-profile-id')
            });

            $.ajax({
                url: self.urls.createReviewURL,
                method: 'POST',
                data: data,
                dataType: 'JSON',
                beforeSend: function() {
                    self.disableFieldsHighlight();
                    self.spinner.show( self.html.loadingSpinnerContainerId );
                },
                success: function( response ) {
                    if( response.success ) {
                        $( self.html.modals.createReviewModalId ).modal('hide');
                        alertify.success( response.message );
                    } else {
                        alertify.error( response.message );
                        self.enableFieldsHighlight( response.errors )
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    alertify.error( errorThrown );
                },
                complete: function() {
                    self.disableFieldsHighlight();
                    self.spinner.hide();
                    $( self.html.forms.createReviewFormId ).reset();
                }
            } );

            event.preventDefault();
        });
    };

    return businessProfileView;
});
