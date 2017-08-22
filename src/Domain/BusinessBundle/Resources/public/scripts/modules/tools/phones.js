define(['jquery', 'bootstrap', 'select2', 'maskedInput'], function( $, bootstrap, select2, mask ) {
    'use strict';

    var phones = function() {
        this.html = {
            containerListId: '#phone-fields-list',
            addLinkId: '#add-another-phone',
            removeLinkClass: '.remove-phone',
            type: '.business-phone-type',
            phoneInputs: 'input[ id $= "_phone" ]'
        };

        this.handleAdd();
        this.handleRemove();
        this.handleSelect();
        this.addMaskEvent();
    };

    phones.prototype.handleAdd = function() {
        var that = this;

        $(document).on('click', that.html.addLinkId, function(event) {

            var phonesList = $( that.html.containerListId );

            var phonesCount = phonesList.data('length');

            // grab the prototype template
            var newWidget = phonesList.attr('data-prototype');

            var $newWidget = $(newWidget.replace(/__name__/g, phonesCount));

            $newWidget.find( '.help-block' ).addClass( 'phone-error-section-' + phonesCount );

            var formInput = $newWidget.find( '.form__field input' );

            formInput.focus(function(){
                $(this).parent().addClass("field-active");
                $(this).parent().find('label').addClass("label-active");
            });

            formInput.blur(function(){
                if($(this).val() === "") {
                    $(this).parent().removeClass("field-active field-filled");
                    $(this).parent().find('label').removeClass("label-active");
                } else {
                    $(this).parent().addClass("field-filled");
                }
            });

            phonesCount++;

            $newWidget.appendTo(phonesList);

            phonesList.data('length', phonesCount);

            that.handleSelect();
            that.addMaskEvent();
            event.preventDefault();
        });
    };

    phones.prototype.handleRemove = function() {
        var that = this;

        $(document).on('click', that.html.removeLinkClass, function(event) {
            var block = $(this).parent( 'div' );
            block.remove();
            
            event.preventDefault();
        });
    };

    phones.prototype.handleSelect = function() {
        $( this.html.type ).select2();
    };

    phones.prototype.addMaskEvent = function() {
        var phones = $( this.html.phoneInputs );

        phones.mask( '999-999-9999' );
        phones.bind( 'paste', function () {
            $( this ).val( '' );
        });
    };

    //auto-init
    new phones();
});