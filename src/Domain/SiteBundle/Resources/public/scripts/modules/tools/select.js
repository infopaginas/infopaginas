var select = function() {
    this.selectControl = $( '.select-control' );
    this.selectArrow = $( '.select2-selection__arrow' );
    this.tab = $('.tabs-block a');
};

select.prototype.initSelect = function() {
    var self = this;

    self.selectControl.select2({
        placeholder: function(){
            $(this).data('placeholder');
        }
    });
    self.selectArrow.hide();
    $( window ).resize(function() {
        self.selectControl.select2();
        self.selectArrow.hide();
    }.bind( this ) );

    this.tab.click(function (e) {
        e.preventDefault()
        $(this).tab('show')
        self.selectControl.select2();
    })
};

select.prototype.run = function() {
    this.initSelect();
};

$( function () {
    var controller = new select();
    controller.run();
});
