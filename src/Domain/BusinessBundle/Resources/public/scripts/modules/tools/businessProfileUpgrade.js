define(['jquery', 'business/tools/modalForm'], function( $, FormHandler ) {
    'use strict';

    //init businessProfile object variables
    var businessProfileUpgrade = function() {
        this.urls = {
            upgradeBusinessProfileURL: Routing.generate( 'domain_site_user_profile_upgrade' ),
            userProfilePage:           Routing.generate( 'domain_site_user_profile' )
        };

        this.html = {
            buttons: {
                upgradeBusinessProfileButtonId: '#upgradeBusinessProfileButton'
            },
            forms: {
                upgradeBusinessProfileFormId: '#upgradeBusinessProfileForm'
            },
            modals: {
                upgradeBusinessProfileModalId: '#upgradeBusinessProfileModal'
            }
        };

        this.profileUpgradeFormHandler = new FormHandler({
            formId: this.html.forms.upgradeBusinessProfileFormId,
            modalId: this.html.modals.upgradeBusinessProfileModalId
        });

        this.run();
    };

    businessProfileUpgrade.prototype.handleBusinessProfileUpgrade = function () {
        var self = this;

        $( document ).on( 'click', this.html.buttons.upgradeBusinessProfileButtonId, function( event ) {

            var data = $( self.html.forms.upgradeBusinessProfileFormId ).serializeArray();

            self.profileUpgradeFormHandler.doRequest( self.urls.upgradeBusinessProfileURL, data );

            event.preventDefault();
        });
    };

    //setup required "listeners"
    businessProfileUpgrade.prototype.run = function() {
        this.handleBusinessProfileUpgrade();

        var that = this;
    };

    return businessProfileUpgrade;
});
