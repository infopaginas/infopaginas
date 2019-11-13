$( document ).ready( function() {
    var html = {
        containers: {
            chartBlock:   'div[id$="ChartContainer"]',
            hintBlock:    'div[id$="ChartHintContainer"]',
            statBlock:    'div[id$="ChartStatContainer"]',
            keywordBlock: '#' + uniqueId + '_keywordChartContainer'
        },
        inputs: {
            mainDateStart: '#' + uniqueId + '_mainReportFiltersDateStart',
            mainDateEnd:   '#' + uniqueId + '_mainReportFiltersDateEnd',
            periodOption:  '#' + uniqueId + '_periodOption',
            keywordLimit:  '#' + uniqueId + '_keywordReportLimit',
            mainPeriods:   'input[name="' + uniqueId + '_mainReportFilters[period]"]',
            datePicker:    '[data-date-format]'
        },
        buttons: {
            exportExcel: '[data-export-type = "export-excel"]',
            exportPdf:   '[data-format = "pdf"]',
            print:       '[data-export-type = "print"]',
            filter:      'button[id$="Filter"]'
        },
        tabs: {
            mainReportTab: 'a[href="#tab_' + uniqueId + '_6"]'
        },
        data: {
            chartType: 'chart-type'
        },
        forms: {
            exportFrom: '#exportForm'
        }
    };

    var chartData  = {};
    var chartAjaxCall = {};

    var events = {
        chartConverted: 'chartConverted'
    };

    var defaultValue = {
        datesRange:    'custom',
        limit:         10,
        exportTimeout: 1000
    };

    var chartType = {
        keywords       : 'keyword',
        ads            : 'ads',
        socialNetworks : 'social_networks',
        impressions    : 'impressions'
    };

    var reportUrls = {
        businessOverviewDataAction: Routing.generate( 'domain_business_admin_reports_business_overview_data' ),
        keywordsDataAction:         Routing.generate( 'domain_business_admin_reports_keywords_data' ),
        socialNetworksDataAction:   Routing.generate( 'domain_business_admin_reports_social_networks_data' ),
        adUsageDataAction:          Routing.generate( 'domain_business_admin_reports_ad_usage_data' )
    };

    initDatetimePickers();
    initAjaxRequestTracker();

    function initAjaxRequestTracker() {
        var chartBlocks = $( html.containers.chartBlock );

        chartBlocks.each(function () {
            var type  = getChartType( $( this ) );

            chartAjaxCall[ type ] = false;
        });
    }

    function checkAjaxCalls() {
        var chartBlocks = $( html.containers.chartBlock );
        var result = true;

        chartBlocks.each(function () {
            var type  = getChartType( $( this ) );

            if ( !chartAjaxCall.hasOwnProperty( type ) || chartAjaxCall[ type ] ) {
                result = false;
                return false;
            }
        });

        return result;
    }

    function getMainFilterValues( chartBlock ) {
        var dateStart    = $( html.inputs.mainDateStart ).val();
        var dateEnd      = $( html.inputs.mainDateEnd ).val();
        var keywordLimit = $( html.inputs.keywordLimit ).val();
        var period       = $( html.inputs.periodOption ).val();
        var chartType    = '';

        if (chartBlock) {
            chartType = getChartType( chartBlock );
        }

        return {
            'businessProfileId': businessProfileId,
            'start': dateStart,
            'end':   dateEnd,
            'datesRange': defaultValue.datesRange,
            'limit': keywordLimit,
            'chartType': chartType,
            'periodOption': period
        };
    }

    function loadBusinessReport( chartBlock ) {
        var data = getMainFilterValues( chartBlock );
        var url;

        if ( data.chartType === chartType.keywords ) {
            url = reportUrls.keywordsDataAction;
        } else if ( data.chartType === chartType.socialNetworks) {
            url = reportUrls.socialNetworksDataAction;
        } else if ( data.chartType === chartType.ads ) {
            url = reportUrls.adUsageDataAction;
        } else {
            url = reportUrls.businessOverviewDataAction;
        }

        if ( chartAjaxCall[ data.chartType ] ) {
            return false;
        }

        $.ajax({
            url:      url,
            data:     data,
            dataType: 'JSON',
            type:     'POST',
            beforeSend: function() {
                chartBlock.html( '' );
                chartBlock.parent().find( html.containers.hintBlock ).html( '' );
                chartBlock.parent().find( html.containers.statBlock ).html( '' );

                chartAjaxCall[ data.chartType ] = true;
            },
            success: function( response ) {
                chartBlock.parent().find( html.containers.hintBlock ).html( response.chartHint );

                if ( data.chartType === chartType.keywords ) {
                    chartBlock.parent().find( html.containers.hintBlock ).html( response.stats );
                    loadKeywordsChart( chartBlock, response.keywords, response.searches );
                } else if ( data.chartType === chartType.socialNetworks ) {
                    loadSocialNetworksChart( chartBlock, response.socialNetworks, response.clicks );
                } else if ( data.chartType === chartType.ads ) {
                    loadAdUsageChart( chartBlock, response.dates, response.clicks, response.impressions );
                } else {
                    loadBusinessOverviewChart( chartBlock, response.dates, response.chart, response.chartTitle );

                    if ( data.chartType === chartType.impressions  ) {
                        $( html.containers.statBlock ).first().html( response.stats );
                    }
                }
            },
            complete: function () {
                chartAjaxCall[ data.chartType ] = false;
            }
        });
    }

    function loadBusinessOverviewChart( chartBlock, dates, chartData, title ) {
        chartBlock.highcharts({
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

    function loadKeywordsChart( chartBlock, keywords, searches ) {
        chartBlock.highcharts({
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

    function loadSocialNetworksChart( chartBlock, socialNetworks, clicks ) {
        chartBlock.highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Social Networks'
            },
            xAxis: {
                categories: formatSocialNetworkNames(socialNetworks)
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
                name: 'Clicks',
                data: clicks
            }]
        });
    }

    function loadAdUsageChart( chartBlock, dates, clicks, impressions ) {
        chartBlock.highcharts({
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

    handleReportUpdate();

    function handleReportUpdate() {
        $( html.buttons.filter ).on( 'click', function() {
            initAllCharts();
        });
        $( html.tabs.mainReportTab ).on( 'click', function() {
            initAllCharts();
        });
    }

    function initAllCharts() {
        var chartBlocks = $( html.containers.chartBlock );

        chartBlocks.each(function () {
            loadBusinessReport( $( this ) );
        });
    }

    handleExport();

    function handleExport() {
        $( document ).on( 'click', html.buttons.exportPdf, function () {
            var print = $( this ).data( 'export-type' );
            var chartBlocks = $( html.containers.chartBlock );

            if ( checkAjaxCalls() ) {
                $.each( chartBlocks, function() {
                    var chart = $( this );
                    var type  = getChartType( $( this ) );

                    html2canvas( chart[ 0 ] ).then(function( canvas ) {
                        chartData[ type ] = canvas.toDataURL();
                        $( document ).trigger( events.chartConverted, [print] );
                    });
                });
            }
        });


        $( document ).on('click', html.buttons.exportExcel, function () {
            var filterParams = getMainFilterValues();
            var exportRoute  = $( this ).data( 'route' );

            filterParams.format = $( this ).data( 'format' );

            window.open( Routing.generate( exportRoute, filterParams ) );
        });

        $( document ).on( events.chartConverted, function ( e, print ) {
            var chartBlocks = $( html.containers.chartBlock );
            var result = true;

            $.each( chartBlocks, function() {
                var type  = getChartType( $( this ) );

                if ( !( chartData.hasOwnProperty( type ) && chartData[ type ] ) ) {
                    result = false;
                    return false;
                }
            });

            if ( result ) {
                setTimeout(function() {
                    sendChartExportData( print );
                }, defaultValue.exportTimeout);
            }
        });
    }

    function sendChartExportData( print ) {
        var filters = getMainFilterValues();

        $( html.forms.exportFrom ).html( '' );

        $.each( chartData, function( key, value ) {
            $( '<input />' ).attr( 'type', 'hidden' )
                .attr( 'name' , 'chart[' + key + ']' )
                .attr( 'value', value )
                .appendTo( html.forms.exportFrom );

            $( '<input />' ).attr( 'type', 'hidden' )
                .attr( 'name' , 'date[' + key + '][startDate]' )
                .attr( 'value', filters.start )
                .appendTo( html.forms.exportFrom );

            $( '<input />' ).attr( 'type', 'hidden' )
                .attr( 'name' , 'date[' + key + '][endDate]' )
                .attr( 'value', filters.end )
                .appendTo( html.forms.exportFrom );

            $( '<input />' ).attr( 'type', 'hidden' )
                .attr( 'name' , 'businessId' )
                .attr( 'value', businessProfileId )
                .appendTo( html.forms.exportFrom );

            if ( key === chartType.keywords ) {
                var keywordStatBlock = $( html.containers.keywordBlock ).parent().find( html.containers.statBlock );

                $( '<input />' ).attr( 'type', 'hidden' )
                    .attr( 'name' , 'statisticsTableData[keyword]' )
                    .attr( 'value', keywordStatBlock.html() )
                    .appendTo( html.forms.exportFrom );
            }
        });

        if ( print ) {
            $( '<input />' ).attr( 'type', 'hidden' )
                .attr( 'name' , 'print' )
                .attr( 'value', 1 )
                .appendTo( html.forms.exportFrom );
        }

        $( html.forms.exportFrom ).submit();
    }

    handlePeriodChoicesUpdate();

    function handlePeriodChoicesUpdate()
    {
        $( document ).on( 'ifChecked ifUnchecked', html.inputs.mainPeriods, function () {
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

    function getChartType( chartBlock ) {
        return chartBlock.data( html.data.chartType );
    }

    function formatSocialNetworkNames(socialNetworks) {
        socialNetworks.forEach(function(socialNetwork, index) {
            socialNetworks[index] = getSocialNetworkName(socialNetwork);
        });
        return socialNetworks;
    }

    function getSocialNetworkName(socialNetworkVisit) {
        return jsUcfirst(socialNetworkVisit.split('V')[0]);
    }

    function jsUcfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
});
