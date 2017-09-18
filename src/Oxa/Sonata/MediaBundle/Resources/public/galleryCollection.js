$( document ).ready( function() {
    var settings = {
        on: {
            icon: 'glyphicon glyphicon-check'
        },
        off: {
            icon: 'glyphicon glyphicon-unchecked'
        },
        maxRows: 25,
        maxFileSize: 10485760, // 10Mb
        maxFileSizeText: '10 Mb',
        errors: {
            maxSize:  'error-max-size',
            maxCount: 'error-max-count',
            invalidExtension: 'error-invalid-extension'
        }
    };

    var imageUploadQueue = [];

    var validExtensions = [
        'png',
        'jpeg',
        'jpg',
        'gif'
    ];

    var errorBlock = $( '#' + formId + '_images_dropzone-error' );
    var mediaUploadButton = '#' + formId + '_addGalleryImage';

    $( document ).on( 'click', 'button.gallery-remove-button', function() {
        var button      = $( this );
        var parentRow   = button.parents( '.sonata-collection-row' ).first();
        var color       = button.data( 'color' );
        var textDefault = button.data( 'text-default' );
        var textChecked = button.data( 'text-checked' );
        var textBlock   = button.find( 'span' );

        var isChecked = button.hasClass( 'btn-default' );
        var hasMedia  = parentRow.find( 'input[id *="_media"]' ).first().val();

        if ( !checkRemoveButtonEnable( isChecked, hasMedia ) ) {
            return;
        }

        // Set the button's state
        button.data( 'state', (isChecked) ? 'on' : 'off' );

        // Set the button's icon
        button.find( '.state-icon' )
            .removeClass()
            .addClass( 'state-icon ' + settings[button.data( 'state' )].icon )
        ;

        // Update the button's color
        if ( isChecked ) {
            button
                .removeClass( 'btn-default' )
                .addClass( 'btn-' + color + ' active' )
            ;

            textBlock.html( textChecked );

            parentRow.find( 'input' ).attr( 'disabled', 'disabled' );
            parentRow.find( 'textarea' ).attr( 'disabled', 'disabled' );
        } else {
            button
                .removeClass( 'btn-' + color + ' active' )
                .addClass( 'btn-default' )
            ;

            textBlock.html( textDefault );

            parentRow.find( 'input' ).removeAttr( 'disabled' );
            parentRow.find( 'textarea' ).removeAttr( 'disabled' );
        }
    });

    $( 'div[ id $= "' + formId + '_images" ] a.sonata-collection-add' ).on( 'sonata-collection-item-added', function( event ) {
        var buttons = $( 'button.gallery-remove-button' );

        $.each( buttons, function( index, item ) {
            var button = $( item );
            var parentRow = button.parents( '.sonata-collection-row' ).first();
            var isChecked = button.hasClass( 'btn-default' );
            var mediaId = parentRow.find( 'input[id *="_media"]' ).val();

            if ( isChecked && !mediaId ) {
                button.click();
            }
        });

        updateMediaPositions();
    });

    function checkRemoveButtonEnable( isChecked, hasMedia ) {
        var buttonEnabled;

        var rowCount = $( 'div[ id $= "' + formId + '_images" ]' )
            .find( 'button.gallery-remove-button.btn-default' )
            .length
        ;

        if ( (rowCount >= settings.maxRows) || !hasMedia ) {
            buttonEnabled = isChecked;
        } else {
            buttonEnabled =  true;
        }

        return buttonEnabled;
    }

    // sortable
    $( '#' + formId + '_images' ).sortable({
        items: 'div.sonata-collection-row',
        update: updateMediaPositions,
        axis: 'y',
        cursorAt: { top: -$( window ).scrollTop() }
    });

    $( window ).scroll(function () {
        // workaround for scroll (in relative container with fixed header and side block) while sorting
        $( '#' + formId + '_images' ).sortable( 'option', 'cursorAt', { top: -$( window ).scrollTop() } );
    });

    function updateMediaPositions() {
        var positions = $( 'input[id *= "_position"]' );

        $.each( positions, function( index, item ) {
            $( item ).val( index );
        });
    }

    // upload button
    $( document ).on( 'change', mediaUploadButton, function() {
        var uploadButton = $( mediaUploadButton ).first();
        var files = uploadButton.prop( 'files' );

        handleFileUpload( files );

        uploadButton.val( null );
    } );

    // drop zone
    function sendFileToServer( formData, row ) {
        var uploadURL = Routing.generate( 'domain_business_admin_images_upload' ); //Upload URL

        $.ajax({
            url: uploadURL,
            type: 'POST',
            contentType: false,
            processData: false,
            cache: false,
            data: formData,
            success: function( response ) {
                if ( response.success ) {
                    row.media.val( response.data.id );
                    row.preview.attr( 'src', response.data.url );
                    row.filename.html( response.data.name );
                    row.removeButton.click();
                } else {
                    // error
                    addDropZoneError( response.message );
                    row.parentRow.remove();
                }

                $( document ).trigger( 'uploadAdminImage' );
            }
        });
    }

    function createGalleryRow() {
        var uploadBlock = $( '#' + formId + '_images_dropzone' ).parents( '.media-upload-block' );

        // add row
        uploadBlock.find( 'a.sonata-collection-add' ).click();

        this.media = uploadBlock
            .parents( '.sonata-ba-field-standard-natural' )
            .find( 'input[id *="_media"]:not([data-status="processed"], [value])' )
            .first()
        ;

        this.parentRow = this.media.parents( '.sonata-collection-row' ).first();
        this.preview   = this.parentRow.find( '.media-preview-block img' );
        this.filename  = this.parentRow.find( '.media-name-block span' );
        this.removeButton = this.parentRow.find( '.media_gallery-remove-block button' );

        this.media.attr( 'data-status', 'processed');
    }

    function addDropZoneError( error ) {
        var errorHtml = '<div class="help-inline sonata-ba-field-error-messages"><ul class="list-unstyled"><li><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + error + '</li></ul></div>';

        errorBlock.html( errorHtml );
    }

    function handleFileUpload( files ) {
        if ( !validateUploadedFiles( files ) ) {
            return;
        }

        for ( var i = 0; i < files.length; i++ ) {
            var fd = new FormData();
            var galleryRow = new createGalleryRow();

            fd.append( 'file', files[i] );

            imageUploadQueue.push({
                data: fd,
                row: galleryRow
            });
        }

        $( document ).trigger( 'uploadAdminImage' );
    }

    $( document ).on( 'uploadAdminImage', function( event, type, id ) {
        var image = imageUploadQueue.shift();

        if ( image ) {
            sendFileToServer( image.data, image.row );
        }
    });

    function validateUploadedFiles( files ) {
        var rowCount = $( 'div[ id $= "' + formId + '_images" ]' )
            .find( 'button.gallery-remove-button.btn-default' )
            .length
        ;

        if ( (rowCount + files.length) > settings.maxRows ) {
            addDropZoneError( errorBlock.data( settings.errors.maxCount ) + ' ' + settings.maxRows );
            return false;
        }

        for ( var i in files ) {
            var file = files[i];

            if ( file instanceof File) {
                if ( file.name ) {
                    var fileName = file.name;
                    var fileNameExt = fileName.substr( fileName.lastIndexOf( '.' ) + 1 );

                    if ( $.inArray( fileNameExt, validExtensions ) == -1 ) {
                        addDropZoneError( errorBlock.data( settings.errors.invalidExtension ) );
                        return false;
                    }
                }

                if ( file.size > settings.maxFileSize ) {
                    addDropZoneError( errorBlock.data( settings.errors.maxSize ) + ' ' + settings.maxFileSizeText );
                    return false;
                }
            }
        }

        return true;
    }

    $( document ).ready(function() {
        var dropZone = $( '.media-upload-block div.dropzone' );

        dropZone.on( 'dragenter', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        });

        dropZone.on( 'dragover', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        });

        dropZone.on( 'drop', function ( e ) {
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;

            //We need to send dropped files to Server
            handleFileUpload( files );
        });

        $( document ).on( 'dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $( document ).on( 'dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $( document ).on( 'drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
        });
    });

    initMediaPreview();

    $( document ).on( 'change', '#' + formId + '_logo, #' + formId + '_background', function( e ) {
        var mediaId = $( this ).val();

        if ( mediaId ) {
            getMediaPreview( this, mediaId );
        } else {
            clearMediaPreview( this );
        }
    });

    function getMediaPreview( elem, mediaId ) {
        showSpinnerMediaPreview( elem );

        $.ajax({
            url: Routing.generate( 'domain_business_admin_images_preview', {id: mediaId} ),
            type: 'GET',
            success: function( response ) {
                var img = $( elem ).parents( '.sonata-ba-field-list-natural .field-container' ).find( '.single-media-preview-block img' );

                if ( response.success ) {
                    img.attr( 'src', response.data.url );
                } else {
                    img.removeAttr( 'src' );
                }
            }
        });
    }

    function clearMediaPreview( elem ) {
        var img = $( elem ).parents( '.sonata-ba-field-list-natural' ).find( '.single-media-preview-block img' );
        img.removeAttr( 'src' );
    }

    function showSpinnerMediaPreview( elem ) {
        var img = $( elem ).parents( '.sonata-ba-field-list-natural' ).find( '.single-media-preview-block img' );
        img.attr( 'src', spinnerLink );
    }

    function initMediaPreview() {
        var items = $( '#' + formId + '_logo, #' + formId + '_background' );

        $.each( items, function( index, item ) {
            var media      = $( item );
            var mediaBlock = media.parents( '.sonata-ba-field-list-natural .field-container' );
            var html = '<div class="single-media-preview-block"><img></div>';
            mediaBlock.prepend( html );

            var mediaId = media.val();

            if ( mediaId ) {
                getMediaPreview( item, mediaId )
            }
        });
    }
});
