define(['jquery', 'select2', 'bootstrap'], function( $, select2, bootstrap ) {
    'use strict';

    var workingHours = function() {
        this.html = {
            containerListId: '#working-hours-fields-list',
            addLinkId: '#add-another-working-hours',
            removeLinkClass: '.remove-working-hours',
            day: '.working-hours-day',
            timeStart: '.working-hours-time-start',
            timeEnd: '.working-hours-time-end',
            openAllTime: 'input.working-hours-open-all-time'
        };

        this.handleAdd();
        this.handleRemove();
        this.handleSelect();
        this.handleOpenAllTime();
    };

    workingHours.prototype.handleAdd = function() {
        var that = this;

        $(document).on('click', that.html.addLinkId, function(event) {

            var workingHoursList = $( that.html.containerListId );

            var workingHoursCount = workingHoursList.data( 'length' );

            // grab the prototype template
            var newWidget = workingHoursList.attr( 'data-prototype' );

            var $newWidget = $( newWidget.replace( /__name__/g, workingHoursCount ) );

            $newWidget.find( '.help-block' ).addClass( 'working-hours-error-section-' + workingHoursCount );

            var formInput = $newWidget.find( '.form__field input' );

            formInput.focus(function(){
                $( this ).parent().addClass( "field-active" );
                $( this ).parent().find( 'label' ).addClass( "label-active" );
            });

            formInput.blur(function(){
                if( $( this ).val() === "" ) {
                    $( this ).parent().removeClass( "field-active field-filled" );
                    $( this).parent().find( 'label' ).removeClass( "label-active" );
                } else {
                    $( this ).parent().addClass( "field-filled" );
                }
            });

            workingHoursCount++;

            $newWidget.appendTo( workingHoursList );

            workingHoursList.data( 'length', workingHoursCount );

            that.handleSelect();
            that.handleNewInput();

            event.preventDefault();
        });
    };

    workingHours.prototype.handleRemove = function() {
        var that = this;

        $( document ).on( 'click', that.html.removeLinkClass, function(event) {
            var block = $( this ).parent( 'div' );
            block.remove();
            
            event.preventDefault();
        });
    };

    workingHours.prototype.handleSelect = function() {
        var timeSelects = $( this.html.timeStart + ', ' + this.html.timeEnd );

        timeSelects.find( 'select' ).select2();
        $( this.html.day ).select2();
    };

    workingHours.prototype.handleNewInput = function() {
        $( '.form input, .form textarea' ).each( function() {
            var $this;

            $this = $( this );
            if ( $this.prop( 'value' ).length !== 0 ) {
                $this.parent().addClass( 'field-active' );
            } else {
                $this.parent().removeClass( 'field-active field-filled' );
                $this.parent().find( 'label' ).removeClass( 'label-active' );
            }
        });
    };

    workingHours.prototype.handleOpenAllTime = function() {
        var that = this;
        var openAllTimeCheckboxes = $( that.html.openAllTime );

        $( document ).on( 'change', that.html.openAllTime, function( event ) {
            checkCollectionWorkingHours( this );
        });

        $.each( openAllTimeCheckboxes, function( index, openAllTimeCheckbox ) {
            checkCollectionWorkingHours( openAllTimeCheckbox );
        });

        function checkCollectionWorkingHours( openAllTimeCheckbox ) {
            var workingHourBlock = $( openAllTimeCheckbox ).parents( 'div.multi-field' ).first();

            var timeSelects = workingHourBlock.find( that.html.timeStart + ', ' + that.html.timeEnd );

            if ( $( openAllTimeCheckbox ).prop( 'checked' ) ) {
                timeSelects.find( 'select' ).val( 0 ).trigger( 'change' );

                timeSelects.find( 'select' ).select2( 'destroy' );
                timeSelects.find( 'select' ).prop( 'disabled', true );
                timeSelects.find( 'select' ).select2();

            } else {
                timeSelects.find( 'select' ).select2( 'destroy' );
                timeSelects.find( 'select' ).prop( 'disabled', false );
                timeSelects.find( 'select' ).select2();
            }
        }
    };

    //auto-init
    new workingHours();
});