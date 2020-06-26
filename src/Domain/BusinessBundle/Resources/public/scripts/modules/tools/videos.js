define(['jquery', 'bootstrap', 'tools/spin'], function( $, bootstrap, Spin ) {
    'use strict';

    //handle "media" business profile tab here
    var videos = function() {
        this.html = {
            buttons: {
                fileInputId: 'business_profile_form_videoFile',
                startUploadRemoteFileButtonId: 'start-remote-video-upload'
            },
            videoContainerId: '#video',
            removeVideoLinkId: '#remove-video',
            remoteVideoURLInputId: '#remote-video-url',
            videoRowContainer: 'div.media__item.video-item',
            videoTitleFields: '#video input[ id *= "_title"]',
            videoDescriptionFields: '#video textarea[ id *= "_description"]'
        };

        this.urls = {
            uploadByURL: Routing.generate('domain_business_remote_videos_upload')
        };

        this.spinner = new Spin();

        this.spinnerContainerId = 'videos-spin-container';

        //max business profile videos count - 1
        this.maxAllowedFilesCount = 1;

        this.maxAllowedFileSize = 128000000;

        this.handleFileUploadInput();
        this.handleClickOnRemoveLink();
        this.handleRemoteVideosUpload();
        this.handleVideoValidationError();
    };

    //only 1 video allowed
    videos.prototype.checkMaxAllowedFilesCount = function( filesSelected ) {
        var filesAlreadyAdded = $( document ).find( this.html.videoRowContainer ).length;

        var filesAdded = filesSelected + filesAlreadyAdded;

        if( filesAdded > this.maxAllowedFilesCount ) {
            var error = $( this.html.remoteVideoURLInputId ).data( 'error-count-limit' );

            this.videoErrorHandler( error );
            return false;
        }

        return true;
    };

    //max allowed filesize: 128mb
    videos.prototype.checkMaxAllowedFileSize = function( files ) {
        var filesInput = document.getElementById( this.html.buttons.fileInputId );
        var file = filesInput.files[0];

        if( file.size > this.maxAllowedFileSize ) {
            var error = $( this.html.remoteVideoURLInputId ).data( 'error-size-limit' );

            this.videoErrorHandler( error );
            return false;
        }

        return true;
    };

    //show loader spinner
    videos.prototype.beforeRequestHandler = function () {
        this.spinner.show( this.spinnerContainerId );
        this.removeVideoErrors();

        $( '#' + this.html.buttons.startUploadRemoteFileButtonId ).attr( 'disabled', 'disabled' );
        $( '#' + this.html.buttons.fileInputId ).attr( 'disabled', 'disabled' );
    };

    //hide loader spinner on complete
    videos.prototype.completeHandler = function() {
        this.spinner.hide();
        $( this.html.remoteVideoURLInputId ).val( '' );

        $( '#' + this.html.buttons.startUploadRemoteFileButtonId ).removeAttr( 'disabled' );
        $( '#' + this.html.buttons.fileInputId ).removeAttr( 'disabled' );
    };

    //actions on ajax success
    videos.prototype.onRequestSuccess = function( response ) {
        if( response.success ) {
            $( this.html.videoContainerId ).html( response.message );
            this.updateFieldSelectionFocus();
        } else {
            this.videoErrorHandler( response.message );
        }
    };

    videos.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {
        var messageError;

        if ( jqXHR.responseJSON ) {
            messageError = jqXHR.responseJSON.message;
        } else {
            messageError = errorThrown;
        }

        this.videoErrorHandler( messageError );
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

    //get video from form
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

            that.removeVideoErrors();

            if( that.checkMaxAllowedFilesCount( files.length ) == false ) {
                return false;
            }

            if( that.checkMaxAllowedFileSize( files ) == false ) {
                return false;
            }

            that.doRequest( $this.parent().find( 'button.file-upload-button' ).data( 'url' ) );
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

            if ( !$remoteVideoURLInput.val() ) {
                var error = $( that.html.remoteVideoURLInputId ).data( 'error-empty' );

                that.removeVideoErrors();
                that.videoErrorHandler( error );
            } else {
                that.removeVideoErrors();

                if( that.checkMaxAllowedFilesCount( 1 ) == false ) {
                    return false;
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
            }
        } );
    };

    //remove video by click on "remove" link
    videos.prototype.handleClickOnRemoveLink = function() {
        var videoContainerId = this.html.videoContainerId;

        $(document).on( 'click', this.html.removeVideoLinkId, function( event ) {
            $( document ).find( videoContainerId ).html( '' );
            event.preventDefault();
        } );
    };

    videos.prototype.videoErrorHandler = function( error ) {
        var $remoteVideoURLInput = $( this.html.remoteVideoURLInputId );

        $remoteVideoURLInput.parent().addClass( 'field--not-valid' );
        $remoteVideoURLInput.after( "<span data-error-message class='error'>" + error + "</span>" );

        return false;
    };

    videos.prototype.removeVideoErrors = function() {
        var $remoteVideoURLInput = $( this.html.remoteVideoURLInputId );

        $remoteVideoURLInput.parent().removeClass( 'field--not-valid' );
        $remoteVideoURLInput.parent().find( 'span[data-error-message]' ).remove();

        return false;
    };

    videos.prototype.updateFieldSelectionFocus = function () {
        $( '.form input, .form textarea' ).each(function() {
            var $this;

            $this = $( this );
            if ($this.prop( 'value' ).length !== 0){
                $this.parent().addClass( 'field-active' );
            } else {
                $this.parent().removeClass( 'field-active field-filled' );
                $this.parent().find( 'label' ).removeClass( 'label-active' );
            }
        });
    };

    videos.prototype.handleVideoValidationError = function() {
        $( document ).on( 'change', this.html.videoDescriptionFields + ',' + this.html.videoTitleFields, function() {
            var description = $( this );

            description.parent().removeClass( 'field--not-valid' );
            description.parent().find( 'span[data-error-message]' ).remove();
        });
    };

    return videos;
});
