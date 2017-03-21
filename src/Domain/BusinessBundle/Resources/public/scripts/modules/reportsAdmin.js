$( document ).ready( function() {

    var tabs = {
        'interactionTab': 'a[href="#tab_' + formId + '_3"]',
        'keywordTab': 'a[href="#tab_' + formId + '_4"]'
    };

    var html = {
        containers: {
            businessOverviewChartContainerId: '#businessOverviewChartContainer',
            businessOverviewStatsContainerId: '#businessOverviewStatisticsContainer',
            keywordChartContainerId: '#keywordChartContainer',
            keywordStatsContainerId: '#keywordStatisticsContainer'
        },
        inputs: {
            interactionDateStart:   '#' + formId + '_interactionDateStart',
            interactionDateEnd:     '#' + formId + '_interactionDateEnd',
            keywordDateStart:       '#' + formId + '_keywordDateStart',
            keywordDateEnd:         '#' + formId + '_keywordDateEnd',
            keywordLimit:           '#' + formId + '_keywordLimit'
        },
        buttons: {
            keywordFilter: '#keywordFilter',
            interactionFilter: '#interactionFilter',
            exportExcel: '#export-excel',
            exportPdf: '#export-pdf',
            print: '#print'
        },
        tabs: {
            interactionTabId: '#tab_' + formId + '_3',
            keywordTabId: '#tab_' + formId + '_4'
        }
    };

    var reportUrls = {
        businessOverviewDataAction: Routing.generate( 'domain_business_admin_reports_business_overview_data' ),
        keywordsDataAction: Routing.generate( 'domain_business_admin_reports_keywords_data' ),
        pdfExportURL: Routing.generate( 'domain_business_admin_reports_pdf_export' ),
        excelExportURL: Routing.generate( 'domain_business_admin_reports_excel_export' )
    };

    var pdfExportButton = '<div class="form-group"><button type="button" href="' + reportUrls.pdfExportURL + '" id="export-pdf" class="btn green-btn"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>PDF</button></div>';
    var excelExportButton = '<div class="form-group"><button type="button" href="' + reportUrls.excelExportURL + '" id="export-excel" class="btn green-btn"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Excel</button></div>';
    var printButton = '<div class="form-group"><button type="button" href="' + reportUrls.pdfExportURL + '" id="print" class="btn green-btn"><i class="fa fa-print" aria-hidden="true"></i>Print</button></div>';

    var interactionDateEndContainer = $( '#sonata-ba-field-container-' + formId + '_interactionDateEnd' );
    interactionDateEndContainer.after( printButton );
    interactionDateEndContainer.after( pdfExportButton );
    interactionDateEndContainer.after( excelExportButton );
    interactionDateEndContainer.after( '<div class="form__section scrollable-table" id="businessOverviewStatisticsContainer"></div>' );
    interactionDateEndContainer.after( '<div id="businessOverviewChartContainer" style="min-width: 310px; height: 400px; margin: 0 auto"></div>' );
    interactionDateEndContainer.after( '<div class="form-group"><button id="interactionFilter" type="button" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter </button></div>' );

    var keywordLimitContainer = $( '#sonata-ba-field-container-' + formId + '_keywordLimit' );
    keywordLimitContainer.after( printButton );
    keywordLimitContainer.after( pdfExportButton );
    keywordLimitContainer.after( excelExportButton );
    keywordLimitContainer.after( '<div class="form__section scrollable-table" id="keywordStatisticsContainer"></div>' );
    keywordLimitContainer.after( '<div id="keywordChartContainer" style="min-width: 310px; height: 400px; margin: 0 auto"></div>' );
    keywordLimitContainer.after( '<div class="form-group"><button id="keywordFilter" type="button" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter </button></div>' );

    function loadBusinessOverviewReport() {
        $.ajax({
            url: reportUrls.businessOverviewDataAction,
            data: getInteractionFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( html.containers.businessOverviewChartContainerId ).html( '' );
                $( html.containers.businessOverviewStatsContainerId ).html( '' );
            },
            success: function(response) {
                $( html.containers.businessOverviewStatsContainerId ).html( response.stats );
                loadBusinessOverviewChart( response.dates, response.views, response.impressions );
            }
        });
    }

    function loadKeywordsReport() {
        $.ajax({
            url: reportUrls.keywordsDataAction,
            data: getKeywordFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( html.containers.keywordChartContainerId ).html( '' );
                $( html.containers.keywordStatsContainerId ).html( '' );
            },
            success: function(response) {
                $( html.containers.keywordStatsContainerId ).html( response.stats );
                loadKeywordsChart( response.keywords, response.searches );
            }
        });
    }

    function loadBusinessOverviewChart(dates, views, impressions) {
        $( html.containers.businessOverviewChartContainerId ).highcharts({
            title: {
                text: 'Interactions',
                x: -20 //center
            },
            xAxis: {
                categories: dates
            },
            yAxis: {
                title: {
                    text: 'Count'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [
                {
                    name: 'Views',
                    data: views
                },
                {
                    name: 'Impressions',
                    data: impressions
                }
            ]
        });
    }

    function loadKeywordsChart(keywords, searches) {
        $(html.containers.keywordChartContainerId).highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Keywords'
            },
            xAxis: {
                categories: keywords
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Count'
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b><br/>' + this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            series: [{
                name: 'Keywords',
                data: searches
            }]
        });
    }

    function getInteractionFilterValues() {
        var interactionDateStart = $( html.inputs.interactionDateStart ).val();
        var interactionDateEnd   = $( html.inputs.interactionDateEnd ).val();

        return {
            'businessProfileId': businessProfileId,
            'start': interactionDateStart,
            'end': interactionDateEnd,
            'datesRange': 'custom',
            'limit': 10
        };
    }

    function getKeywordFilterValues() {
        var keywordDateStart = $( html.inputs.keywordDateStart ).val();
        var keywordDateEnd   = $( html.inputs.keywordDateEnd ).val();
        var keywordLimit     = $( html.inputs.keywordLimit ).val();

        return {
            'businessProfileId': businessProfileId,
            'start': keywordDateStart,
            'end': keywordDateEnd,
            'datesRange': 'custom',
            'limit': keywordLimit
        };
    }

    handleReportUpdate();

    function handleReportUpdate() {
        $( html.buttons.interactionFilter ).on( 'click', function() {
            loadBusinessOverviewReport();
        });

        $( tabs.interactionTab ).on( 'click', function() {
            loadBusinessOverviewReport();
        });

        $( html.buttons.keywordFilter ).on( 'click', function() {
            loadKeywordsReport();
        });

        $( tabs.keywordTab ).on( 'click', function() {
            loadKeywordsReport();
        });
    }

    handleExport();

    function handleExport()
    {
        $( document ).on( 'click', html.buttons.exportExcel, function (e) {
            var filtersData = $.param( getFilterParams() );
            location.href = $( this ).attr( 'href' ) + '?' + filtersData;
        });

        $( document ).on('click', html.buttons.exportPdf, function (e) {
            var filtersData = $.param( getFilterParams() );
            location.href = $( this ).attr( 'href' ) + '?' + filtersData;
        });

        $( document ).on('click', html.buttons.print, function (e) {
            var filterParams = getFilterParams();
            filterParams.print = true;

            var filtersData = $.param( filterParams );

            location.href = $( this ).attr( 'href' ) + '?' + filtersData;
        });
    }

    function getFilterParams()
    {
        var filterParams;

        if ( $( html.tabs.interactionTabId ).hasClass('active') ) {
            filterParams = getInteractionFilterValues();
        } else {
            filterParams = getKeywordFilterValues();
        }

        return filterParams;
    }
});