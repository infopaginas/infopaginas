$( document ).ready( function () {
    var fileInput = 'input[type=file]';
    var mappingInfoInputClass = '.mapping-info';
    var fieldsWrapperClass = '.sonata-ba-collapsed-fields';
    var mapingFieldsWrapper = '#csv-mapping';
    var fileFieldsSelect = 'select.file-field';
    var delimiterInput = '.delimiter';

    var headers = [];
    var defaultDelimiter = ',';

    $( fileInput ).on( 'change', handleFileUpload );
    $( 'form' ).on( 'submit', handleFormSubmission );

    function handleFormSubmission( e ) {
        e.preventDefault();

        var mappingInfo = {};
        for ( var field in JSON.parse( $( mappingInfoInputClass ).val() ) ) {
            mappingInfo[ field ] = $( '#field-' + field ).val();
        }
        $( mappingInfoInputClass ).val( JSON.stringify( mappingInfo ) );
        $( this )[ 0 ].submit();
    }

    function setMappingOptions() {
        $( fileFieldsSelect ).each( function ( index, item ) {
            $( item ).empty();
            item.append( new Option() );
            headers.forEach( function ( header ) {
                item.append( new Option( header, header ) );
            } );
            $( this ).trigger( 'change' );
        } );
    }

    var showMappingFields = (function () {
        var executed = false;
        return function () {
            if ( !executed ) {
                executed = true;
                $( fieldsWrapperClass ).append( $( mapingFieldsWrapper )[ 0 ] );
                $( mapingFieldsWrapper ).removeClass( 'hidden' );
            }
        };
    })();

    function handleFileUpload() {
        var file = $( fileInput )[ 0 ].files[ 0 ];

        if ( !file ) {
            return;
        }

        var reader = new FileReader();
        reader.readAsArrayBuffer( file );

        reader.onloadend = function ( e ) {
            // Get the Array Buffer
            var data = e.target.result;
            var byteLength = data.byteLength;
            var ui8a = new Uint8Array( data, 0 );
            // Used to store each character that makes up CSV header
            var headerString = '';
            for ( var i = 0; i < byteLength; i++ ) {
                var char = String.fromCharCode( ui8a[ i ] );
                // Check if the char is a new line
                if ( char.match( /[^\r\n]+/g ) !== null ) {
                    headerString += char;
                } else {
                    break;
                }
            }
            headers = headerString.split( getDelimiter() );

            setMappingOptions();
            showMappingFields();
        };
    }

    function getDelimiter() {
        return $( delimiterInput ).val() ? $( delimiterInput ).val() : defaultDelimiter;
    }
} );
