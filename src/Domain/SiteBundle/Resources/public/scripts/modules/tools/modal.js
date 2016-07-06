var modal = function() {};

modal.prototype.doubleModal = function() {
    var modalLink = $( '.create-acc' );

    modalLink.on( 'click', function(e) {
        e.preventDefault();

        setTimeout(function() {
            if ( $( '.modal.fade.in' ) ) {
                $( 'body' ).addClass( 'modal-open' );
            }
        }, 500)
    });
};

modal.prototype.run = function() {
    this.doubleModal();
};

$(function () {
    var controller = new modal();
    controller.run();
});
