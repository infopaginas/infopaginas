define(['jquery', 'business/tools/modalForm'], function( $, FormHandler ) {
    'use strict';

    //init businessProfile object variables
    var businessProfileClose = function() {
        this.urls = {
            closeBusinessProfileURL: Routing.generate( 'domain_business_profile_close' ),
            userProfilePage:         Routing.generate( 'domain_site_user_profile' )
        };

        this.html = {
            buttons: {
                closeBusinessProfileButtonId: '#closeBusinessProfileButton'
            },
            forms: {
                closeBusinessProfileFormId: '#closeBusinessProfileForm'
            },
            modals: {
                closeBusinessProfileModalId: '#closeBusinessProfileModal'
            }
        };

        this.profileCloseFormHandler = new FormHandler({
            formId: this.html.forms.closeBusinessProfileFormId,
            modalId: this.html.modals.closeBusinessProfileModalId
        });

        this.run();
    };

    businessProfileClose.prototype.handleBusinessProfileClose = function () {
        var self = this;

        $( document ).on( 'click', this.html.buttons.closeBusinessProfileButtonId, function( event ) {

            var data = $( self.html.forms.closeBusinessProfileFormId ).serializeArray();
            data.push({
                'name': 'businessProfileId',
                'value': $( this ).data( 'business-profile-id' )
            });

            self.profileCloseFormHandler.doRequest( self.urls.closeBusinessProfileURL, data );

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
