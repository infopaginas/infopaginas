define(['jquery', 'bootstrap', 'alertify', 'tools/spin'], function( $, bootstrap, alertify, Spin ) {
    'use strict';

    //handle "media" business profile tab here
    var videos = function() {
        this.html = {
            buttons: {
                fileInputId: 'domain_business_bundle_business_profile_form_type_videoFile',
                startUploadRemoteFileButtonId: 'start-remote-video-upload'
            },
            videoContainerId: '#video',
            removeVideoLinkId: '#remove-video',
            remoteVideoURLInputId: '#remote-video-url'
        };

        this.urls = {
            uploadByURL: Routing.generate('domain_business_remote_videos_upload')
        };

        this.spinner = new Spin();

        this.spinnerContainerId = 'videos-spin-container';

        //max business profile videos count - 1
        this.maxAllowedFilesCount = 1;

        this.handleFileUploadInput();
        this.handleClickOnRemoveLink();
        this.handleRemoteVideosUpload();
    };

    //only 1 video allowed
    videos.prototype.checkMaxAllowedFilesCount = function( files ) {
        var filesSelected = files.length;
        var filesAlreadyAdded = $( document ).find( '.' + this.html.imageContainerClassname ).length;

        var filesAdded = filesSelected + filesAlreadyAdded;

        if( filesAdded > this.maxAllowedFilesCount ) {
            alertify.error( 'Error: too much images added. Max files count = ' + this.maxAllowedFilesCount );
            return false;
        }

        return true;
    };

    //show loader spinner
    videos.prototype.beforeRequestHandler = function () {
        this.spinner.show( this.spinnerContainerId );
    };

    //hide loader spinner on complete
    videos.prototype.completeHandler = function() {
        this.spinner.hide();
        $( this.html.remoteVideoURLInputId ).val( '' );
    };

    //actions on ajax success
    videos.prototype.onRequestSuccess = function( response ) {
        if( response.success ) {
            $( this.html.videoContainerId ).html( response.message );
        } else {
            var $remoteVideoURLInput = $( this.html.remoteVideoURLInputId );

            $remoteVideoURLInput.addClass('error');
            alertify.error( response.message );
        }
    };

    videos.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        var $remoteVideoURLInput = $( this.html.remoteVideoURLInputId );

        $remoteVideoURLInput.addClass('error');
        alertify.error( errorThrown );
    };

    //ajax request
    videos.prototype.doRequest = function ( ajaxURL ) {
        $.ajax({
            url: ajaxURL,
            type: 'POST',
            data: this.getRequestData(),
            dataType: 'JSON',
            enctype: 'multipart/form-data',
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: $.proxy( this.beforeRequestHandler, this ),
            complete: $.proxy( this.completeHandler, this ),
            success: $.proxy( this.onRequestSuccess, this ),
            error: $.proxy( this.errorHandler, this )
        });
    };

    //get images from form
    videos.prototype.getRequestData = function() {
        var formData = new FormData;

        var filesInput = document.getElementById( this.html.buttons.fileInputId );
        var files = filesInput.files;

        for( var i in files ) {
            formData.append( 'files[]', files[i] );
        }

        var businessProfileId = $( '#' + this.html.buttons.fileInputId ).parents( 'form' ).data( 'id' );
        formData.append( 'businessProfileId', businessProfileId );

        return formData;
    };

    //check allowed filesize/count. Start upload process
    videos.prototype.handleFileUploadInput = function() {
        var maxFilesCount = 10;

        var that = this;

        $(document).on('change', '#' + this.html.buttons.fileInputId, function() {
            var $this = $( this );

            var files = document.getElementById( that.html.buttons.fileInputId ).files;

            if( that.checkMaxAllowedFilesCount( files ) == false ) {
                return false;
            }

            that.doRequest( $this.parent().data( 'url' ) );
        });

        //reset input
        $(document).on('click', '#' + this.html.buttons.fileInputId, function() {
            $(this).val(null);
        });
    };

    //it should be possible to upload video from 3rd-party services
    videos.prototype.handleRemoteVideosUpload = function() {
        var $remoteVideoURLInput = $( this.html.remoteVideoURLInputId );

        var that = this;

        $( document ).on( 'click', '#' + this.html.buttons.startUploadRemoteFileButtonId, function( event ) {
            var businessProfileId = $( '#' + that.html.buttons.fileInputId ).parents( 'form' ).data( 'id' );

            if ( $remoteVideoURLInput.hasClass( 'error' ) ) {
                $remoteVideoURLInput.removeClass( 'error' );
            }

            var data = {
                url: $remoteVideoURLInput.val(),
                businessProfileId: businessProfileId
            };

            $.ajax( {
                url: that.urls.uploadByURL,
                type: 'POST',
                data: data,
                dataType: 'JSON',
                beforeSend: $.proxy( that.beforeRequestHandler, that ),
                complete: $.proxy( that.completeHandler, that ),
                success: $.proxy( that.onRequestSuccess, that ),
                error: $.proxy( that.errorHandler, that )
            } );

            event.preventDefault();
        } );
    };

    //remove image by click on "remove" link
    videos.prototype.handleClickOnRemoveLink = function() {
        var videoContainerId = this.html.videoContainerId;

        $(document).on( 'click', this.html.removeVideoLinkId, function( event ) {
            $( document ).find( videoContainerId ).html( '' );
            event.preventDefault();
        } );
    };

    return videos;
});
