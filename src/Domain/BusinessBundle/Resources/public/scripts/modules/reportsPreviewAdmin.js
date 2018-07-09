$( document ).ready( function() {
    var html = {
        containers: {
            businessOverviewChartContainerId: 'div[id$="ChartContainer"]',
            businessOverviewStatsContainerId: 'div[id$="StatisticsContainer"]',
            businessOverviewHintContainerId:  'div[id$="ChartHintContainer"]',
            chartParentBlockId:               'div[id$="ChartParentBlock"]',
            keywordStatsContainerId:          'div[id$="StatisticsKeywordsContainer"]'
        },
        inputs: {
            mainDateStart: 'input[id$="DateStart"]',
            mainDateEnd:   'input[id$="DateEnd"]',
            actionType:    'input[data-action-type]',
            periodOption:  'div.period-option select',
            limit:         'div.limit-option select',
            mainPeriods:   'input[name$="[period]"]',
            datePicker:    '[data-date-format]',
            chartPrefix:   '#chart_',
            statisticsTableData: '#statisticsTableData'
        },
        buttons: {
            exportPdf: '#exportPdf',
            filter:    'button[id$="Filter"]'
        },
        forms: {
            exportFrom: '#exportForm'
        }
    };

    var events = {
        chartConverted: 'chartConverted'
    };

    var defaultValue = {
        datesRange:    'custom',
        limit:         10,
        exportTimeout: 1000
    };

    var reportUrls = {
        businessOverviewDataAction: Routing.generate( 'domain_business_admin_reports_business_overview_data' ),
        keywordsDataAction:         Routing.generate( 'domain_business_admin_reports_keywords_data' ),
        adUsageDataAction:          Routing.generate( 'domain_business_admin_reports_ad_usage_data' ),
        chartExportAction:          Routing.generate( 'domain_business_admin_chart_reports_export' )
    };

    var chartData  = {};
    var chartAjaxCall = {};
    var chartType = {
        keywords: 'keyword',
        ads:      'ads'
    };

    initDatetimePickers();
    initAjaxRequestTracker();

    function updateChartDates( data ) {
        var startDateInput = $( '[name="date[' + data.chartType + '][startDate]"]' );
        var endDateInput = $( '[name="date[' + data.chartType + '][endDate]"]' );
        var startDateFilterInput = $( 'input[id$="' + data.chartType + 'DateStart"]' );
        var endDateFilterInput = $( 'input[id$="' + data.chartType + 'DateEnd"]' );

        startDateInput.val( startDateFilterInput.val() );
        endDateInput.val( endDateFilterInput.val() );
    }

    function loadBusinessInteractionReport( chartBlock ) {
        var data = getInteractionFilterValues( chartBlock );
        var url;

        if ( data.chartType === chartType.keywords ) {
            url = reportUrls.keywordsDataAction;
        } else if ( data.chartType === chartType.ads ) {
            url = reportUrls.adUsageDataAction;
        } else {
            url = reportUrls.businessOverviewDataAction;
        }

        updateChartDates( data );

        if ( chartAjaxCall[ data.chartType ] ) {
            return false;
        }

        $.ajax({
            url:      url,
            data:     data,
            dataType: 'JSON',
            type:     'POST',
            beforeSend: function() {
                chartBlock.find( html.containers.businessOverviewChartContainerId ).html( '' );
                chartBlock.find( html.containers.businessOverviewHintContainerId ).html( '' );
                chartAjaxCall[ data.chartType ] = true;
            },
            success: function( response ) {
                chartBlock.find( html.containers.businessOverviewHintContainerId ).html( response.chartHint );

                if ( data.chartType === chartType.keywords ) {
                    $( html.containers.keywordStatsContainerId ).html( response.stats );
                    $( html.inputs.statisticsTableData ).val( response.stats );
                    loadKeywordsChart( chartBlock, response.keywords, response.searches );
                } else if ( data.chartType === chartType.ads ) {
                    loadAdUsageChart( chartBlock, response.dates, response.clicks, response.impressions );
                } else {
                    loadBusinessOverviewChart( chartBlock, response.dates, response.chart, response.chartTitle );
                }
            },
            complete: function () {
                chartAjaxCall[ data.chartType ] = false;
            }
        });
    }

    function sendChartExportData() {
        $.each( chartData, function( key, value ) {
            $( html.inputs.chartPrefix + key ).val( value );
        });

        $( html.forms.exportFrom ).submit();
    }

    function loadBusinessOverviewChart( chartBlock, dates, chartData, title ) {
        chartBlock.find( html.containers.businessOverviewChartContainerId ).highcharts({
            title: {
                text: title,
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
                layout:        'vertical',
                align:         'right',
                verticalAlign: 'middle',
                borderWidth:   0
            },
            series: [
                {
                    name: title,
                    data: chartData
                }
            ]
        });
    }

    function loadKeywordsChart( chartBlock, keywords, searches) {
        chartBlock.find( html.containers.businessOverviewChartContainerId ).highcharts({
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

    function loadAdUsageChart( chartBlock, dates, clicks, impressions ) {
        chartBlock.find( html.containers.businessOverviewChartContainerId ).highcharts({
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
                layout:        'vertical',
                align:         'right',
                verticalAlign: 'middle',
                borderWidth:   0
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

    function getInteractionFilterValues( chartBlock ) {
        var dateStart    = chartBlock.find( html.inputs.mainDateStart ).val();
        var dateEnd      = chartBlock.find( html.inputs.mainDateEnd ).val();
        var keywordLimit = chartBlock.find( html.inputs.limit ).val();
        var chartType    = chartBlock.find( html.inputs.actionType ).val();
        var period       = chartBlock.find( html.inputs.periodOption ).val();

        return {
            'businessProfileId': businessProfileId,
            'start':             dateStart,
            'end':               dateEnd,
            'datesRange':        defaultValue.datesRange,
            'limit':             keywordLimit ? keywordLimit : defaultValue.limit,
            'chartType':         chartType,
            'periodOption':      period
        };
    }

    handleReportUpdate();

    function handleReportUpdate() {
        $( html.buttons.filter ).on( 'click', function( event ) {
            var chartBlock = $( event.target ).parents( html.containers.chartParentBlockId );

            loadBusinessInteractionReport( chartBlock );
        });

        var parentBlocks = $( html.containers.chartParentBlockId );

        parentBlocks.each(function () {
            var chartBlock = $( this );

            loadBusinessInteractionReport( chartBlock );
        });
    }

    handleExport();

    function handleExport() {
        $( document ).on( 'click', html.buttons.exportPdf, function ( e ) {
            var chartBlocks = $( html.containers.chartParentBlockId );

            if ( checkAjaxCalls() ) {
                $.each( chartBlocks, function() {
                    var chart = $( this ).find( html.containers.businessOverviewChartContainerId );
                    var type  = $( this ).find( html.inputs.actionType ).val();

                    html2canvas( chart[ 0 ] ).then(function( canvas ) {
                        chartData[ type ] = canvas.toDataURL();
                        $( document ).trigger( events.chartConverted );
                    });
                });
            }
        });

        $( document ).on( events.chartConverted, function ( e ) {
            var chartBlocks = $( html.containers.chartParentBlockId );
            var result = true;

            $.each( chartBlocks, function() {
                var type  = $( this ).find( html.inputs.actionType ).val();

                if ( !( chartData.hasOwnProperty( type ) && chartData[ type ] ) ) {
                    result = false;
                    return false;
                }
            });

            if ( result ) {
                setTimeout(function() {
                    sendChartExportData();
                }, defaultValue.exportTimeout);
            }
        });
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

    function handlePeriodChoicesCalendar( item )
    {
        var chartBlock = $( item ).parents( html.containers.chartParentBlockId );
        var period = parseInt( chartBlock.find( html.inputs.mainPeriods + ':checked' ).data( 'month' ) );
        var endDate   = new Date();
        var startDate = new Date();

        startDate.setMonth( startDate.getMonth() - period );

        chartBlock.find( html.inputs.mainDateStart ).data( 'DateTimePicker' ).setDate( startDate );
        chartBlock.find( html.inputs.mainDateEnd ).data( 'DateTimePicker' ).setDate( endDate );
    }

    function initDatetimePickers() {
        $( html.inputs.datePicker ).datetimepicker({
            'pickTime': false,
            'useCurrent' :true,
            'minDate': '1\/1\/1900',
            'maxDate': null,
            'showToday': true,
            'language': 'en',
            'defaultDate': '',
            'disabledDates': [],
            'enabledDates': [],
            'icons': {
                'time': 'fa fa-clock-o',
                'date': 'fa fa-calendar',
                'up': 'fa fa-chevron-up',
                'down': 'fa fa-chevron-down'
            },
            'useStrict': false,
            'sideBySide': false,
            'daysOfWeekDisabled': [],
            'useSeconds': false
        });
    }

    function initAjaxRequestTracker() {
        var parentBlocks = $( html.containers.chartParentBlockId );

        parentBlocks.each(function () {
            var type  = $( this ).find( html.inputs.actionType ).val();

            chartAjaxCall[ type ] = false;
        });
    }

    function checkAjaxCalls()
    {
        var parentBlocks = $( html.containers.chartParentBlockId );
        var result = true;

        parentBlocks.each(function () {
            var type  = $( this ).find( html.inputs.actionType ).val();

            if ( !chartAjaxCall.hasOwnProperty( type ) || chartAjaxCall[ type ] ) {
                result = false;
                return false;
            }
        });

        return result;
    }
});
