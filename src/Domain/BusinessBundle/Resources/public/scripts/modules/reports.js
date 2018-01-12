define(['jquery', 'bootstrap', 'highcharts', 'tools/spin', 'tools/select', 'business/tools/businessProfileClose', 'jquery-ui'], function( $, bootstrap, highcharts, Spin, select, businessProfileClose ) {
    'use strict';

    //init reports object variables
    var reports = function() {
        this.urls = {
            businessOverviewDataAction: Routing.generate('domain_business_reports_business_overview_data'),
            adUsageDataAction: Routing.generate('domain_business_reports_ad_usage_data'),
            keywordsDataAction: Routing.generate('domain_business_reports_keywords_data')
        };

        this.html = {
            containers: {
                businessOverviewChartContainerId: '#businessOverviewChartContainer',
                businessOverviewStatsContainerId: '#businessOverviewStatisticsContainer',
                adUsageStatsContainerId: '#adUsageStatisticsContainer',
                adUsageChartContainerId: '#adUsageChartContainer',
                keywordChartContainerId: '#keywordChartContainer',
                keywordStatsContainerId: '#keywordStatisticsContainer',
                keywordsLimitContainerId: '#keywordsLimitContainer',
                interactionTypeContainerId: '#interactionTypeContainer',
                interactionHintContainerId: '#interactionHintContainer',
                interactionPeriodContainerId: '#interactionGroupPeriodContainer',
                customDatesContainer: '#customDatesContainer'
            },
            inputs: {
                dateRange:  '#domain_business_bundle_business_report_filter_type_dateRange',
                dateStart:  '#domain_business_bundle_business_report_filter_type_start',
                dateEnd:    '#domain_business_bundle_business_report_filter_type_end',
                limit:      '#domain_business_bundle_business_report_filter_type_limit',
                actionType: '#domain_business_bundle_business_report_filter_type_actionType',
                period:     '#domain_business_bundle_business_report_filter_type_groupPeriod'
            }
        };

        this.spinner = new Spin();
        this.businessProfileClose = new businessProfileClose;

        this.run();
    };

    reports.prototype.handleDatesChange = function()
    {
        var $dateRangeControl = $( this.html.inputs.dateRange );
        var $customDatesWidgetContainer = $( this.html.containers.customDatesContainer );

        $dateRangeControl.on('change', function() {
            if ( $( this ).val() == 'custom' ) {
                $customDatesWidgetContainer.show();
            } else {
                $customDatesWidgetContainer.hide();
            }
        });
    };

    reports.prototype.loadBusinessOverviewReport = function()
    {
        var self = this;

        $.ajax({
            url: self.urls.businessOverviewDataAction,
            data: self.getFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( self.html.containers.businessOverviewChartContainerId ).html( '' );
                $( self.html.containers.businessOverviewStatsContainerId ).html( '' );
                $( self.html.containers.interactionHintContainerId ).html( '' );
                self.showLoader( self.html.containers.businessOverviewChartContainerId );
            },
            success: function( response ) {
                $( self.html.containers.businessOverviewStatsContainerId ).html( response.stats );
                $( self.html.containers.interactionHintContainerId ).html( response.chartHint );
                self.loadBusinessOverviewChart( response.dates, response.chart, response.chartTitle );
            }
        });
    };

    reports.prototype.loadAdUsageReport = function() {
        var self = this;

        $.ajax({
            url: self.urls.adUsageDataAction,
            data: self.getFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( self.html.containers.adUsageChartContainerId ).html( '' );
                $( self.html.containers.adUsageStatsContainerId ).html( '' );
                self.showLoader( self.html.containers.adUsageStatsContainerId );
            },
            success: function( response ) {
                $( self.html.containers.adUsageStatsContainerId ).html( response.stats );
                self.loadAdUsageChart( response.dates, response.clicks, response.impressions );
            }
        });
    };

    reports.prototype.loadKeywordsReport = function()
    {
        var self = this;

        $.ajax({
            url: self.urls.keywordsDataAction,
            data: self.getFilterValues(),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function() {
                $( self.html.containers.keywordChartContainerId ).html( '' );
                $( self.html.containers.keywordStatsContainerId ).html( '' );
                self.showLoader( self.html.containers.keywordChartContainerId );
            },
            success: function( response ) {
                $( self.html.containers.keywordStatsContainerId ).html( response.stats );
                self.loadKeywordsChart( response.keywords, response.searches );
            }
        });
    };

    reports.prototype.loadBusinessOverviewChart = function( dates, chart, title )
    {
        $( this.html.containers.businessOverviewChartContainerId ).highcharts({
            title: {
                text: $( this.html.containers.businessOverviewChartContainerId ).data( 'title' ),
                x: -20 //center
            },
            xAxis: {
                categories: dates
            },
            yAxis: {
                title: {
                    text: $( this.html.containers.businessOverviewChartContainerId ).data( 'y-axis' )
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
                    data: chart
                }
            ]
        });
    };

    reports.prototype.loadAdUsageChart = function( dates, clicks, impressions )
    {
        $( this.html.containers.adUsageChartContainerId ).highcharts({
            title: {
                text: $( this.html.containers.adUsageChartContainerId ).data( 'title' ),
                x: -20 //center
            },
            xAxis: {
                categories: dates
            },
            yAxis: {
                title: {
                    text: $( this.html.containers.adUsageChartContainerId ).data( 'y-axis' )
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
                    name: $( this.html.containers.adUsageChartContainerId ).data( 'series-name-clicks' ),
                    data: clicks
                },
                {
                    name: $( this.html.containers.adUsageChartContainerId ).data( 'series-name-imp' ),
                    data: impressions
                }
            ]
        });
    };

    reports.prototype.loadKeywordsChart = function(keywords, searches)
    {
        $(this.html.containers.keywordChartContainerId).highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: $( this.html.containers.keywordChartContainerId ).data( 'title' )
            },
            xAxis: {
                categories: keywords
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: $( this.html.containers.keywordChartContainerId ).data( 'y-axis' )
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
                name: $(this.html.containers.keywordChartContainerId).data( 'series-name-search' ),
                data: searches
            }]
        });
    };

    reports.prototype.getFilterValues = function()
    {
        var businessProfileId = $( '#businessProfileId' ).val();
        var datesRange = $( this.html.inputs.dateRange ).val();
        var dateStart  = $( this.html.inputs.dateStart ).val();
        var dateEnd    = $( this.html.inputs.dateEnd).val();
        var limit      = $( this.html.inputs.limit ).val();
        var actionType = $( this.html.inputs.actionType ).val();
        var period     = $( this.html.inputs.period ).val();

        return {
            'businessProfileId': businessProfileId,
            'datesRange': datesRange,
            'start': dateStart,
            'end': dateEnd,
            'limit': limit,
            'chartType': actionType,
            'periodOption': period
        };
    };

    reports.prototype.handleReportUpdate = function()
    {
        var self = this;

        $( document ).on( 'click', '.tabs-block li', function() {
            $( self.html.containers.keywordsLimitContainerId ).hide();
            $( self.html.containers.interactionTypeContainerId ).hide();
            $( self.html.containers.interactionPeriodContainerId ).hide();

            if ( $( '.tabs-block li.active' ).find( 'a' ).attr( 'aria-controls' ) == 'overview' ) {
                $( self.html.containers.keywordsLimitContainerId ).show();
                $( self.html.containers.interactionTypeContainerId ).show();
                $( self.html.containers.interactionPeriodContainerId ).show();
            }

            self.refreshActiveReport();
        });

        $( document ).on('change', this.html.inputs.dateRange, function() {
            if ( $( this ).val() !== 'custom' ) {
                self.refreshActiveReport();
            }
        });

        $( document ).on('change', this.html.inputs.limit, function() {
            self.refreshActiveReport();
        });

        $( document ).on('change', this.html.inputs.actionType, function() {
            self.refreshActiveReport();
        });

        $( document ).on('change', this.html.inputs.period, function() {
            self.refreshActiveReport();
        });
    };

    reports.prototype.refreshActiveReport = function()
    {
        var activeTab = $( '.tabs-block li.active' ).find( 'a' ).attr( 'aria-controls' );

        switch ( activeTab ) {
            case 'overview':
                $( this.html.containers.keywordsLimitContainerId ).show();
                $( this.html.containers.interactionTypeContainerId ).show();
                this.loadBusinessOverviewReport();
                this.loadKeywordsReport();
                break;
            case 'ad_usage':
                this.loadAdUsageReport();
                break;
        }
    };

    reports.prototype.initDatePickers = function()
    {
        var self = this;

        $( '.js-datepicker' ).datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function( dateText, inst ) {
                $( this ).val( dateText );
                self.refreshActiveReport();
            }
        });
    };

    reports.prototype.showLoader = function( container )
    {
        this.spinner.show( container.replace( '#', '' ) );
    };

    reports.prototype.handleExport = function()
    {
        var self = this;

        $(document).on('click', '#export-excel', function (e) {
            var filtersData = $.param(self.getFilterValues());
            location.href = $(this).attr('href') + '?' + filtersData;
        });

        $(document).on('click', '#export-pdf', function (e) {
            var filtersData = $.param(self.getFilterValues());
            location.href = $(this).attr('href') + '?' + filtersData;
        });

        $(document).on('click', '#print', function (e) {
            var filterParams = self.getFilterValues();
            filterParams.print = true;

            var filtersData = $.param( filterParams );

            location.href = $( this ).attr( 'href' ) + '?' + filtersData;
        });
    };

    reports.prototype.printDoc = function($document)
    {
        var self = this;

        if (typeof $document.print === 'undefined') {
            setTimeout(function(){
                console.log('timeout!');
                self.printDoc($document);
            }, 1000);
        } else {
            $document.print();
        }
    };

    reports.prototype.initSelects = function ()
    {
        $( this.html.inputs.dateRange ).select2({
            minimumResultsForSearch: -1
        });

        $( this.html.inputs.actionType ).select2({
            minimumResultsForSearch: -1
        });

        $( this.html.inputs.period ).select2({
            minimumResultsForSearch: -1
        });

        $( this.html.inputs.limit ).select2({
            minimumResultsForSearch: -1
        });
    };

    reports.prototype.run = function()
    {
        this.handleDatesChange();
        this.initDatePickers();
        this.handleReportUpdate();
        this.handleExport();
        this.initSelects();

        new select();

        //global variables from index.html.twig scope
        this.loadBusinessOverviewChart( overviewDataDates, overviewDataChart, overviewChartTitle );
        this.loadKeywordsChart( keywordDataChartWord, keywordDataChartSearch );
    };

    return reports;
});
