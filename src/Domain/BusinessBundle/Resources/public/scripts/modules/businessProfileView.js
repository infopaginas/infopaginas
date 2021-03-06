define( ['jquery', 'bootstrap', 'business/tools/interactions', 'tools/select', 'slick', 'lightbox', 'tools/slider', 'tools/starRating', 'tools/spin', 'tools/redirect', 'tools/resetPassword',
    'tools/login', 'tools/registration', 'profile-redesign' ], function( $, bootstrap, interactionsTracker, select, slick, lightbox, slider, rating, Spin, Redirect ) {
    'use strict';

    var businessProfileView = function() {
        this.html = {
            buttons: {
                createReviewButtonId: '#createReviewButton',
                claimBusinessButtonId: '#claimBusinessButton',
                couponsClass: '.coupon'
            },
            forms: {
                createReviewFormId: '#createReviewForm',
                claimBusinessFormId: '#claimBusinessForm',
                createReviewFormPrefix: 'business_review',
                claimBusinessFormPrefix: '#business_claim_request'
            },
            modals: {
                createReviewModalId: '#writeReviewModal',
                claimBusinessModalId: '#claimBusinessModal',
                reportProblemModalId: '#reportProblemModal',
                popupModalId: '#popupModal'
            },
            loadingSpinnerContainerId: 'create-review-spinner-container',
            claimBusinessMessage: '#claimBusinessMessage',
            ratings: 'div.ratings'
        };

        this.urls = {
            createReviewURL: Routing.generate( 'domain_business_review_save' ),
            claimBusinessURL: Routing.generate( 'domain_business_claim' ),
            getRatingsURL: Routing.generate( 'domain_business_profile_get_ratings' )
        };

        this.spinner = new Spin();
        this.redirect = new Redirect;
        this.businessId = $('#businessProfileName').data('business-profile-id');

        this.run();
    };

    //setup required "listeners"
    businessProfileView.prototype.run = function() {
        new select();

        new interactionsTracker();

        this.initializeBusinessRatings();
        this.handleReviewCreation();
        this.handleBusinessClaim();
        this.handlePrintableCoupons();
        this.handlePopup();
    };

    businessProfileView.prototype.handlePopup = function() {
        var popupModal = $(this.html.modals.popupModalId);

        if ( popupModal.length ) {
            setTimeout(this.showPopup.bind(this), popupModal.data('time-to-appear') * 1000);
            popupModal.find('.hide-modal').on('click', function() {
                popupModal.removeClass('popup--opened');
            });
        }
    };

    businessProfileView.prototype.showPopup = function() {
        $(this.html.modals.popupModalId).addClass('popup--opened');
    };

    businessProfileView.prototype.initializeBusinessRatings = function() {
        if ( this.hasRatings() ) {
            var self = this;

            $.ajax( {
                url: this.urls.getRatingsURL,
                method: 'POST',
                data: { 'id': this.businessId },
                success: function( response ) {
                    self.showBusinessRatings( response );
                },
            } );
        }
    };

    businessProfileView.prototype.showBusinessRatings = function( data ) {
        $( this.html.ratings ).append( data );
    };

    businessProfileView.prototype.hasRatings = function() {
        return Boolean( $( this.html.ratings ).length );
    };

    //build form field id
    businessProfileView.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //"error" fields highlighting
    businessProfileView.prototype.enableFieldsHighlight = function( formId, errors, prefix ) {
        var $form = $( formId );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass( 'has-error' )) {
            $formGroupElement.addClass( 'has-error' );
        }

        if ( typeof prefix === 'undefined' ) {
            prefix =  '#' + this.html.forms.createReviewFormPrefix;
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

    //remove "error" highlighting
    businessProfileView.prototype.disableFieldsHighlight = function( formId ) {
        var $form = $( formId );
        $form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        $form.find( '.form-group' ).removeClass('has-error');

        $form.find( 'span[data-error-message]' ).remove();
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
                    self.disableFieldsHighlight( self.html.forms.createReviewFormId );
                    self.spinner.show( self.html.loadingSpinnerContainerId );
                },
                success: function( response ) {
                    if( response.success ) {
                        $( self.html.modals.createReviewModalId ).modal( 'hide' );
                        $( self.html.modals.createReviewModalId ).modalFunc({close: true});

                        $( self.html.forms.createReviewFormId ).find( '.star-rating .fa.fa-star-selected' ).each( function( idx, el ) {
                            return $( this ).removeClass( 'fa-star fa-star-selected' ).addClass( 'fa-star-o' );
                        });

                        $( self.html.forms.createReviewFormId )[0].reset();
                        $( self.html.modals.createReviewModalId ).find( 'div.form__field' ).removeClass( 'field-active' ).removeClass( 'field-filled' );
                        $( self.html.modals.createReviewModalId ).find( 'label.label-active' ).removeClass( 'label-active' );
                        $( self.html.modals.createReviewModalId ).modalFunc({close: true});
                    } else {
                        if ( !$.isEmptyObject( response.errors ) ) {
                            self.enableFieldsHighlight( self.html.forms.createReviewFormId, response.errors )
                        } else {
                            this.enableFieldsHighlight( { 'username': [errorThrown] } );
                        }
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    this.enableFieldsHighlight( { 'username': [errorThrown] } );
                },
                complete: function() {
                    self.spinner.hide();
                }
            } );

            event.preventDefault();
        });
    };

    businessProfileView.prototype.handleBusinessClaim = function() {
        var self = this;

        $( document ).on( 'click', this.html.buttons.claimBusinessButtonId, function( event ) {

            var data = $( self.html.forms.claimBusinessFormId ).serializeArray();
            data.push({
                'name': 'businessProfileId',
                'value': $( this ).data( 'business-profile-id' )
            });

            $.ajax({
                url: self.urls.claimBusinessURL,
                method: 'POST',
                data: data,
                dataType: 'JSON',
                beforeSend: function() {
                    $( self.html.claimBusinessMessage ).text( '' );
                    self.disableFieldsHighlight( self.html.forms.claimBusinessFormId );
                    self.spinner.show( self.html.loadingSpinnerContainerId );
                },
                success: function( response ) {
                    if( response.success ) {
                        $( self.html.claimBusinessMessage ).text( response.message );
                        $( self.html.buttons.claimBusinessButtonId ).remove();
                    } else {
                        if ( !$.isEmptyObject( response.errors ) ) {
                            self.enableFieldsHighlight( self.html.forms.claimBusinessFormId, response.errors, self.html.forms.claimBusinessFormPrefix )
                        } else {
                            self.enableFieldsHighlight( { 'message': [errorThrown] } );
                        }
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    self.enableFieldsHighlight( { 'message': [errorThrown] } );
                },
                complete: function() {
                    self.spinner.hide();
                }
            } );

            event.preventDefault();
        });
    };

    //print coupon by click on "print" link
    businessProfileView.prototype.handlePrintableCoupons = function() {
        $( document ).on( 'click', this.html.buttons.couponsClass, function( event ) {
            var imageURL = $( this ).attr( 'href' );
            var popup = window.open( imageURL );

            var closePrint = function() {
                if ( popup ) {
                    popup.close();
                }
            };

            popup.onbeforeunload = closePrint;
            popup.onafterprint = closePrint;
            popup.focus(); // Required for IE
            popup.print();

            event.preventDefault();
        } );
    };

    return businessProfileView;
});
