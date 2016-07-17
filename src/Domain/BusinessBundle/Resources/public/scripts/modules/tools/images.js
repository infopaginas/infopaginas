define(['jquery', 'bootstrap', 'alertify', 'tools/spin'], function( $, bootstrap, alertify, Spin ) {
    'use strict';

    //handle "media" business profile tab here
    var images = function() {
        this.html = {
            buttons: {
                fileInputId: 'domain_business_bundle_free_business_profile_form_type_files'
            },
            imageContainerClassname: 'image-wrap',
            imageEditFormClassname: 'image-edit-form',
            isPrimaryCheckboxClassname: 'is-primary',
            formsContainerId: 'forms-container',
            carouselContainerClassname: 'carousel-property',
            removeImageClassname: 'remove-image-link'
        };

        this.spinner = new Spin();

        this.spinnerContainerId = 'images-spin-container';

        //10Mb (per specification)
        this.maxAllowedFileSize = 10000000;

        //max business profile images count - 10
        this.maxAllowedFilesCount = 10;

        this.handleFileUploadInput();
        this.handleClickOnImages();
        this.handleClickOnIsPrimaryCheckbox();
        this.handleClickOnRemoveLink();
    };

    images.prototype.checkMaxAllowedFileSize = function( files ) {
        for( var i in files ) {
            var file = files[i];

            if( file.size > this.maxAllowedFileSize ) {
                alertify.error( 'Sorry. Maximum allowed filesize is 10Mb.' );
                return false;
            }
        }

        return true;
    };

    images.prototype.checkMaxAllowedFilesCount = function( files ) {
        var filesSelected = files.length;
        var filesAlreadyAdded = $( document ).find( '.' + this.html.imageContainerClassname ).length;

        var filesAdded = filesSelected + filesAlreadyAdded;

        if( filesAdded > this.maxAllowedFilesCount ) {
            alertify.error( 'Error: too much images added. Max files count = ' + this.maxAllowedFilesCount );
            return false;
        }

        return true;
    };

    images.prototype.beforeRequestHandler = function () {
        $( document ).find( '.' + this.html.imageEditFormClassname ).hide();
        this.spinner.show( this.spinnerContainerId );
    };

    images.prototype.completeHandler = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    images.prototype.onRequestSuccess = function( response ) {
        var $response = $( $.parseHTML( response ) );

        var $imagesContainer = $( '.' + this.html.carouselContainerClassname );
        var $formsContainer = $( '#' + this.html.formsContainerId );

        var $responseImages = $response.find( '.' + this.html.imageContainerClassname );

        $responseImages.each(function() {
            var $image = $(this);

            var imageId = $image.data( 'id' );

            var $form = $response.find( '#images-form-' + imageId );

            if( !$(document).find( '[data-id="' + imageId + '"]' ).length > 0 ) {
                $imagesContainer.append( $image );
                $formsContainer.append( $form );
            }
        });
    };

    //ajax request
    images.prototype.doRequest = function ( ajaxURL ) {
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
    images.prototype.getRequestData = function() {
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
    images.prototype.handleFileUploadInput = function() {
        var maxFilesCount = 10;

        var that = this;

        $(document).on('change', '#' + this.html.buttons.fileInputId, function() {
            var $this = $( this );

            var files = document.getElementById( that.html.buttons.fileInputId ).files;

            if( that.checkMaxAllowedFileSize( files ) == false ) {
                return false;
            }

            if( that.checkMaxAllowedFilesCount( files ) == false ) {
                return false;
            }

            that.doRequest( $this.parent().data( 'url' ) );
        });
    };

    //on image click - show "active" border & show image-edit form
    images.prototype.handleClickOnImages = function() {
        var $imageContainerClass = '.' + this.html.imageContainerClassname;
        var $imageFormContainerClass = '.' + this.html.imageEditFormClassname;

        $(document).on('click', $imageContainerClass, function() {
            var $self = $(this);

            $(document).find( $imageContainerClass ).removeClass( 'active' );
            $self.addClass( 'active' );

            $(document).find( $imageFormContainerClass ).hide();

            var imageId = $self.data( 'id' );

            $(document).find( '#images-form-' + imageId ).show();
        });
    };

    //only 1 image can be "primary" - remove is_primary from another
    images.prototype.handleClickOnIsPrimaryCheckbox = function() {
        var that = this;

        $(document).on( 'click', '.' + this.html.isPrimaryCheckboxClassname, function() {
            var $isPrimaryCheckboxes = $( '.' + that.html.isPrimaryCheckboxClassname ).not( this );
            $isPrimaryCheckboxes.removeAttr( 'checked' );
        } );
    };

    //remove image by click on "remove" link
    images.prototype.handleClickOnRemoveLink = function() {
        $(document).on( 'click', '.' + this.html.removeImageClassname, function( event ) {
            var imageId = $(this).data( 'id' );

            $(document).find( '[data-id="' + imageId + '"]' ).remove();
            $(document).find( '#images-form-' + imageId ).remove();

            event.preventDefault();
        } );
    };

    return images;
});
