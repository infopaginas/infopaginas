define( [ 'jquery' ], function ( $ ) {
    'use strict';

    var socialFeeds = function () {
        this.html = {
            instagramFeedClass: '.instagram-feed',
            instagramPostsWrapperClass: '.posts-wrapper',

            instagramEmbedPostEndpoint: 'https://api.instagram.com/oembed/?url=http://instagr.am/p/',
            instagramEmbedPostParameters: '&omitscript=true'
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
            $( '.fb-page' ).attr( 'data-width', $( '.social-feeds-column' ).width() )
                .removeClass( 'fb_iframe_widget fb_iframe_widget_fluid' );
            FB.XFBML.parse();
        }
    };

    socialFeeds.prototype.run = function () {
        this.insertInstagramPosts( this.instagramURL );
        this.setFBWidth();
        window.addEventListener('resize', this.setFBWidth);
    };

    return socialFeeds;
} );
