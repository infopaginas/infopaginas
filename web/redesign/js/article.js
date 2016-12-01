$(document).ready(function() {
    $('.slider').slick({
      autoplay: true,
      autoplaySpeed: 5000,
      arrows: true,
      dots: false,
      responsive: true,
      adaptiveHeight: true,
      variableWidth: false,
      slidesToShow: 1,
      prevArrow: '<span class="arrow prev"></span>',
      nextArrow: '<span class="arrow next"></span>',
      responsive: [
        {
          breakpoint: 768,
          settings: {
            dots: true,
            arrows: false
          }
        }
      ]
    }); 

    $('.slider-nav').slick({
      slidesToShow: 8,
      slidesToScroll: 1,
      asNavFor: '.slider',
      arrows: false,
      dots: false,
      centerMode: true,
      focusOnSelect: true
    });
});
