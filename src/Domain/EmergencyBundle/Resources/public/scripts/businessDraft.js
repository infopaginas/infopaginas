define(['jquery', 'bootstrap', 'business/tools/form', 'tools/spin', 'maskedInput', 'select2'], function( $, bootstrap, FormHandler, Spin ) {
    'use strict';

    var businessDraft = function() {
        this.urls = {
            saveBusiness: Routing.generate( 'emergency_business_draft_save' ),
            redirectUrl: Routing.generate( 'emergency_business_draft_create' )
        };

        this.businessFormName = 'domain_emergency_bundle_emergency_draft_business_type';

        this.html = {
            buttons: {

            },
            forms: {
                businessRequestFormId: '#businessRequestForm'
            },
            fields: {
                phoneId: '#' + this.businessFormName + '_phone',
                categoryId: '#' + this.businessFormName + '_category',
                customCategoryId: '#' + this.businessFormName + '_customCategory'
            }
        };

        this.businessRequestFormHandler = new FormHandler({
            formId: this.html.forms.businessRequestFormId,
            spinnerId: this.html.newProfileRequestSpinnerContainerId,
            redirectUrl: this.urls.redirectUrl
        });

        this.spinner = new Spin();

        this.run();
    };

    businessDraft.prototype.handleProfileSave = function() {
        var that = this;

        $( document ).on( 'submit' , this.html.forms.newProfileRequestFormId , function( event ) {
            event.preventDefault();

            that.businessRequestFormHandler.doRequest( that.urls.saveBusiness );

            event.preventDefault();
        });
    };

    businessDraft.prototype.categoryHandler = function() {
        var customCategory = $( this.html.fields.customCategoryId );
        var category       = $( this.html.fields.categoryId );

        category.on( 'change', function() {
            var data = $( this ).val();

            if ( data ) {
                customCategory.val( '' );
                customCategory.attr( 'disabled', 'disabled' );
            } else {
                customCategory.removeAttr( 'disabled' );
            }
        });
    };

    businessDraft.prototype.addMaskEvent = function() {
        var phone = $( this.html.fields.phoneId );

        phone.mask( '999-999-9999' );
        phone.bind( 'paste', function () {
            $( this ).val( '' );
        });
    };

    businessDraft.prototype.bindEvents = function() {
        this.addMaskEvent();
        this.categoryHandler();
    };

    businessDraft.prototype.run = function() {
        this.handleProfileSave();
        this.bindEvents();
    };

    return businessDraft;
});
