define(['jquery', 'select2'], function($, select2 ) {
    'use strict';

    var select = function() {
        this.selectControl = $( '.select-control' );
        this.selectArrow = $( '.select2-selection__arrow' );
        this.tab = $('.tabs-block a');
        this.initSelect();
    };

    select.prototype.initSelect = function() {


            var self = this;

            self.selectControl.select2({
                minimumResultsForSearch: -1,
                placeholder: function(){
                    $(this).data('placeholder');
                }
            });

            self.selectArrow.hide();
            self.selectControl.hide();

            this.tab.click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                self.selectControl.select2();
            })


    };

    return select;
});
