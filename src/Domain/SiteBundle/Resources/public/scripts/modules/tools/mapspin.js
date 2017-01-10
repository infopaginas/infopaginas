define(['jquery', 'spin'], function( $, _spin ) {
    'use strict';

    var spin = function ( options ) {
        this.init(options);
        return this;
    };

    spin.prototype.init = function ( container, options ) {
        this.options = {
            lines: 17,
            length: 54,
            width: 7,
            radius: 30,
            scale: 0.5,
            corners: 1,
            color: '#000',
            opacity: 0.1,
            rotate: 3 ,
            direction: 1,
            speed: 0.7,
            trail: 42,
            fps: 20,
            zIndex: 2e9,
            className: 'spinner',
            top: '39%',
            left: '50%',
            shadow: false,
            hwaccel: false,
            position: 'absolute'
        };
        $.extend( this.options, options );

        this.spinnerOn = false;

        if ( typeof map !== 'undefined' ) {
            this.spinner = new _spin(this.options);
            this.container = container;
            this.bindEvents();
        }
    };

    spin.prototype.show = function(container) {
        var target = document.getElementById(container);
        this.spinner = this.spinner.spin(target);
    };

    spin.prototype.hide = function() {
        this.spinner.stop();
    };

    spin.prototype.bindEvents = function() {
        var self = this;

        self.spinnerOn = true;
        self.show( this.container );

        google.maps.event.addListener( map, 'bounds_changed', function() {
            self.mapLoadingStart();
        });

        google.maps.event.addListener( map, 'dragstart', function() {
            self.mapLoadingStart();
        });

        google.maps.event.addListener( map, 'maptypeid_changed', function() {
            self.mapLoadingStart();
        });

        google.maps.event.addListener( map, 'idle', function() {
            self.mapLoadingEnd();
        });

        google.maps.event.addListener( map, 'tilesloaded', function() {
            self.mapLoadingEnd();
        });
    };

    spin.prototype.mapLoadingStart = function() {
        if (!this.spinnerOn) {
            this.show( this.container );
            this.spinnerOn = true;
        }
    };

    spin.prototype.mapLoadingEnd = function() {
        this.hide( this.container );
        this.spinnerOn = false;
    };

    return spin;
});