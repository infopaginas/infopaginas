$( document ).ready( function() {
    var html = {
        containers: {
            businessOverviewChartContainerId: '#' + uniqueId + '_interactionReportChartContainer',
            businessOverviewStatsContainerId: '#' + uniqueId + '_interactionReportStatisticsContainer',
            businessOverviewHintContainerId: '#' + uniqueId + '_interactionReportChartHintContainer',
            keywordChartContainerId: '#' + uniqueId + '_keywordReportChartContainer',
            keywordStatsContainerId: '#' + uniqueId + '_keywordReportStatisticsContainer',
            adUsageChartContainerId: '#' + uniqueId + '_adUsageReportChartContainer',
            adUsageStatsContainerId: '#' + uniqueId + '_adUsageReportStatisticsContainer'
        },
        inputs: {
            mainDateStart:    '#' + uniqueId + '_mainReportFiltersDateStart',
            mainDateEnd:      '#' + uniqueId + '_mainReportFiltersDateEnd',
            actionType:       '#' + uniqueId + '_actionType',
            periodOption:     '#' + uniqueId + '_periodOption',
            keywordLimit:     '#' + uniqueId + '_keywordReportLimit',
            adUsageDateStart: '#' + uniqueId + '_adUsageReportFiltersDateStart',
            adUsageDateEnd:   '#' + uniqueId + '_adUsageReportFiltersDateEnd',
            mainPeriods:      'input[name="' + uniqueId + '[period]"]'
        },
        buttons: {
            mainFilter:         '#' + uniqueId + '_mainReportFiltersFilter',
            actionTypeFilter:   '#' + uniqueId + '_actionTypeFilter',
            periodOptionFilter: '#' + uniqueId + '_periodOptionFilter',
            keywordFilter:      '#' + uniqueId + '_keywordReportLimitFilter',
            adUsageFilter:      '#' + uniqueId + '_adUsageReportFiltersFilter',
            export:             '[data-export-type]',
            exportExcel:        '[data-export-type = "export-excel"]',
            exportPdf:          '[data-export-type = "export-pdf"]',
            print:              '[data-export-type = "print"]'
        },
        tabs: {
            mainReportTab:      'a[href="#tab_' + uniqueId + '_6"]',
            adUsageTab:         'a[href="#tab_' + uniqueId + '_7"]',
            mainReportTabId:    '#tab_' + uniqueId + '_6',
            adUsageTabId:       '#tab_' + uniqueId + '_7'
        }
    };

    var reportUrls = {
        businessOverviewDataAction: Routing.generate( 'domain_business_admin_reports_business_overview_data' ),
        keywordsDataAction:         Routing.generate( 'domain_business_admin_reports_keywords_data' ),
        adUsageDataAction:          Routing.generate( 'domain_business_admin_reports_ad_usage_data' )
    };

    initDatetimePickers();

    function loadBusinessOverviewReport() {
        $.ajax({
            url: reportUrls.businessOverviewDataAction,
            data: getMainFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( html.containers.businessOverviewChartContainerId ).html( '' );
                $( html.containers.businessOverviewStatsContainerId ).html( '' );
                $( html.containers.businessOverviewHintContainerId ).html( '' );
            },
            success: function(response) {
                $( html.containers.businessOverviewStatsContainerId ).html( response.stats );
                $( html.containers.businessOverviewHintContainerId ).html( response.chartHint );
                loadBusinessOverviewChart( response.dates, response.chart, response.chartTitle );
            }
        });
    }

    function loadKeywordsReport() {
        $.ajax({
            url: reportUrls.keywordsDataAction,
            data: getMainFilterValues(),
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

    function loadBusinessOverviewChart(dates, chartData, title) {
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
                    name: title,
                    data: chartData
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

    function getMainFilterValues() {
        var dateStart    = $( html.inputs.mainDateStart ).val();
        var dateEnd      = $( html.inputs.mainDateEnd ).val();
        var keywordLimit = $( html.inputs.keywordLimit ).val();
        var chartType    = $( html.inputs.actionType ).val();
        var period       = $( html.inputs.periodOption ).val();

        return {
            'businessProfileId': businessProfileId,
            'start': dateStart,
            'end':   dateEnd,
            'datesRange': 'custom',
            'limit': keywordLimit,
            'chartType': chartType,
            'periodOption': period
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

    handleReportUpdate();

    function handleReportUpdate() {
        $( html.buttons.mainFilter ).on( 'click', function() {
            loadBusinessOverviewReport();
            loadKeywordsReport();
        });

        $( html.tabs.mainReportTab ).on( 'click', function() {
            loadBusinessOverviewReport();
            loadKeywordsReport();
        });

        $( html.buttons.actionTypeFilter ).on( 'click', function() {
            loadBusinessOverviewReport();
        });

        $( html.buttons.periodOptionFilter ).on( 'click', function() {
            loadBusinessOverviewReport();
        });

        $( html.buttons.keywordFilter ).on( 'click', function() {
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
        $( document ).on('click', html.buttons.export, function (e) {
            var filterParams = getFilterParams();
            var exportRoute  = $( this ).data( 'route' );

            filterParams.format = $( this ).data( 'format' );

            if ( $( this ).data( 'export-type' ) == 'print' ) {
                filterParams.print = true;
            }

            window.open( Routing.generate( exportRoute, filterParams ) );
        });
    }

    function getFilterParams() {
        var filterParams;

        if ( $( html.tabs.mainReportTabId ).hasClass( 'active' ) ) {
            filterParams = getMainFilterValues();
        } else {
            filterParams = getAdUsageFilterValues();
        }

        return filterParams;
    }

    handlePeriodChoicesUpdate();

    function handlePeriodChoicesUpdate()
    {
        $( document ).on( 'ifChecked ifUnchecked', html.inputs.mainPeriods, function ( e ) {
            if ( $( this ).prop( 'checked' ) ) {
                handlePeriodChoicesCalendar( this );
            }
        });
    }

    function handlePeriodChoicesCalendar()
    {
        var period = parseInt( $( html.inputs.mainPeriods + ':checked' ).data( 'month' ) );
        var endDate   = new Date();
        var startDate = new Date();

        startDate.setMonth( startDate.getMonth() - period );

        $( html.inputs.mainDateStart ).data( 'DateTimePicker' ).setDate( startDate );
        $( html.inputs.mainDateEnd ).data( 'DateTimePicker' ).setDate( endDate );
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
