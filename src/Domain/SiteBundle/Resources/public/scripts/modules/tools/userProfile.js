define(['jquery', 'bootstrap', 'alertify', 'tools/spin', 'tools/geolocation'], function( $, bootstrap, alertify, Spin, Geolocation ) {
    'use strict';

    //init userProfile object variables
    var userProfile = function() {
        this.urls = {
            saveProfile: Routing.generate('domain_site_user_profile_save'),
            savePassword: Routing.generate('domain_site_user_password_update')
        };

        this.modals = {
            passwordUpdateModalId: '#updatePasswordModal'
        };

        this.html = {
            buttons: {
                saveProfileButtonId: '#saveProfile',
                saveNewPasswordButtonId: '#savePassword'
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

            loadingSpinnerContainerClass: '.spinner-container'
        };

        this.spinner = new Spin();

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
                    $field.addClass( 'error' );

                    var $errorSection = $field.next('.help-block');

                    for (var key in errors[field]) {
                        $errorSection.append(errors[field][key]);
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
        $form.find( 'input' ).removeClass('error');
        $form.find( '.form-group' ).removeClass('has-error');
        $form.find( '.help-block' ).html('');
    };

    //actions before ajax send
    userProfile.prototype.beforeRequestHandler = function () {
        this.disableFieldsHighlight();

        var $form = $( this.getCurrentFormId() );

        var spinnerId = $form.find( this.html.loadingSpinnerContainerClass).attr('id');

        this.spinner.show( spinnerId );
    };

    //actions then ajax request done
    userProfile.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    userProfile.prototype.successHandler = function( response ) {
        if ( response.success ) {
            alertify.success( response.message );
            $( this.html.forms.passwordUpdateFormId )[0].reset();
            $( this.modals.passwordUpdateModalId ).modal( 'hide' );
            $( this.modals.passwordUpdateModalId ).modalFunc({close: true});
        } else {
            this.enableFieldsHighlight( response.errors );
            alertify.error( response.message );
        }
    };

    //actions on ajax failure
    userProfile.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        alertify.error( errorThrown );
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

    //setup required "listeners"
    userProfile.prototype.run = function() {
        this.handleProfileSaving();
        this.handlePasswordUpdate();
    };

    return userProfile;
});
