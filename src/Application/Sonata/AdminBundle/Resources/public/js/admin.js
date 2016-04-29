$(document).ready(function () {
    $('.fancybox-single').fancybox();
    $('select.remove-select2').removeAttr('multiple').select2('destroy');

    FilterSwitcher.$filterContainer = $('.sonata-ba-filter');
    FilterSwitcher.$listContainer = $('.sonata-ba-list');
    FilterSwitcher.$switcher = $('#filter-switch');

});
var FilterSwitcher = {
    open: true,

    $filterContainer: null,
    $listContainer: null,
    $switcher: null,

    switchFilter: function () {
        this.open = !this.open;
        var $imgSwitcher = this.$switcher.find('img'),
            imgSrc = $imgSwitcher.attr('src');

        if (this.open) {
            this.$filterContainer.removeClass('hide');
            this.$listContainer.removeClass('width98');
            $imgSwitcher.attr('src', imgSrc.replace('_open.png', '_close.png'));
        } else {
            this.$filterContainer.addClass('hide');
            this.$listContainer.addClass('width98');
            $imgSwitcher.attr('src', imgSrc.replace('_close.png', '_open.png'));
        }
    }
};