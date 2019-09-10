define( [ 'jquery', 'videojs', 'video_share_js' ], function ( $, videoJS ) {
    window.videojs = videoJS;

    var medias = Array.prototype.slice.apply( document.querySelectorAll( 'video' ) );

    medias.forEach( function ( media ) {
        media.addEventListener( 'play', function ( event ) {
            medias.forEach( function ( media ) {
                if ( event.target != media ) {
                    media.pause();
                }
            } );
        } );
    } );

    $( '.video-js' ).each( function ( index, element ) {
        var video = videojs( element.id );
        video.volume( 0.5 );

        video.socialShare( {
            facebook: {},
            twitter: {}
        } );
    } );
} );