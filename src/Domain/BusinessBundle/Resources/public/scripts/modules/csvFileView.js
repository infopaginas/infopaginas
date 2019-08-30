$( document ).ready( function () {
    var interval = 5000;
    var spinnerWrapperClass = '.spinner-wrapper';
    var id = $( spinnerWrapperClass ).data( 'id' );
    var url = Routing.generate( 'domain_business_admin_csv_import_file_status', { id: id } );

    if ( id ) {
        setInterval( checkCSVImportFileStatus, interval );
    }

    function checkCSVImportFileStatus() {
        $.get( url, function ( data ) {
            if ( data.status === true ) {
                location.reload();
            }
        } );
    }
} );