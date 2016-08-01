define(['jquery', 'bootstrap', 'alertify', 'tools/spin'], function( $, bootstrap, alertify, Spin ) {
    'use strict';

    //handle "media" business profile tab here
    var videos = function() {
        this.html = {
            buttons: {
                fileInputId: 'domain_business_bundle_business_profile_form_type_video'
            },
            videoContainerId: '#video'
        };

        this.spinner = new Spin();

        this.spinnerContainerId = 'images-spin-container';

        //max business profile videos count - 1
        this.maxAllowedFilesCount = 1;

        this.handleFileUploadInput();
        this.handleClickOnRemoveLink();
    };

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

    videos.prototype.beforeRequestHandler = function () {
        this.spinner.show( this.spinnerContainerId );
    };

    videos.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    videos.prototype.onRequestSuccess = function( response ) {
        $(this.html.videoContainerId).html(response);
    };

    //ajax request
    videos.prototype.doRequest = function ( ajaxURL ) {
        $.ajax({
            url: ajaxURL,
            type: 'POST',
            data: this.getRequestData(),
            enctype: 'multipart/form-data',
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: $.proxy( this.beforeRequestHandler, this ),
            complete: $.proxy( this.completeHandler, this ),
            success: $.proxy( this.onRequestSuccess, this )
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

    //remove image by click on "remove" link
    videos.prototype.handleClickOnRemoveLink = function() {
        /*$(document).on( 'click', '.' + this.html.removeImageClassname, function( event ) {
            var imageId = $(this).data( 'id' );

            $(document).find( '[data-id="' + imageId + '"]' ).remove();
            $(document).find( '#images-form-' + imageId ).remove();

            event.preventDefault();
        } );*/
    };

    return videos;
});
