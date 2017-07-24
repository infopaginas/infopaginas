$( document ).ready( function() {

    var tabs = {
        'interactionTab': 'a[href="#tab_' + formId + '_3"]',
        'keywordTab': 'a[href="#tab_' + formId + '_4"]',
        'adUsageTab': 'a[href="#tab_' + formId + '_5"]'
    };

    var html = {
        containers: {
            businessOverviewChartContainerId: '#businessOverviewChartContainer',
            businessOverviewStatsContainerId: '#businessOverviewStatisticsContainer',
            keywordChartContainerId: '#keywordChartContainer',
            keywordStatsContainerId: '#keywordStatisticsContainer',
            adUsageStatsContainerId: '#adUsageStatisticsContainer',
            adUsageChartContainerId: '#adUsageChartContainer'
        },
        inputs: {
            interactionDateStart:   '#' + formId + '_interactionDateStart',
            interactionDateEnd:     '#' + formId + '_interactionDateEnd',
            keywordDateStart:       '#' + formId + '_keywordDateStart',
            keywordDateEnd:         '#' + formId + '_keywordDateEnd',
            keywordLimit:           '#' + formId + '_keywordLimit',
            adUsageDateStart:       '#' + formId + '_adUsageDateStart',
            adUsageDateEnd:         '#' + formId + '_adUsageDateEnd'
        },
        buttons: {
            keywordFilter: '#keywordFilter',
            interactionFilter: '#interactionFilter',
            adUsageFilter: '#adUsageFilter',
            exportExcel: '#export-excel',
            exportPdf: '#export-pdf',
            print: '#print'
        },
        tabs: {
            interactionTabId: '#tab_' + formId + '_3',
            keywordTabId: '#tab_' + formId + '_4',
            adUsageTabId: '#tab_' + formId + '_5'
        }
    };

    var reportUrls = {
        businessOverviewDataAction: Routing.generate( 'domain_business_admin_reports_business_overview_data' ),
        keywordsDataAction: Routing.generate( 'domain_business_admin_reports_keywords_data' ),
        adUsageDataAction: Routing.generate('domain_business_admin_reports_ad_usage_data'),
        pdfExportURL: Routing.generate( 'domain_business_admin_reports_pdf_export' ),
        excelExportURL: Routing.generate( 'domain_business_admin_reports_excel_export' )
    };

    var exportButtons = '<div class="export-report-button-group">' +
        '<button type="button" href="' + reportUrls.excelExportURL + '" id="export-excel" class="btn green-btn"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Excel</button>' +
        '<button type="button" href="' + reportUrls.pdfExportURL + '" id="export-pdf" class="btn green-btn"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>PDF</button>' +
        '<button type="button" href="' + reportUrls.pdfExportURL + '" id="print" class="btn green-btn"><i class="fa fa-print" aria-hidden="true"></i>Print</button>' +
        '</div>'
    ;

    var interactionDateEndContainer = $( '#sonata-ba-field-container-' + formId + '_interactionDateEnd' );
    interactionDateEndContainer.after( exportButtons );
    interactionDateEndContainer.after( '<div class="form__section scrollable-table" id="businessOverviewStatisticsContainer"></div>' );
    interactionDateEndContainer.after( '<div id="businessOverviewChartContainer" class="chart-container"></div>' );
    interactionDateEndContainer.after( '<div class="form-group"><button id="interactionFilter" type="button" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter </button></div>' );

    var keywordLimitContainer = $( '#sonata-ba-field-container-' + formId + '_keywordLimit' );
    keywordLimitContainer.after( exportButtons );
    keywordLimitContainer.after( '<div class="form__section scrollable-table" id="keywordStatisticsContainer"></div>' );
    keywordLimitContainer.after( '<div id="keywordChartContainer" class="chart-container"></div>' );
    keywordLimitContainer.after( '<div class="form-group"><button id="keywordFilter" type="button" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter </button></div>' );

    var adUsageDateEndContainer = $( '#sonata-ba-field-container-' + formId + '_adUsageDateEnd' );
    adUsageDateEndContainer.after( exportButtons );
    adUsageDateEndContainer.after( '<div class="form__section scrollable-table" id="adUsageStatisticsContainer"></div>' );
    adUsageDateEndContainer.after( '<div id="adUsageChartContainer" class="chart-container"></div>' );
    adUsageDateEndContainer.after( '<div class="form-group"><button id="adUsageFilter" type="button" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter </button></div>' );

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

    function loadAdUsageReport() {
        $.ajax({
            url: reportUrls.adUsageDataAction,
            data: getAdUsageFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( html.containers.adUsageChartContainerId ).html( '' );
                $( html.containers.adUsageStatsContainerId ).html( '' );
            },
            success: function(response) {
                $( html.containers.adUsageStatsContainerId ).html( response.stats );
                loadAdUsageChart( response.dates, response.clicks, response.impressions );
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

    function loadAdUsageChart( dates, clicks, impressions ) {
        $( html.containers.adUsageChartContainerId ).highcharts({
            title: {
                text: 'Ad Usage Statistics',
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
                    name: 'Clicks',
                    data: clicks
                },
                {
                    name: 'Impressions',
                    data: impressions
                }
            ]
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

    function getAdUsageFilterValues() {
        var adUsageDateStart = $( html.inputs.adUsageDateStart ).val();
        var adUsageDateEnd   = $( html.inputs.adUsageDateEnd ).val();

        return {
            'businessProfileId': businessProfileId,
            'start': adUsageDateStart,
            'end': adUsageDateEnd,
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

        $( html.buttons.adUsageFilter ).on( 'click', function() {
            loadAdUsageReport();
        });

        $( tabs.adUsageTab ).on( 'click', function() {
            loadAdUsageReport();
        });
    }

    handleExport();

    function handleExport()
    {
        $( document ).on( 'click', html.buttons.exportExcel, function (e) {
            var filtersData = $.param( getFilterParams() );
            window.open( $( this ).attr( 'href' ) + '?' + filtersData );
        });

        $( document ).on('click', html.buttons.exportPdf, function (e) {
            var filtersData = $.param( getFilterParams() );
            window.open( $( this ).attr( 'href' ) + '?' + filtersData );
        });

        $( document ).on('click', html.buttons.print, function (e) {
            var filterParams = getFilterParams();
            filterParams.print = true;

            var filtersData = $.param( filterParams );

            window.open( $( this ).attr( 'href' ) + '?' + filtersData );
        });
    }

    function getFilterParams()
    {
        var filterParams;

        if ( $( html.tabs.interactionTabId ).hasClass( 'active' ) ) {
            filterParams = getInteractionFilterValues();
        } else if ( $( html.tabs.keywordTabId ).hasClass( 'active' ) ) {
            filterParams = getKeywordFilterValues();
        } else {
            filterParams = getAdUsageFilterValues();
        }

        return filterParams;
    }
});