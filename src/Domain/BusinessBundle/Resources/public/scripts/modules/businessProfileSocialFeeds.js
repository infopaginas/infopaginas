define( [ 'jquery' ], function ( $ ) {
    'use strict';

    var socialFeeds = function () {
        this.html = {
            instagramFeedClass: '.instagram-feed',
            instagramPostsWrapperClass: '.posts-wrapper',

            instagramEmbedPostEndpoint: 'https://api.instagram.com/oembed/?url=http://instagr.am/p/',
            instagramEmbedPostParameters: '&omitscript=true',
            FBPostsWrapperClass: 'fb-page',
            FBPagePlaceholder: '#fb-page-placeholder',
            lazyloadElementClass: 'lazy',
            twitterPlaceholder: '#twitter-timeline-placeholder',
            twitterTimelineClass: 'twitter-timeline',
        };

        this.instagramURL = $( this.html.instagramFeedClass ).data( 'instagram-url' );

        this.run();
    };

    socialFeeds.prototype.insertInstagramPosts = function ( urlSting ) {
        if ( urlSting ) {
            var that = this;
            var url = new URL(urlSting);
            url.searchParams.append('__a', '1');
            $.get( url )
                .done( function ( data ) {
                        $.each( data.graphql.user.edge_owner_to_timeline_media.edges, function ( index, value ) {
                            $.get( that.html.instagramEmbedPostEndpoint + value.node.shortcode + that.html.instagramEmbedPostParameters )
                                .done( function ( data ) {
                                    $( that.html.instagramPostsWrapperClass ).append( data.html );
                                    instgrm.Embeds.process();
                                } )
                        } );
                } );
        }
    };

    socialFeeds.prototype.setFBWidth = function () {
        if ( typeof FB !== 'undefined' ) {
            $( '.' + this.html.FBPostsWrapperClass ).attr( 'data-width', $( '.social-feeds-column' ).width() )
                .removeClass( 'fb_iframe_widget fb_iframe_widget_fluid' );
            FB.XFBML.parse();
        }
    };

    socialFeeds.prototype.initFBPlugin = function () {
        $( this.html.FBPagePlaceholder ).addClass( this.html.FBPostsWrapperClass );
        this.setFBWidth();
        window.addEventListener( 'resize', $.proxy( this.setFBWidth, this ) );
    };

    socialFeeds.prototype.showFeedsIfVisible = function () {
        var instagramPlaceholder = $( this.html.instagramFeedClass + ' ' + this.html.instagramPostsWrapperClass );
        var facebookPlaceholder = $( this.html.FBPagePlaceholder );
        var twitterPlaceholder = $( this.html.twitterPlaceholder );

        this.showFacebookFeed( facebookPlaceholder );
        this.showInstagramFeed( instagramPlaceholder );
        this.showTwitterFeed( twitterPlaceholder );

        if ( $( '.' + this.html.lazyloadElementClass ).length === 0 ) {
            window.removeEventListener( 'scroll', this.showFeedsIfVisibleProxy );
            window.removeEventListener( 'resize', this.showFeedsIfVisibleProxy );
            window.removeEventListener( 'orientationChange', this.showFeedsIfVisibleProxy );
        }
    };

    socialFeeds.prototype.showFacebookFeed = function ( facebookPlaceholder ) {
        if ( facebookPlaceholder.hasClass( this.html.lazyloadElementClass ) && this.isScrolledIntoView( facebookPlaceholder ) ) {
            this.initFBPlugin();
            facebookPlaceholder.removeClass( this.html.lazyloadElementClass );
        }
    };

    socialFeeds.prototype.showInstagramFeed = function ( instagramPlaceholder ) {
        if ( instagramPlaceholder.hasClass( this.html.lazyloadElementClass ) && this.isScrolledIntoView( instagramPlaceholder ) ) {
            this.insertInstagramPosts( this.instagramURL );
            instagramPlaceholder.removeClass( this.html.lazyloadElementClass );
        }
    };

    socialFeeds.prototype.showTwitterFeed = function ( twitterPlaceholder ) {
        if ( twitterPlaceholder.hasClass( this.html.lazyloadElementClass ) && this.isScrolledIntoView( twitterPlaceholder ) ) {
            twitterPlaceholder.removeClass( this.html.lazyloadElementClass ).addClass( this.html.twitterTimelineClass );
            twttr.widgets.load();
        }
    };

    socialFeeds.prototype.isScrolledIntoView = function ( element, fullyInView ) {
        var pageTop = $( window ).scrollTop();
        var pageBottom = pageTop + $( window ).height();
        var elementTop = $( element ).offset().top;
        var elementBottom = elementTop + $( element ).height();

        if ( fullyInView === true ) {
            return ((pageTop < elementTop) && (pageBottom > elementBottom));
        } else {
            return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
        }
    };

    socialFeeds.prototype.run = function () {
        this.showFeedsIfVisibleProxy = $.proxy( this.showFeedsIfVisible, this );
        window.addEventListener( 'scroll', this.showFeedsIfVisibleProxy );
        window.addEventListener( 'resize', this.showFeedsIfVisibleProxy );
        window.addEventListener( 'orientationChange', this.showFeedsIfVisibleProxy );
    };

    return socialFeeds;
} );
