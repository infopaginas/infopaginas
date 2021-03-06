define(['jquery'], function ( $ ) {
    'use strict';

    var ads = function () {
        this.init();

        return this;
    };

    var BANNER_BREAK_POINT_728x90 = 740;
    var BANNER_BREAK_POINT_468x60 = 480;
    var BANNER_BREAK_POINT_320x50 = 320;
    var BANNER_BREAK_POINT_300 = 300;

    var BASE_BREAK_POINT = 805;
    var RESPONSIVE_ADS_BLOCK_SIZE = 0.6;

    var BANNER_SIZE_728x90 = [728, 90];
    var BANNER_SIZE_468x60 = [468, 60];
    var BANNER_SIZE_320x50 = [320, 50];
    var BANNER_SIZE_300x250 = [300, 250];
    var BANNER_SIZE_300x600 = [300, 600];

    ads.prototype.init = function () {
        this.options = {
            html: {
                adsSettings: '#adsSettings',
                adsData: 'dfp-ads',
                searchTargeting:        'dfp-targeting-search',
                locationTargeting:      'dfp-targeting-location',
                categoriesTargeting:    'dfp-targeting-categories',
                slugTargeting:          'dfp-targeting-slug'
            },
            type: {
                resizable:          'resizable',
                resizableBlock:     'resizableBlock',
                resizableSideBlock: 'resizableSideBlock',
                default:            'default'
            }
        };

        var self = this;

        $( document ).ready(function() {
            self.initAds();
        });
    };

    ads.prototype.initAds = function () {
        this.options.adsSettings = $( this.options.html.adsSettings );

        if ( this.options.adsSettings.length ) {
            this.options.adsData = this.options.adsSettings.data( this.options.html.adsData );
            this.initAdsHeader();
            this.initAdsBody();
        }
    };

    ads.prototype.initAdsHeader = function () {
        var self = this;

        var searchTargeting     = this.options.adsSettings.data( self.options.html.searchTargeting );
        var locationTargeting   = this.options.adsSettings.data( self.options.html.locationTargeting );
        var categoriesTargeting = this.options.adsSettings.data( self.options.html.categoriesTargeting );
        var slugTargeting       = this.options.adsSettings.data( self.options.html.slugTargeting );

        googletag.cmd.push(function() {
            // init header
            var googleResponsiveCommonSize    = self.getResponsiveCommonSizeMapping( googletag );
            var googleResponsiveBlockSize     = self.getResponsiveBlockSizeMapping( googletag );
            var googleResponsiveSideBlockSize = self.getResponsiveSideBlockSizeMapping( googletag );

            $.each( self.options.adsData, function() {
                var item = this;

                if ( item && ((item.isMobile && window.innerWidth < BASE_BREAK_POINT) || !item.isMobile) ) {
                    var slot = googletag.defineSlot( item.slotId, item.sizes, item.htmlId );

                    if ( item.type == self.options.type.resizable ) {
                        slot.defineSizeMapping( googleResponsiveCommonSize );
                    } else if ( item.type == self.options.type.resizableBlock && googleResponsiveBlockSize ) {
                        slot.defineSizeMapping( googleResponsiveBlockSize );
                    } else if ( item.type == self.options.type.resizableSideBlock ) {
                        slot.defineSizeMapping( googleResponsiveSideBlockSize );
                    }

                    slot.addService( googletag.pubads() );
                }
            });

            // targeting
            if ( searchTargeting ) {
                googletag.pubads().setTargeting( 'search', searchTargeting );
            }

            if ( locationTargeting ) {
                googletag.pubads().setTargeting( 'location', locationTargeting );
            }

            if ( categoriesTargeting ) {
                googletag.pubads().setTargeting( 'categories', categoriesTargeting );
            }

            if ( slugTargeting ) {
                googletag.pubads().setTargeting( 'slug', slugTargeting );
            }

            googletag.pubads().collapseEmptyDivs( true );
            googletag.pubads().enableSingleRequest();
            googletag.enableServices();
        });
    };

    ads.prototype.initAdsBody = function () {
        var self = this;

        $.each( self.options.adsData, function() {
            var item = this;

            if ( item ) {
                googletag.cmd.push(function() {
                    googletag.display( item.htmlId );
                });
            }
        });
    };

    ads.prototype.getResponsiveCommonSizeMapping = function ( googletag ) {
        return googletag.sizeMapping().
            addSize( [0, 0], [] ).
            addSize( [BANNER_BREAK_POINT_320x50, 0], BANNER_SIZE_320x50 ). // Mobile
            addSize( [BANNER_BREAK_POINT_468x60, 0], BANNER_SIZE_468x60 ). // Tablet
            addSize( [BANNER_BREAK_POINT_728x90, 0], BANNER_SIZE_728x90 ). // Desktop
            build();
    };

    ads.prototype.getResponsiveBlockSizeMapping = function ( googletag ) {
        var googleResponsiveBlockSize;
        var windowWidth = window.innerWidth;

        var resizableWidth;

        if ( windowWidth >= BASE_BREAK_POINT ) {
            resizableWidth = windowWidth * RESPONSIVE_ADS_BLOCK_SIZE;
        } else {
            resizableWidth = windowWidth;
        }

        if ( resizableWidth >= BANNER_BREAK_POINT_728x90 ) {
            googleResponsiveBlockSize = googletag.sizeMapping().addSize( [0, 0], BANNER_SIZE_728x90 ).build();
        } else if ( resizableWidth >= BANNER_BREAK_POINT_468x60 ) {
            googleResponsiveBlockSize = googletag.sizeMapping().addSize( [0, 0], BANNER_SIZE_468x60 ).build();
        } else if (resizableWidth >= BANNER_BREAK_POINT_320x50) {
            googleResponsiveBlockSize = googletag.sizeMapping().addSize( [0, 0], BANNER_SIZE_320x50 ).build();
        } else {
            // screen width less than 320 is not supported
            googleResponsiveBlockSize = null;
        }

        return googleResponsiveBlockSize;
    };

    ads.prototype.getResponsiveSideBlockSizeMapping = function ( googletag ) {
        var googleResponsiveSideBlockSize = googletag.sizeMapping().addSize( [ 0, 0 ], [] );
        if ( window.innerWidth < BASE_BREAK_POINT ) {
            googleResponsiveSideBlockSize.addSize( [ BANNER_BREAK_POINT_300, 0 ], BANNER_SIZE_300x250 );
        } else {
            googleResponsiveSideBlockSize.addSize( [ BANNER_BREAK_POINT_300, 0 ], BANNER_SIZE_300x600 );
        }

        return googleResponsiveSideBlockSize.build();
    };

    return ads;
});
