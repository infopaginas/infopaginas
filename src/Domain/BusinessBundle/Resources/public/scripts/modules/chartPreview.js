define(['jquery', 'bootstrap', 'highcharts', 'tools/spin', 'tools/select', 'jquery-ui'], function( $, bootstrap, highcharts, Spin, select ) {
    'use strict';

    //init reportPreview object variables
    var reportPreview = function() {
        this.urls = {
            interaction: Routing.generate( 'domain_business_reports_business_overview_data' ),
            ads:         Routing.generate( 'domain_business_reports_ad_usage_data' ),
            keywords:    Routing.generate( 'domain_business_reports_keywords_data' ),
            export:      Routing.generate( 'domain_business_admin_chart_reports_export' )
        };

        this.html = {
            form: {
                exportForm: '#export_form'
            },
            containers: {
                chartContainer: '#chart_container',
                previewBlock:   '#chart_preview_block',
                actionType:     '#action_type_container',
                groupPeriod:    '#group_period_container',
                keywordsLimit:  '#keywords_limit_container',
                customDates:    '#custom_dates_container',
                statsContainerId: '#statisticsTableContainer'
            },
            inputs: {
                dateRange:  '#domain_business_bundle_business_chart_filter_type_dateRange',
                dateStart:  '#domain_business_bundle_business_chart_filter_type_start',
                dateEnd:    '#domain_business_bundle_business_chart_filter_type_end',
                limit:      '#domain_business_bundle_business_chart_filter_type_limit',
                actionType: '#domain_business_bundle_business_chart_filter_type_actionType',
                period:     '#domain_business_bundle_business_chart_filter_type_groupPeriod',
                businessId: '#business_profile_id',
                datePicker: '.js-datepicker',
                statsContainerInput: '#statisticsTableData'
            },
            buttons: {
                export:        '#export_preview',
                addToExport:   '#add-to-export',
                removePreview: 'i[data-remove]'
            },
            messages: {
                errorSpan: '#max-amount-error'
            }
        };

        this.values = {
            customDates: 'custom',
            dateFormat:  'yy-mm-dd',
            imageFormat: 'image/png',
            chartType: {
                keywords: 'keyword',
                ads:      'ads'
            },
            previewChartName:   'chart',
            previewChartNumber: 0,
            maxAmountOfCharts: 15
        };

        this.spinner = new Spin();

        this.run();
    };

    reportPreview.prototype.handleDatesChange = function()
    {
        var self = this;
        var $dateRangeControl = $( this.html.inputs.dateRange );
        var $customDatesWidgetContainer = $( this.html.containers.customDates );

        $dateRangeControl.on('change', function() {
            if ( $( this ).val() === self.values.customDates ) {
                $customDatesWidgetContainer.show();
            } else {
                $customDatesWidgetContainer.hide();
            }
        });
    };

    reportPreview.prototype.loadReport = function()
    {
        var self = this;
        var data = self.getFilterValues();
        var url;

        if ( data.chartType === self.values.chartType.keywords ) {
            url = self.urls.keywords;
        } else if ( data.chartType === self.values.chartType.ads ) {
            url = self.urls.ads;
        } else {
            url = self.urls.interaction;
        }

        $.ajax({
            url:      url,
            data:     data,
            dataType: 'JSON',
            type:     'POST',
            beforeSend: function() {
                $( self.html.containers.chartContainer ).html( '' );
                $( self.html.containers.statsContainerId ).html( '' );
                self.showLoader( self.html.containers.chartContainer );
            },
            success: function( response ) {
                if ( data.chartType === self.values.chartType.keywords ) {
                    $( self.html.containers.statsContainerId ).html( response.stats );
                    $( self.html.inputs.statsContainerInput ).val( response.stats );
                    self.loadKeywordsChart( response.keywords, response.searches );
                } else if ( data.chartType === self.values.chartType.ads ) {
                    self.loadAdUsageChart( response.dates, response.clicks, response.impressions );
                } else {
                    self.loadBusinessOverviewChart( response.dates, response.chart, response.chartTitle );
                }
            }
        });
    };

    reportPreview.prototype.loadBusinessOverviewChart = function( dates, chart, title )
    {
        $( this.html.containers.chartContainer ).highcharts({
            title: {
                text: title,
                x: -20 //center
            },
            xAxis: {
                categories: dates
            },
            yAxis: {
                title: {
                    text: $( this.html.containers.chartContainer ).data( 'y-axis' )
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

    reportPreview.prototype.loadAdUsageChart = function( dates, clicks, impressions )
    {
        $( this.html.containers.chartContainer ).highcharts({
            title: {
                text: $( this.html.containers.chartContainer ).data( 'title-ads' ),
                x: -20 //center
            },
            xAxis: {
                categories: dates
            },
            yAxis: {
                title: {
                    text: $( this.html.containers.chartContainer ).data( 'y-axis' )
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
                    name: $( this.html.containers.chartContainer ).data( 'series-name-click-ads' ),
                    data: clicks
                },
                {
                    name: $( this.html.containers.chartContainer ).data( 'series-name-imp-ads' ),
                    data: impressions
                }
            ]
        });
    };

    reportPreview.prototype.loadKeywordsChart = function( keywords, searches )
    {
        $( this.html.containers.chartContainer ).highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: $( this.html.containers.chartContainer ).data( 'title-keywords' )
            },
            xAxis: {
                categories: keywords
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: $( this.html.containers.chartContainer ).data( 'y-axis-keywords' )
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
                name: $( this.html.containers.chartContainer ).data( 'series-keywords' ),
                data: searches
            }]
        });
    };

    reportPreview.prototype.getFilterValues = function()
    {
        var businessId = $( this.html.inputs.businessId ).val();
        var datesRange = $( this.html.inputs.dateRange ).val();
        var dateStart  = $( this.html.inputs.dateStart ).val();
        var dateEnd    = $( this.html.inputs.dateEnd).val();
        var limit      = $( this.html.inputs.limit ).val();
        var actionType = $( this.html.inputs.actionType ).val();
        var period     = $( this.html.inputs.period ).val();

        return {
            'businessProfileId': businessId,
            'datesRange':        datesRange,
            'start':             dateStart,
            'end':               dateEnd,
            'limit':             limit,
            'chartType':         actionType,
            'periodOption':      period
        };
    };

    reportPreview.prototype.handleReportUpdate = function()
    {
        var self = this;

        $( document ).on('change', this.html.inputs.dateRange, function() {
            if ( $( this ).val() !== self.values.customDates ) {
                self.loadReport();
            }
        });

        $( document ).on('change', this.html.inputs.limit, function() {
            self.loadReport();
        });

        $( document ).on('change', this.html.inputs.actionType, function() {
            var actionType = $( this ).val();

            if ( actionType === self.values.chartType.keywords ) {
                self.hideItem( self.html.containers.groupPeriod );
                self.showItem( self.html.containers.keywordsLimit );
                self.showItem( self.html.containers.statsContainerId );
            } else if ( actionType === self.values.chartType.ads ) {
                self.hideItem( self.html.containers.groupPeriod );
                self.hideItem( self.html.containers.keywordsLimit );
                self.hideItem( self.html.containers.statsContainerId );
            } else {
                self.showItem( self.html.containers.groupPeriod );
                self.hideItem( self.html.containers.keywordsLimit );
                self.hideItem( self.html.containers.statsContainerId );
            }

            self.loadReport();
        });

        $( document ).on('change', this.html.inputs.period, function() {
            self.loadReport();
        });

        $( document ).on('click', this.html.buttons.addToExport, function() {
            var chartBlock = $( self.html.containers.chartContainer );
            var currentAmountOfCharts = self.getLength();

            if ( currentAmountOfCharts >= self.values.maxAmountOfCharts ) {
                self.showItem( self.html.messages.errorSpan )
            } else if ( chartBlock.children().length ) {
                html2canvas( chartBlock[ 0 ] ).then(function( canvas ) {
                    var image = canvas.toDataURL( self.values.imageFormat );

                    self.addPreview( image );
                    self.clearChartBlock();
                });
            }
        });

        $( document ).on('click', this.html.buttons.removePreview, function() {
            var previewBlock = $( this ).parent();

            if (self.getLength() < self.values.maxAmountOfCharts ) {
                self.hideItem( self.html.messages.errorSpan );
            }

            previewBlock.remove();
        });
    };

    reportPreview.prototype.addPreview = function( image )
    {
        var previewBlock = $( this.html.containers.previewBlock );
        var previewNumber = this.values.previewChartNumber;
        var startDate = this.convertDate( $( this.html.inputs.dateStart ).val() );
        var endDate = this.convertDate( $( this.html.inputs.dateEnd ).val() );
        var statisticsId = 'statisticsTableData[' + previewNumber + ']';
        var previewName  = this.values.previewChartName + '[' + previewNumber + ']';
        var startDateInput = '<input type="hidden" name="date[' + previewNumber + '][startDate]" value="' + startDate + '"/>';
        var endDateInput = '<input type="hidden" name="date[' + previewNumber + '][endDate]" value="' + endDate + '"/>';
        var statisticsTableData = '<input type="hidden" name="' + statisticsId + '" value/>';

        var imageBlock = $(
            '<li>' +
                '<i class="fa fa-trash" aria-hidden="true" data-remove></i>' +
                '<img src="' + image + '">' +
                '<input name="' + previewName + '" type="hidden" value="' + image + '">' +
                 startDateInput +
                 endDateInput +
                 statisticsTableData +
            '</li>' );

        previewBlock.append( imageBlock );

        if( $( this.html.containers.statsContainerId ).is( ':visible' ) ) {
            $( '[name="' + statisticsId + '"]' ).val( $( this.html.inputs.statsContainerInput ).val() );
            this.hideItem( this.html.containers.statsContainerId );
        }

        this.values.previewChartNumber++;
    };

    reportPreview.prototype.convertDate = function ( string ) {
        var dateArray = string.split('-');

        return dateArray[0] + '-' + dateArray[2] + '-' + dateArray[1];
    };

    reportPreview.prototype.clearChartBlock = function()
    {
        $( this.html.containers.chartContainer ).html( '' );
    };

    reportPreview.prototype.initDatePickers = function()
    {
        var self = this;

        $( self.html.inputs.datePicker ).datepicker({
            dateFormat: self.values.dateFormat,
            onSelect: function( dateText, inst ) {
                $( this ).val( dateText );
                self.loadReport();
            }
        });
    };

    reportPreview.prototype.showLoader = function( container )
    {
        this.spinner.show( container.replace( '#', '' ) );
    };

    reportPreview.prototype.handleExport = function()
    {
        var self = this;

        $( document ).on('click', self.html.buttons.export, function ( e ) {
            e.preventDefault();

            var previewBlock = $( self.html.containers.previewBlock );

            if ( previewBlock.children().length ) {
                $( self.html.form.exportForm ).submit();
                $( self.html.containers.previewBlock ).html( '' );
            }
        });
    };

    reportPreview.prototype.initSelects = function ()
    {
        var selectParams = {
            minimumResultsForSearch: -1
        };

        $( this.html.inputs.dateRange ).select2( selectParams );
        $( this.html.inputs.actionType ).select2( selectParams );
        $( this.html.inputs.period ).select2( selectParams );
        $( this.html.inputs.limit ).select2( selectParams );
    };

    reportPreview.prototype.showItem = function ( item )
    {
        $( item ).removeClass( 'display-none' );
    };

    reportPreview.prototype.hideItem = function ( item )
    {
        $( item ).addClass( 'display-none' );
    };

    reportPreview.prototype.run = function()
    {
        this.handleDatesChange();
        this.initDatePickers();
        this.handleReportUpdate();
        this.handleExport();
        this.initSelects();

        new select();

        this.loadReport();
    };

    reportPreview.prototype.getLength = function () {
       return $( this.html.containers.previewBlock ).children().length;
    };

    return reportPreview;
});
