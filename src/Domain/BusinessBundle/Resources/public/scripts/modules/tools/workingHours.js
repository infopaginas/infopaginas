define(['jquery', 'selectize', 'bootstrap', 'moment', 'dateTimePicker'], function( $, selectize, bootstrap ) {
    'use strict';

    var workingHours = function() {
        this.html = {
            containerListId: '#working-hours-fields-list',
            addLinkId: '#add-another-working-hours',
            removeLinkClass: '.remove-working-hours',
            days: 'select.working-hours-day:not(.selectized)',
            timeStart: '.working-hours-time-start',
            timeEnd: '.working-hours-time-end',
            openAllTime: 'input.working-hours-open-all-time'
        };

        this.handleAdd();
        this.handleRemove();
        this.handleSelect();
        this.handleOpenAllTime();
        this.addOneIfEmpty();
    };

    workingHours.prototype.handleAdd = function() {
        var that = this;

        $(document).on( 'click', that.html.addLinkId, function( event ) {
            that.add();
            event.preventDefault();
        });
    };

    workingHours.prototype.handleRemove = function() {
        var that = this;

        $( document ).on( 'click', that.html.removeLinkClass, function( event ) {
            $( this ).parent().remove();
            
            event.preventDefault();
        });
    };

    workingHours.prototype.handleSelect = function() {
        $( this.html.days ).selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: true
        });
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

            var timeInputs = workingHourBlock.find( that.html.timeStart + ', ' + that.html.timeEnd );

            if ( $( openAllTimeCheckbox ).prop( 'checked' ) ) {
                timeInputs.prop( 'disabled', true );
            } else {
                timeInputs.prop( 'disabled', false );
            }
        }
    };

    workingHours.prototype.handleDatepicker = function () {
        $( 'body' ).on( 'focus', '.working-hours-time-start', function() {
            $( this ).datetimepicker({
                format: 'hh:mm a',
                pickDate: false,
                pickSeconds: false,
                pick12HourFormat: false
            });
        });
    };

    workingHours.prototype.add = function () {
        var that = this,
            workingHoursList = $( that.html.containerListId ),
            workingHoursCount = workingHoursList.data( 'length' ),
            newWidget = workingHoursList.attr( 'data-prototype' ),
            $newWidget = $( newWidget.replace( /__name__/g, workingHoursCount ) ),
            formInput = $newWidget.find( '.form__field input' );

        $newWidget.find( '.help-block' ).addClass( 'working-hours-error-section-' + workingHoursCount );

        formInput.focus( function() {
            var parent = $( this ).parent();

            parent.addClass( 'field-active' );
            parent.find( 'label' ).addClass( 'label-active' );
        });

        formInput.blur( function() {
            var parent = $( this ).parent();

            if( $( this ).val() === '' ) {
                parent.removeClass( 'field-active field-filled' );
                parent.find( 'label' ).removeClass( 'label-active' );
            } else {
                parent.addClass( 'field-filled' );
            }
        });

        workingHoursCount++;

        $newWidget.appendTo( workingHoursList );

        workingHoursList.data( 'length', workingHoursCount );

        that.handleSelect();
        that.handleNewInput();
        that.handleDatepicker();
    };

    workingHours.prototype.addOneIfEmpty = function() {
        var that              = this,
            workingHoursList  = $( that.html.containerListId ),
            workingHoursCount = workingHoursList.data( 'length' );

        if (!workingHoursCount) {
            that.add()
        }
    };

    //auto-init
    new workingHours();
});