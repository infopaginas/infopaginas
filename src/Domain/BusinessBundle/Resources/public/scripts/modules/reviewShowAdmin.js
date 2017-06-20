$( document ).ready( function() {
    var html = {
        containers: {
            reviewContainerId:      '#' + uniqueId + '_reviewListTable',
            pageCurrentContainerId: '#' + uniqueId + '_reviewPaginationCurrent',
            pageTotalContainerId:   '#' + uniqueId + '_reviewPaginationTotal'
        },
        inputs: {
            goToPage:   '#' + uniqueId + '_reviewPaginationPage'
        },
        buttons: {
            nextPage: '#' + uniqueId + '_reviewPaginationNext',
            prevPage: '#' + uniqueId + '_reviewPaginationPrev',
            goToPage: '#' + uniqueId + '_reviewPaginationGo'
        },
        tabs: {
            'reviewTab':   'a[href="#tab_' + uniqueId + '_2"]'
        },
        urls: {
            businessReview: Routing.generate( 'domain_business_admin_business_reviews_data' )
        }
    };

    var currentBusinessReviewPage = 1;
    var maxBusinessProfilePageCount;

    function loadBusinessReviews( page ) {
        $.ajax({
            url: html.urls.businessReview,
            data: {
                page: page,
                businessProfileId: businessProfileId
            },
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( html.containers.reviewContainerId ).html( '' );
            },
            success: function(response) {
                $( html.containers.reviewContainerId ).html( response.data );
                handleBusinessReviewPaginator( page, response.pageCount );
            }
        });
    }

    handleBusinessReviewUpdate();

    function handleBusinessReviewPaginator( page, totalPages ) {
        currentBusinessReviewPage = parseInt( page );
        maxBusinessProfilePageCount = parseInt( totalPages );

        $( html.containers.pageCurrentContainerId ).html( currentBusinessReviewPage );
        $( html.containers.pageTotalContainerId ).html( maxBusinessProfilePageCount );
        $( html.inputs.goToPage ).attr( 'max', maxBusinessProfilePageCount );
        $( html.inputs.goToPage ).val( currentBusinessReviewPage );
    }

    function handleBusinessReviewUpdate() {
        $( html.buttons.nextPage ).on( 'click', function() {
            loadBusinessReviewNexPage();
        });

        $( html.buttons.prevPage ).on( 'click', function() {
            loadBusinessReviewPrevPage();
        });

        $( html.buttons.goToPage ).on( 'click', function() {
            loadBusinessReviewPage();
        });

        $( html.inputs.goToPage ).keypress(function( e ) {
            if( e.which == 13 ) {
                loadBusinessReviewPage();
            }
        });

        $( html.tabs.reviewTab ).on( 'click', function() {
            loadBusinessReviewPage();
        });
    }

    function loadBusinessReviewPrevPage() {
        var page = currentBusinessReviewPage - 1;

        if ( page >= 1 ) {
            loadBusinessReviews( page );
        }
    }

    function loadBusinessReviewNexPage() {
        var page = currentBusinessReviewPage + 1;

        if ( !maxBusinessProfilePageCount || maxBusinessProfilePageCount >= page ) {
            loadBusinessReviews( page );
        }
    }

    function loadBusinessReviewPage() {
        var page = $( html.inputs.goToPage ).val();

        if ( !page || page > maxBusinessProfilePageCount || page < 1 ) {
            page = currentBusinessReviewPage;
        }

        loadBusinessReviews( page );
    }
});
