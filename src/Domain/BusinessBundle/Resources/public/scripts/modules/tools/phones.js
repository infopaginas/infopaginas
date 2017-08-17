define(['jquery', 'bootstrap', 'select2'], function( $, bootstrap, select2 ) {
    'use strict';

    var phones = function() {
        this.html = {
            containerListId: '#phone-fields-list',
            addLinkId: '#add-another-phone',
            removeLinkClass: '.remove-phone',
            type: '.business-phone-type'
        };

        this.handleAdd();
        this.handleRemove();
        this.handleSelect();
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

    //auto-init
    new phones();
});