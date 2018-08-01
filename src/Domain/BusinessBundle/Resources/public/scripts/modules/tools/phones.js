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
        this.addOneIfEmpty();
    };

    phones.prototype.handleAdd = function() {
        var that = this;

        $(document).on('click', that.html.addLinkId, function(event) {
            that.add();
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

    phones.prototype.add = function() {
        var that = this,
            phonesList  = $( that.html.containerListId ),
            phonesCount = phonesList.data( 'length' ),
            newWidget   = phonesList.attr( 'data-prototype' ),
            $newWidget  = $( newWidget.replace( /__name__/g, phonesCount ) ),
            formInput   = $newWidget.find( '.form__field input' );

        $newWidget.find( '.help-block' ).addClass( 'phone-error-section-' + phonesCount );

        formInput.focus(function(){
            var $parent = $( this ).parent();

            $parent.addClass( "field-active" );
            $parent.find( 'label' ).addClass( "label-active" );
        });

        formInput.blur(function(){
            var $parent = $( this ).parent();

            if($( this ).val() === "") {
                $parent.removeClass( "field-active field-filled") ;
                $parent.find( 'label' ).removeClass( "label-active" );
            } else {
                $parent.addClass( "field-filled" );
            }
        });

        phonesCount++;

        $newWidget.appendTo( phonesList );

        phonesList.data( 'length', phonesCount );

        that.handleSelect();
        that.addMaskEvent();
    };

    phones.prototype.addOneIfEmpty = function() {
        var that        = this,
            phonesList  = $( that.html.containerListId ),
            phonesCount = phonesList.data('length');

        if (!phonesCount) {
            that.add()
        }
    };

    //auto-init
    new phones();
});