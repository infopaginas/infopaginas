define(['jquery', 'bootstrap', 'tools/spin', 'tools/select'], function( $, bootstrap, Spin, select ) {
    'use strict';

    //handle "media" business profile tab here
    var images = function() {
        this.html = {
            buttons: {
                fileInputId:                   'domain_business_bundle_business_profile_form_type_files',
                startUploadRemoteFileButtonId: 'start-remote-image-upload'
            },
            imageContainerClassname:     'image-wrap',
            imageEditFormClassname:      'image-edit-form',
            isPrimaryCheckboxClassname:  'is-primary',
            galleryContainerId:          'gallery',
            imageRowClassName:           'image-row',
            removeImageClassname:        'remove-image-link',
            remoteImageURLInputId:       '#remote-image-url',
            uploadImageInputId:          '#error-image-message',
            imageTypeSelectClassname:    '.select-image-type',
            imageRowContainer:           'div.media__item.image-item',
            imageRowLogoContainer:       'div.media__item.image-item.image-form-logo',
            imageRowBackgroundContainer: 'div.media__item.image-item.image-form-background',
            fileContextSelect:           '#media-select-image-type',
            imageDescriptionFields:      '#gallery textarea[ id *= "_description"]'
        };

        this.urls = {
            uploadByURL: Routing.generate( 'domain_business_remote_images_upload' )
        };

        this.spinner = new Spin();

        this.spinnerContainerId = 'images-spin-container';

        //10Mb (per specification)
        this.maxAllowedFileSize = 10000000;

        //1Mb
        this.maxAllowedBackgroundFileSize = 1000000;
        this.imageContextBackground = 'business_profile_background';

        //max business profile images count - 25
        this.maxAllowedFilesCount = 25;
        // single logo and background
        this.maxAllowedMediaItemCount = 1;

        // removed images to disable its re-enable after upload
        this.removedItems = [];

        this.handleFileUploadInput();
        this.handleClickOnImages();
        this.handleClickOnIsPrimaryCheckbox();
        this.handleClickOnRemoveLink();
        this.handleRemoteImageUpload();
        this.handleImageTypeChange();
        this.handleImageValidationError();
    };

    //max allowed filesize: 10mb
    images.prototype.checkMaxAllowedFileSize = function( files ) {
        var fileContextValue = $( this.html.fileContextSelect ).val();
        var errorMessage;

        if ( fileContextValue == this.imageContextBackground ) {
            errorMessage = 'error-background-size-limit';
        } else {
            errorMessage = 'error-size-limit';
        }

        for ( var i in files ) {
            var file = files[i];

            if ( file.size && !(this.checkCommonFileSize( file.size, fileContextValue )) ) {
                var error = $( this.html.uploadImageInputId ).data( errorMessage );

                this.imageErrorHandler( error );
                return false;
            }
        }

        return true;
    };

    images.prototype.checkCommonFileSize = function( size, context ) {
        if ( (context != this.imageContextBackground && size < this.maxAllowedFileSize) ||
            (context == this.imageContextBackground && size < this.maxAllowedBackgroundFileSize)
        ) {
            return true;
        }

        return false;
    };

    //max allowed files count: 10
    images.prototype.checkMaxAllowedFilesCount = function( files ) {
        var error                  = '';
        var filesSelected          = files.length;
        var filesAlreadyAdded      = $( document ).find( this.html.imageRowContainer ).length;
        var logoAlreadyAdded       = $( document ).find( this.html.imageRowLogoContainer ).length;
        var backgroundAlreadyAdded = $( document ).find( this.html.imageRowBackgroundContainer ).length;
        var fileContextValue       = $( this.html.fileContextSelect ).val();

        var filesAdded      = filesSelected + filesAlreadyAdded;
        var logoAdded       = filesSelected + logoAlreadyAdded;
        var backgroundAdded = filesSelected + backgroundAlreadyAdded;

        if( filesAdded > this.maxAllowedFilesCount ) {
            error = $( this.html.uploadImageInputId ).data( 'error-count-limit' ) + this.maxAllowedFilesCount;

            this.imageErrorHandler( error );
            return false;
        }

        if ( fileContextValue == logoTypeConstant && logoAdded > this.maxAllowedMediaItemCount ) {
            error = $( this.html.uploadImageInputId ).data( 'error-logo-limit' ) + this.maxAllowedMediaItemCount;

            this.imageErrorHandler( error );
            return false;
        }

        if ( fileContextValue == backgroundTypeConstant && backgroundAdded > this.maxAllowedMediaItemCount ) {
            error = $( this.html.uploadImageInputId ).data( 'error-background-limit' ) + this.maxAllowedMediaItemCount;

            this.imageErrorHandler( error );
            return false;
        }

        return true;
    };

    //actions before ajax start: show loader / etc
    images.prototype.beforeRequestHandler = function () {
        this.removeImageErrors();
        this.removeURLErrors();
        $( document ).find( '.' + this.html.imageEditFormClassname ).hide();
        this.spinner.show( this.spinnerContainerId );

        $( '#' + this.html.buttons.startUploadRemoteFileButtonId ).attr( 'disabled', 'disabled' );
        $( '#' + this.html.buttons.fileInputId ).attr( 'disabled', 'disabled' );
    };

    //action on ajax compelete
    images.prototype.completeHandler = function() {
        this.spinner.hide();
        $( this.html.remoteImageURLInputId ).val( '' );

        var $galleryContainer = $( document ).find( '#' + this.html.galleryContainerId );

        //hide table if no images exists
        if( $galleryContainer.find( this.html.imageRowContainer ).length > 0 ) {
            $galleryContainer.parent().find( '.blank__message' ).hide();
            $galleryContainer.show();
        } else {
            $galleryContainer.parent().find( '.blank__message' ).show();
            $galleryContainer.hide();
        }

        $( '#' + this.html.buttons.startUploadRemoteFileButtonId ).removeAttr( 'disabled' );
        $( '#' + this.html.buttons.fileInputId ).removeAttr( 'disabled' );
    };

    //actions on ajax success
    images.prototype.onRequestSuccess = function( response ) {
        var that = this;

        if ( response.success === false ) {
            this.imageErrorHandler( response.message );
        } else {
            this.removeImageErrors();
            that.removeURLErrors();
            var $response = $( response );

            var $imagesContainer = $( document ).find( '#' + this.html.galleryContainerId );

            $response.each(function() {
                var $imageRow = $( this );

                var imageId = $imageRow.attr( 'id' );

                if( !$( document ).find( '#' + imageId ).length > 0 && that.removedItems.indexOf( imageId ) === -1 ) {
                    $imagesContainer.append( $imageRow );
                }
            });

            new select;
        }
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

        var fileContextValue = $( this.html.fileContextSelect ).val();
        formData.append( 'context', fileContextValue );

        return formData;
    };

    //check allowed filesize/count. Start upload process
    images.prototype.handleFileUploadInput = function() {
        var that = this;

        $(document).on( 'change', '#' + this.html.buttons.fileInputId, function() {
            var $this = $( this );

            var files = document.getElementById( that.html.buttons.fileInputId ).files;

            if( that.checkMaxAllowedFileSize( files ) == false ) {
                return false;
            }

            if( that.checkMaxAllowedFilesCount( files ) == false ) {
                return false;
            }

            that.doRequest( $this.parent().find( 'button.file-upload-button' ).data( 'url' ) );
        } );

        $(document).on( 'click', '#' + this.html.buttons.fileInputId, function() {
            $(this).val(null);
        } );
    };

    //it should be possible to upload image from 3rd-party services by URL
    images.prototype.handleRemoteImageUpload = function() {
        var $remoteImageURLInput = $( this.html.remoteImageURLInputId );

        var that = this;

        $( document ).on( 'click', '#' + this.html.buttons.startUploadRemoteFileButtonId, function( event ) {
            that.removeURLErrors();
            that.removeImageErrors();

            if ( !$remoteImageURLInput.val() ) {
                var error = $( that.html.remoteImageURLInputId ).data( 'error-empty' );

                that.URLErrorHandler( error );
            } else {
                if( that.checkMaxAllowedFilesCount( [$remoteImageURLInput.val()] ) == false ) {
                    return false;
                }

                var businessProfileId = $( '#' + that.html.buttons.fileInputId ).parents( 'form' ).data( 'id' );
                var fileContextValue = $( that.html.fileContextSelect ).val();

                var data = {
                    url: $remoteImageURLInput.val(),
                    businessProfileId: businessProfileId,
                    context: fileContextValue
                };

                $.ajax( {
                    url: that.urls.uploadByURL,
                    type: 'POST',
                    data: data,
                    beforeSend: $.proxy( that.beforeRequestHandler, that ),
                    complete: $.proxy( that.completeHandler, that ),
                    success: $.proxy( that.onRequestSuccess, that ),
                    error: $.proxy( that.URLErrorHandler, that )
                } );

                event.preventDefault();
            }
        } );
    };

    //on image click - show "active" border & show image-edit form
    images.prototype.handleClickOnImages = function() {
        var $imageContainerClass = '.' + this.html.imageContainerClassname;
        var $imageFormContainerClass = '.' + this.html.imageEditFormClassname;

        $(document).on( 'click', $imageContainerClass, function() {
            var $self = $(this);

            $(document).find( $imageContainerClass ).removeClass( 'active' );
            $self.addClass( 'active' );

            $(document).find( $imageFormContainerClass ).hide();

            var imageId = $self.data( 'id' );

            $(document).find( '#images-form-' + imageId ).show();
        } );
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
        var that = this;

        $(document).on( 'click', '.' + this.html.removeImageClassname, function( event ) {
            var imageId = 'images-form-' + $(this).data( 'id' );

            that.removedItems.push( imageId );
            $(document).find( '#' + imageId ).remove();

            event.preventDefault();
        } );
    };

    images.prototype.imageErrorHandler = function( error ) {
        var $uploadImageInputId = $( this.html.uploadImageInputId );

        this.removeImageErrors();
        this.removeURLErrors();
        $uploadImageInputId.parent().addClass( 'field--not-valid' );
        $uploadImageInputId.after( "<span data-error-message class='error'>" + error + "</span>" );

        return false;
    };

    images.prototype.URLErrorHandler = function( error ) {
        var $remoteImageURLInputId = $( this.html.remoteImageURLInputId );

        this.removeImageErrors();
        this.removeURLErrors();
        $remoteImageURLInputId.parent().addClass( 'field--not-valid' );
        $remoteImageURLInputId.after( "<span data-error-message class='error'>" + error + "</span>" );

        return false;
    };

    images.prototype.removeImageErrors = function() {
        var $uploadImageInputId = $( this.html.uploadImageInputId );

        $uploadImageInputId.parent().removeClass( 'field--not-valid' );
        $uploadImageInputId.parent().find( 'span[data-error-message]' ).remove();

        return false;
    };

    images.prototype.removeURLErrors = function() {
        var $remoteImageURLInputId = $( this.html.remoteImageURLInputId );

        $remoteImageURLInputId.parent().removeClass( 'field--not-valid' );
        $remoteImageURLInputId.parent().find( 'span[data-error-message]' ).remove();

        return false;
    };

    // only 1 image can be "Logo" - remove logo from other
    images.prototype.handleImageTypeChange = function() {
        var self = this;

        $(document).on( 'change', this.html.imageTypeSelectClassname, function() {
            if ( typeof logoTypeConstant !== undefined && $( this ).val() == logoTypeConstant ) {
                var triggeredSelect = this;

                $.each( $( self.html.imageTypeSelectClassname ), function() {
                    if ( $( this ).val() == logoTypeConstant &&
                        $( triggeredSelect ).closest( '.image-row' ).children('.hidden-media').val() != $( this ).closest( '.image-row' ).children('.hidden-media').val()
                    ) {
                        $( this ).val( photoTypeConstant );

                        new select;
                    }
                } );
            }
        } );

        $(document).on( 'change', this.html.imageTypeSelectClassname, function() {
            if ( typeof backgroundTypeConstant !== undefined && $( this ).val() == backgroundTypeConstant ) {
                var triggeredSelect = this;

                $.each( $( self.html.imageTypeSelectClassname ), function() {
                    if ( $( this ).val() == backgroundTypeConstant &&
                        $( triggeredSelect ).closest( '.image-row' ).children('.hidden-media').val() != $( this ).closest( '.image-row' ).children('.hidden-media').val()
                    ) {
                        $( this ).val( photoTypeConstant );

                        new select;
                    }
                } );
            }
        } );
    };

    images.prototype.handleImageValidationError = function() {
        $( document ).on( 'change', this.html.imageDescriptionFields, function() {
            var description = $( this );

            description.parent().removeClass( 'field--not-valid' );
            description.parent().find( 'span[data-error-message]' ).remove();
        });
    };


    return images;
});
