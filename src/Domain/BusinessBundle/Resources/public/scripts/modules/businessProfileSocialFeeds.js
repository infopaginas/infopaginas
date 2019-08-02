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

    socialFeeds.prototype.insertInstagramPosts = function ( url ) {
        if ( url ) {
            var that = this;

            $.get( url + '?__a=1' )
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
            $( '.fb-page' ).attr( 'data-width', $( '.social-feeds-column' ).width() );
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
