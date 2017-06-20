$( document ).ready( function() {
    var html = {
        containers: {
            businessOverviewChartContainerId: '#' + uniqueId + '_interactionReportChartContainer',
            businessOverviewStatsContainerId: '#' + uniqueId + '_interactionReportStatisticsContainer',
            keywordChartContainerId: '#keywordChartContainer',
            keywordStatsContainerId: '#keywordStatisticsContainer',
            adUsageStatsContainerId: '#adUsageStatisticsContainer',
            adUsageChartContainerId: '#adUsageChartContainer'
        },
        inputs: {
            interactionDateStart:   '#' + uniqueId + '_interactionReportFiltersDateStart',
            interactionDateEnd:     '#' + uniqueId + '_interactionReportFiltersDateEnd',
            keywordDateStart:       '#' + uniqueId + '_keywordReportFiltersDateStart',
            keywordDateEnd:         '#' + uniqueId + '_keywordReportFiltersDateEnd',
            keywordLimit:           '#' + uniqueId + '_keywordReportLimit',
            adUsageDateStart:       '#' + uniqueId + '_adUsageReportFiltersDateStart',
            adUsageDateEnd:         '#' + uniqueId + '_adUsageReportFiltersDateEnd'
        },
        buttons: {
            keywordFilter:      '#' + uniqueId + '_keywordReportFiltersFilter',
            interactionFilter:  '#' + uniqueId + '_interactionReportFiltersFilter',
            adUsageFilter:      '#' + uniqueId + '_adUsageReportFiltersFilter',
            exportExcel:        '[data-export-type = "export-excel"]',
            exportPdf:          '[data-export-type = "export-pdf"]',
            print:              '[data-export-type = "print"]'
        },
        tabs: {
            'interactionTab':   'a[href="#tab_' + uniqueId + '_3"]',
            'keywordTab':       'a[href="#tab_' + uniqueId + '_4"]',
            'adUsageTab':       'a[href="#tab_' + uniqueId + '_5"]',
            interactionTabId:   '#tab_' + uniqueId + '_3',
            keywordTabId:       '#tab_' + uniqueId + '_4',
            adUsageTabId:       '#tab_' + uniqueId + '_5'
        }
    };

    var reportUrls = {
        businessOverviewDataAction: Routing.generate( 'domain_business_admin_reports_business_overview_data' ),
        keywordsDataAction:         Routing.generate( 'domain_business_admin_reports_keywords_data' ),
        adUsageDataAction:          Routing.generate('domain_business_admin_reports_ad_usage_data'),
        pdfExportURL:               Routing.generate( 'domain_business_admin_reports_pdf_export' ),
        excelExportURL:             Routing.generate( 'domain_business_admin_reports_excel_export' )
    };

    initDatetimePickers();

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

        $( html.tabs.interactionTab ).on( 'click', function() {
            loadBusinessOverviewReport();
        });

        $( html.buttons.keywordFilter ).on( 'click', function() {
            loadKeywordsReport();
        });

        $( html.tabs.keywordTab ).on( 'click', function() {
            loadKeywordsReport();
        });

        $( html.buttons.adUsageFilter ).on( 'click', function() {
            loadAdUsageReport();
        });

        $( html.tabs.adUsageTab ).on( 'click', function() {
            loadAdUsageReport();
        });
    }

    handleExport();

    function handleExport() {
        $( document ).on( 'click', html.buttons.exportExcel, function (e) {
            var filtersData = $.param( getFilterParams() );
            window.open( $( this ).data( 'href' ) + '?' + filtersData );
        });

        $( document ).on('click', html.buttons.exportPdf, function (e) {
            var filtersData = $.param( getFilterParams() );

            window.open( $( this ).data( 'href' ) + '?' + filtersData );
        });

        $( document ).on('click', html.buttons.print, function (e) {
            var filterParams = getFilterParams();
            filterParams.print = true;

            var filtersData = $.param( filterParams );

            window.open( $( this ).data( 'href' ) + '?' + filtersData );
        });
    }

    function getFilterParams() {
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

    function initDatetimePickers() {
        $( '[data-date-format]' ).datetimepicker({
            "pickTime":false,
            "useCurrent":true,
            "minDate":"1\/1\/1900",
            "maxDate":null,
            "showToday":true,
            "language":"en",
            "defaultDate":"",
            "disabledDates":[],
            "enabledDates":[],
            "icons":{
                "time":"fa fa-clock-o",
                "date":"fa fa-calendar",
                "up":"fa fa-chevron-up",
                "down":"fa fa-chevron-down"
            },
            "useStrict":false,
            "sideBySide":false,
            "daysOfWeekDisabled":[],
            "useSeconds":false
        });
    }
});
