var select = function() {
    this.selectControl = $( '.select-control' );
    this.selectArrow = $( '.select2-selection__arrow' );
};

select.prototype.initSelect = function() {
    var self = this;

    self.selectControl.select2();
    self.selectArrow.hide();
    $( window ).resize(function() {
        self.selectControl.select2();
        self.selectArrow.hide();
    }.bind( this ) );
};

select.prototype.run = function() {
    this.initSelect();
};

$( function () {
    var controller = new select();
    controller.run();
});
