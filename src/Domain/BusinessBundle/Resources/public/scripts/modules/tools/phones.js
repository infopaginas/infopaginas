define(['jquery', 'bootstrap'], function( $, bootstrap ) {
    'use strict';

    var phones = function() {
        this.html = {
            containerListId: '#phone-fields-list',
            addLinkId: '#add-another-phone',
            removeLinkClass: '.remove-phone'
        };

        this.handleAdd();
        this.handleRemove();
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

            console.log($newWidget[0].outerHTML);

            phonesCount++;

            // create a new list element and add it to the list
            //var newLi = $('<li></li>').html($newWidget[0].outerHTML);

            $newWidget.appendTo(phonesList);

            phonesList.data('length', phonesCount);

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

    //auto-init
    new phones();
});