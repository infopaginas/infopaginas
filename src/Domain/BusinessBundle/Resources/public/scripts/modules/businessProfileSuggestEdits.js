define( ['jquery', 'business/tools/form'], function( $,  FormHandler ) {
    'use strict';

    var businessProfileSuggestEdits = function() {
        var that = this;

        this.html = {
            buttons: {
                createSuggestEditsButtonId: '#createSuggestEditsButton',
            },
            forms: {
                createSuggestEditsFormId: '#createSuggestEditsForm',
                errorBlockSelector: '[data-form-error]'
            },
            section: {
                pageSectionSelector: 'section.page-section',
            },
            loadingSpinnerContainerId: 'create-suggest-edits-spinner-container',
            scrollBlock: 'html, body',
            pageHeader: 'header.header',
            errorBlockHtml: '<div class="error" data-form-error></div>'
        };


        this.createSuggestEditsFormHandler = new FormHandler({
            formId: this.html.forms.createSuggestEditsFormId,
            spinnerId: this.html.loadingSpinnerContainerId
        });

        this.createSuggestEditsFormHandler.addFormError = function ( message ) {
            var headerHeight = $( that.html.pageHeader ).height(),
                $message = $( that.html.errorBlockHtml );

            $message.text( message );

            $( that.html.scrollBlock ).animate({
                scrollTop: $message.offset().top - headerHeight
            }, 1000);

            $( that.html.section.pageSectionSelector ).prepend( $message );
        };

        this.run();
    };

    businessProfileSuggestEdits.prototype.handleSuggestEditsCreation = function() {
        var that = this;

        $( this.html.buttons.createSuggestEditsButtonId ).removeAttr( 'disabled' );

        $( document ).on( 'submit' , this.html.forms.createSuggestEditsFormId , function( event ) {
            that.formSubmitting = true;
            event.preventDefault();
            $( that.html.forms.errorBlockSelector ).remove();

            that.createSuggestEditsFormHandler.doRequest( $( this ).attr( 'action' ) );

            event.preventDefault();
        });
    };

    businessProfileSuggestEdits.prototype.run = function() {
        this.handleSuggestEditsCreation();
    };

    return businessProfileSuggestEdits;
});
