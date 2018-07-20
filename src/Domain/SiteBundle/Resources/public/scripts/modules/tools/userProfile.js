define(['jquery', 'bootstrap', 'tools/spin', 'tools/geolocation', 'business/tools/businessProfileClose', 'business/tools/businessProfileUpgrade'], function( $, bootstrap, Spin, Geolocation, BusinessProfileClose, BusinessProfileUpgrade ) {
    'use strict';

    //init userProfile object variables
    var userProfile = function() {
        this.urls = {
            saveProfile: Routing.generate('domain_site_user_profile_save'),
            savePassword: Routing.generate('domain_site_user_password_update')
        };

        this.modals = {
            passwordUpdateModalId: '#updatePasswordModal',
            closeBusinessId: '#closeBusinessProfileModal',
            upgradeBusiness: '#upgradeBusinessProfileModal'
        };

        this.html = {
            buttons: {
                saveProfileButtonId: '#saveProfile',
                saveNewPasswordButtonId: '#savePassword',
                closeBusiness: '[data-close-business-id]',
                upgradeBusiness: '#upgradeBusiness'
            },
            forms: {
                profileFormName: 'domain_site_user_profile',
                profileFormId: '#userProfileForm',
                passwordUpdateFormName: 'domain_site_user_password_update',
                passwordUpdateFormId: '#passwordUpdateForm'
            },
            fields: {
                locationFieldId: '#domain_site_user_profile_location'
            },
            successBlock: '#success-block',
            loadingSpinnerContainerClass: '.spinner-container'
        };

        this.spinner = new Spin();
        this.businessProfileClose = new BusinessProfileClose;
        this.businessProfileUpgrate = new BusinessProfileUpgrade;

        this.geolocation = new Geolocation( {
            'locationBoxSelector' : this.html.fields.locationFieldId
        } );

        this.run();
    };

    userProfile.prototype.getCurrentFormId = function() {
        var $passwordUpdateModal = $( this.modals.passwordUpdateModalId );
        if( $passwordUpdateModal.hasClass( 'in' ) || $passwordUpdateModal.hasClass( 'modal--opened' ) ) {
            return this.html.forms.passwordUpdateFormId;
        }

        return this.html.forms.profileFormId;
    };

    //get serialized form data
    userProfile.prototype.getSerializedFormData = function() {
        return $( this.getCurrentFormId() ).serialize();
    };

    //build form field id
    userProfile.prototype.getFormFieldId = function( prefix, field ) {
        return prefix + '_' + field;
    };

    //"error" fields highlighting
    userProfile.prototype.enableFieldsHighlight = function( errors, prefix ) {
        var $form = $( this.getCurrentFormId() );
        var $formGroupElement = $form.find( '.form-group' );

        if (!$formGroupElement.hasClass('has-error')) {
            $formGroupElement.addClass('has-error');
        }

        if (typeof prefix === 'undefined') {
            prefix =  '#' + $form.attr('name');
        }

        if (typeof errors !== 'undefined') {
            for (var field in errors) {
                //check for "repeated" fields or embed forms
                if (Array.isArray(errors[field])) {
                    var $field = $(this.getFormFieldId( prefix, field ));

                    $field.parent().addClass( 'field--not-valid' );

                    for (var key in errors[field]) {
                        $field.after( "<span data-error-message class='error'>" + errors[field][key] + "</span>" );
                    }
                } else {
                    this.enableFieldsHighlight( errors[field], this.getFormFieldId(prefix, field) );
                }
            }
        }
    };

    //remove "error" highlighting
    userProfile.prototype.disableFieldsHighlight = function() {
        var $form = $( this.getCurrentFormId() );
        $form.find( 'input' ).parent().removeClass( 'field--not-valid' );
        $form.find( '.form-group' ).removeClass('has-error');

        $form.find( 'span[data-error-message]' ).remove();
    };

    //actions before ajax send
    userProfile.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();

        var $form = $( this.getCurrentFormId() );

        var spinnerId = $form.find( this.html.loadingSpinnerContainerClass).attr('id');

        this.spinner.show( spinnerId );

        $( this.html.successBlock ).find( 'strong' ).html( '' );
        $( this.html.successBlock ).hide();
    };

    //actions then ajax request done
    userProfile.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    userProfile.prototype.successHandler = function( response ) {
        if ( response.success ) {
            $( this.html.forms.passwordUpdateFormId )[0].reset();
            $( this.modals.passwordUpdateModalId ).modal( 'hide' );
            $( this.modals.passwordUpdateModalId ).find( 'div.form__field' ).removeClass( 'field-active' ).removeClass( 'field-filled' );
            $( this.modals.passwordUpdateModalId ).find( 'label.label-active' ).removeClass( 'label-active' );
            $( this.modals.passwordUpdateModalId ).modalFunc({close: true});

            $( this.html.successBlock ).find( 'strong' ).html( response.message );
            $( this.html.successBlock ).show();

            $( 'html, body' ).animate({ scrollTop: 0 }, 'fast');
        } else {
            if ( !$.isEmptyObject( response.errors ) ) {
                this.enableFieldsHighlight( response.errors );
            } else {
                this.enableFieldsHighlight( { 'oldPassword': [response.message] } );
                this.enableFieldsHighlight( { 'firstname': [response.message] } );
            }
        }
    };

    //actions on ajax failure
    userProfile.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        this.enableFieldsHighlight( { 'oldPassword': [errorThrown] } );
    };

    //ajax request
    userProfile.prototype.doRequest = function ( ajaxURL, data ) {
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

    //userProfile handling
    userProfile.prototype.handleProfileSaving = function() {
        var $saveButton = $( this.html.buttons.saveProfileButtonId );
        var that = this;

        $saveButton.on('click', function( event ) {
            var serializedData = that.getSerializedFormData();
            that.doRequest( that.urls.saveProfile, serializedData );

            event.preventDefault();
        });
    };

    userProfile.prototype.handlePasswordUpdate = function() {
        var $saveButton = $( this.html.buttons.saveNewPasswordButtonId );
        var $inputFields = $( this.html.forms.passwordUpdateFormId).find('input[type=password]');
        var that = this;

        $saveButton.on('click', function( event ) {
            var serializedData = that.getSerializedFormData();
            that.doRequest( that.urls.savePassword, serializedData );

            event.preventDefault();
        });
        $inputFields.keypress(function (e) {
            var key = e.which;
            if ( key == 13 ) {
                $saveButton.click();
                return false;
            }
        });
    };

    userProfile.prototype.addEvents = function() {
        var that = this;

        $( document ).on( 'click', this.html.buttons.closeBusiness, function ( event ) {
            event.preventDefault();

            var businessId = $( event.target ).data( 'close-business-id' );

            var closeButton = $( that.modals.closeBusinessId ).find( 'button[data-business-profile-id]' );

            closeButton.data( 'business-profile-id', businessId );

            $( that.modals.closeBusinessId ).modalFunc();
        });

        $( document ).on( 'click', this.html.buttons.upgradeBusiness, function ( event ) {
            event.preventDefault();

            $( that.modals.upgradeBusiness ).modalFunc();
        });

        $('.bps__table__data .bps__row').click(function () {
            $(this).toggleClass('open');
        })
    };

    //setup required "listeners"
    userProfile.prototype.run = function() {
        this.handleProfileSaving();
        this.handlePasswordUpdate();
        this.addEvents();
    };

    return userProfile;
});
