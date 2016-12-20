define(['jquery', 'selectize', 'velocity', 'velocity-ui'], function( $ ) {
    'use strict';

    var headerSearch = $( '#searchBox' );
    var header = $( '.header' );
    var searchButton = $( '#searchButton' );
    var logo = $( '.logo' );
    var navToggle = $( '.nav__toggle' );
    var searchField = $( '.field--search' );
    var geoField = $( '.field--geo' );
    var geoInput = $( '.search-geo' );
    var closeSearch = $( '#close-search' );

    var catToggle = $( '#category-toggle' );
    var cats = $( '.categories' );

    var openCats = [
        { e: cats, p: { translateY: 335 }, o: { duration: 600, easing: "easeOutCubic"} },
    ];

    var closeCats = [
        { e: cats, p: { translateY: 0 }, o: { duration: 600, easing: "easeIn"} },
    ];

    var openSearchSequence = [
        { e: header, p: { height: "190px" }, o: { duration: 300, easing: "easeOut"} },
        { e: logo, p: { translateX: -100 }, o: { duration: 200, sequenceQueue: false} },
        { e: navToggle, p: { translateX: 100 }, o: { duration: 200, sequenceQueue: false } },
        { e: geoField, p: { translateY: 65 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false} },
        { e: geoInput, p: { scale: 1 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false} },
        { e: closeSearch, p: { scale: 1 }, o: { duration: 200 } },
        { e: searchButton, p: { translateX: -200 }, o: { duration: 500, easing: "easeOutCubic" } }
    ];

    var closeSearchSequence = [
        { e: searchButton, p: { translateX: 0 }, o: { duration: 500, easing: "easeOutCubic", sequenceQueue: false} },
        { e: geoField, p: { translateY: 0 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false} },
        { e: geoInput, p: { scale: 0.7 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false} },
        { e: closeSearch, p: { scale: 0 }, o: { duration: 200, easing: "easeOutCubic", sequenceQueue: false} },
        { e: navToggle, p: { translateX: 0 }, o: { duration: 400, easing: "easeOutCubic", sequenceQueue: false } },
        { e: logo, p: { translateX: 0 }, o: { duration: 400, easing: "easeOutCubic", sequenceQueue: false} },
        { e: header, p: { height: "45px" }, o: { duration: 300, easing: "easeOut", sequenceQueue: false} }
    ];


    $.fn.openSearch = function(options) {
        var $this, settings;
        $this = $(this);
        settings = $.extend({
            toggle: true,
            close: false
        }, options);

        if (header.is('.header__search--active')) {

        } else {
            header.addClass( 'header__search--active' );
            $.Velocity.RunSequence(openSearchSequence, { mobileHA: true });
            cats.removeClass( 'categories--opened' );
            $.Velocity.RunSequence(closeCats, { mobileHA: true });
            $( 'body' ).addClass( 'body--no-scroll' );
        }

        if (settings.show) {
            headerSearch.closest( '.header' ).addClass( 'header__search--active' );
            header.addClass( 'header__search--active' );
            $.Velocity.RunSequence(openSearchSequence, { mobileHA: true });
            cats.removeClass( 'categories--opened' );
            $.Velocity.RunSequence(closeCats, { mobileHA: true });
            $( 'body' ).addClass( 'body--no-scroll' );
        }

        if (settings.close) {
            header.removeClass( 'header__search--active' );
            $.Velocity.RunSequence(closeSearchSequence, { mobileHA: true });
            $( 'body' ).removeClass( 'body--no-scroll' );
        }
    };

    if ( !headerSearch.parents( 'form' ).data( 'homepage' ) ) {
        headerSearch.on( 'click', function() {
            header.openSearch();
        });

        closeSearch.on( 'click', function() {
            header.openSearch( {close: true} );
        });
    }

//nav
    var nav = $('#nav');
    var navCloseButton = $('#nav-close');

    var openNav = [
        { e: nav, p: { right: 0 }, o: { duration: 450, easing: "easeOutCubic"} }
    ];

    var closeNav = [
        { e: nav, p: { right: "-100vw" }, o: { duration: 450, easing: "easeIn"} }
    ];

    $.fn.openNav = function() {
        var $this;
        $this = $(this);

        if (nav.is('.nav--opened')) {
            nav.removeClass( 'nav--opened' );
            $.Velocity.RunSequence(closeNav, { mobileHA: true });
            $( 'body' ).removeClass( 'body--no-scroll' );
        } else {
            nav.addClass( 'nav--opened' );
            $.Velocity.RunSequence(openNav, { mobileHA: true });
            $( 'body' ).addClass( 'body--no-scroll' );
        }
    };

    navToggle.on( 'click', function() {
        $(this).openNav();
        cats.removeClass( 'categories--opened' );
        $.Velocity.RunSequence(closeCats, { mobileHA: true });
    });

    navCloseButton.on( 'click', function() {
        $(this).openNav();
    });


//sort

    var sortToggle = $( '#sort-toggle' );
    var filterToggle = $( '#filter-toggle' );
    var sort = $( '.sort' );
    var filter = $( '.filter' );
    var sortButton = $( '.sort-button' );
    var results = $( '.results' );

    $.fn.toggleSorting = function () {
        if ( sortToggle.is( '.active' ) ) {
            sortToggle.removeClass( 'active' );
            sort.removeClass( 'sort--on' );
            results.removeClass( 'active__toggle' );
        } else {
            filterToggle.removeClass( 'active' );
            filter.removeClass( 'filter--on' );
            sortToggle.addClass( 'active' );
            sort.addClass( 'sort--on' );
            results.addClass( 'active__toggle' );
        }
    };

    sortToggle.on( 'click', function () {
        $( this ).toggleSorting()
    });

    $.fn.toggleFiltering = function () {
        if ( filterToggle.is( '.active' ) ) {
            filterToggle.removeClass( 'active' );
            filter.removeClass( 'filter--on' );
            results.removeClass( 'active__toggle' );
        } else {
            sortToggle.removeClass( 'active' );
            sort.removeClass( 'sort--on' );
            filterToggle.addClass( 'active' );
            filter.addClass( 'filter--on' );
            results.addClass( 'active__toggle' );
        }
    };

    filterToggle.on( 'click', function () {
        $( this ).toggleFiltering()
    });

    sortButton.on( 'click', function () {
        $( this ).addClass( 'sort--active' );
        $( this ).siblings().removeClass( 'sort--active' );
    });


    sortButton.on( 'click', function() {
        $(this).addClass('sort--active');
        $(this).siblings().removeClass('sort--active');
    });

// map

    var resultsMap = $( '.results-map' );
    var showMap = $( '#show-map' );
    var hideMap = $( '#hide-map' );
    var adBottom = $( '.ad--bottom' );

    var openMapSequence = [
        { e: adBottom, p: { translateY: 200 }, o: { duration: 400, easing: "easeOutCubic" } },
        { e: showMap, p: { translateY: 140 }, o: { duration: 400, easing: "easeOuCubic", sequenceQueue: false } },
        { e: resultsMap, p: { translateY: "-110vh" }, o: { duration: 600, delay: 200, easing: "easeOutCubic", sequenceQueue: false } },
        { e: hideMap, p: { translateY: -120 }, o: { duration: 200, easing: "easeOutCubic", complete: function() { google.maps.event.trigger(map, 'resize'); } } }
    ];

    var closeMapSequence = [
        { e: hideMap, p: { translateY: 120 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false } },
        { e: resultsMap, p: { translateY: 0 }, o: { duration: 600, delay: 200, easing: "easeOutCubic", sequenceQueue: false } },
        { e: showMap, p: { translateY: 0 }, o: { duration: 300, easing: "easeOutCubic", complete: function() { google.maps.event.trigger(map, 'resize'); } } },
    ];

    var openMapDeskSequence = [
        { e: showMap, p: { translateX: 500 }, o: { duration: 400, easing: "easeOuCubic", sequenceQueue: false } },
        { e: hideMap, p: { translateX: -520 }, o: { duration: 200, easing: "easeOutCubic", complete: function() { google.maps.event.trigger(map, 'resize'); } } }
    ];

    var closeMapDeskSequence = [
        { e: hideMap, p: { translateX: 0 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false } },
        { e: showMap, p: { translateX: 0 }, o: { duration: 300, easing: "easeOutCubic", complete: function() { google.maps.event.trigger(map, 'resize'); } } },
    ];




    $(document).ready( function() {
        if ($( '.ad--bottom' ).is( '.ad--active' )) {
            showMap.addClass( 'floating-offset' );
        }

        var toCompareInput = $('.to-compare__field input');
        var compareSelection = $('#compare-selection-summary');

        $.fn.addToCompare = function() {
            $(this).each(function() {
                var toCompare = $('.to-compare__field input:checked');
                var $this;
                $this = $(this);
                if($(this).is(":checked")) {
                    $('.compare-number').html(toCompare.size());
                } else {
                    $('.compare-number').html(toCompare.size());
                }
                if(toCompare.size() > 0){
                    compareSelection.addClass('to-compare-summary--active');
                } else {
                    compareSelection.removeClass('to-compare-summary--active');
                }
            });
        };

        toCompareInput.on( 'change', function() {
            $( this ).addToCompare();
            $( '.ad--bottom' ).hide();
        });

        var compareRemove = $('.remove-compare-item');
        var compareAdd    = $('.add-compare-item');

        compareRemove.on( 'click', function() {
            var item = $( this ).closest( '.comparison__item' );
            var itemId = item.attr( 'id' );

            $( '[data-item-id = #' + itemId + ']' ).removeAttr( 'disabled' );

            item.velocity( { opacity: 1, top: 100 }, { display: "none" } );
        });

        compareAdd.on( 'click', function() {
            $( this ).attr( 'disabled', 'disabled' );
            var itemId = $( this ).data( 'item-id' );
            $( itemId ).velocity( { opacity: 1, top: 0 }, { display: "flex" } );
        });

        var formInput = $('.form__field input, .form__field textarea');

        formInput.focus(function(){
            $(this).parent().addClass("field-active");
            $(this).parent().find('label').addClass("label-active");
        });

        formInput.blur(function(){
            if($(this).val() === "") {
                $(this).parent().removeClass("field-active field-filled");
                $(this).parent().find('label').removeClass("label-active");
            } else {
                $(this).parent().addClass("field-filled");
            }
        });


        $('.form input, .form textarea').each(function() {
            var $this;
            $this = $(this);
            if ($this.prop('value').length !== 0){
                $this.parent().addClass('field-active');
            }
        });

        var Selectize = require('selectize');
        Selectize.define( 'no-delete', function( options ) {
            this.deleteSelection = function() {};
        });

        $('#domain_business_bundle_business_profile_form_type_subcategories, #domain_business_bundle_business_profile_form_type_areas, #domain_business_bundle_business_profile_form_type_paymentMethods, #domain_business_bundle_business_profile_form_type_tags, #domain_business_bundle_business_profile_form_type_localities, #domain_business_bundle_business_profile_form_type_neighborhoods').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false
        });

        $('#domain_business_bundle_business_profile_form_type_categories, #domain_business_bundle_business_profile_form_type_country, #domain_business_bundle_business_profile_form_type_catalogLocality').selectize({
            plugins: ['remove_button', 'no-delete'],
            delimiter: ',',
            persist: false,
            allowEmptyOption: false
        });

        $('#select-from, #select-to').selectize({
            maxItems: 1
        });

        var modal = $('.modal');
        var modalContent = $('modal__content');
        var modalOutside = $('.modal__outside');

        var openModal = [
            { e: modal, p: { top: 0 }, o: { duration: 400, easing: "easeOuCubic", sequenceQueue: false, display: "flex" } },
            { e: modalOutside, p: { opacity: 1 }, o: { duration: 800, easing: "easeOuCubic", sequenceQueue: false, display: "block" } }
        ];

        var closeModal = [
            { e: modal, p: { top: "100vh" }, o: { duration: 400, easing: "easeOuCubic", sequenceQueue: false, display: "none" } },
            { e: modalOutside, p: { opacity: 0 }, o: { duration: 300, easing: "easeOuCubic", sequenceQueue: false, display: "none" } }
        ];

        $.fn.modalFunc = function(options) {
            $(this).each(function() {
                var $this, settings;
                $this = $(this);
                settings = $.extend({
                    toggle: true,
                    close: false
                }, options);

                if ($this.is('.modal--opened')) {
                    $this.removeClass('modal--opened');
                    $('body').removeClass('body--no-scroll');
                } else {
                    $this.addClass('modal--opened');
                    $('body').addClass('body--no-scroll');
                }
                if (settings.show) {
                    $this.addClass('modal--opened');
                    $('body').addClass('body--no-scroll');
                }
                if (settings.close) {
                    $this.removeClass('modal--opened');
                    $('body').removeClass('body--no-scroll');
                }
            });
        };

        $(document).on('click', '[data-show-modal-id]', function() {
            var $this, modal_id;
            $this = $(this);
            modal_id = $this.data('show-modal-id');

            $("#" + modal_id + ".modal").modalFunc();
            $this.siblings().removeClass('modal--opened');
            return;
        });

        $(document).on('click', '[data-change-modal-id]', function() {
            var $this, modal_id;
            $this = $(this);
            modal_id = $this.data('change-modal-id');

            $this.siblings().removeClass('modal--opened');

            $('.modal--opened').modalFunc({close: true});
            $("#" + modal_id + ".modal").modalFunc();

            return;
        });

        $(document).on('click', '.hide-modal, .modal__outside', function() {
            $('.modal--opened').modalFunc({close: true});
            return;
        });
    });

//
// Categories menu

    $.fn.toggleCat = function() {
        $(this).each(function() {
            var $this;
            $this = $(this);
            if (cats.is('.categories--opened')) {
                cats.removeClass( 'categories--opened' );
                $.Velocity.RunSequence(closeCats, { mobileHA: true });
                $( 'body' ).removeClass( 'body--no-scroll' );
            } else {
                cats.addClass( 'categories--opened' );
                $.Velocity.RunSequence(openCats, { mobileHA: true });
                $( 'body' ).addClass( 'body--no-scroll' );
            }
        });
    };

    catToggle.on( 'click', function() {
        $( this ).toggleCat();
    });


//
// B

    var resultItem = $( '.results__item--normal' );
    var resultClickzone = $( '.item__summary' );
    var resultActions = $( '.item__actions' );
    var detailLink = $( '.detail-link' );
    var itemActive = $('.results__item--active');


//ad

    $('#ad-close').on( 'click', function() {
        $( this ).parent().hide();
        showMap.removeClass( 'floating-offset' );
    });


//media querie conditional scripts

    var mediaquery = window.matchMedia("(min-width: 320px)");
    if (mediaquery.matches) {
        showMap.on( 'click', function() {
            $.Velocity.RunSequence(openMapDeskSequence, { mobileHA: true });
            showMap.removeClass( 'floating-offset' );
            $( 'body' ).addClass( 'body--no-scroll results--map-view' );
        });

        hideMap.on( 'click', function() {
            $.Velocity.RunSequence(closeMapDeskSequence, { mobileHA: true });
            $( 'body' ).removeClass( 'body--no-scroll results--map-view' );
        });

        var sources = document.querySelectorAll('#bgvid source');
        var video = document.querySelector('#bgvid');
        for(var i = 0; i<sources.length;i++) {
            sources[i].setAttribute('src', sources[i].getAttribute('data-src'));
        }

        if (video) {
            video.load();
        }

    } else {
        showMap.on( 'click', function() {
            $.Velocity.RunSequence(openMapSequence, { mobileHA: true });
            showMap.removeClass( 'floating-offset' );
            $( 'body' ).addClass( 'body--no-scroll' );
        });

        hideMap.on( 'click', function() {
            $.Velocity.RunSequence(closeMapSequence, { mobileHA: true });
            $( 'body' ).removeClass( 'body--no-scroll' );
        });

        //custom bg on home for mobile
        var videoContainer = $('.video-header__container');
        var url = videoContainer.data('mbg');
        videoContainer.css('background-image', 'url(' + url + ')');

    }

    var comparisonListToggle = $('#comparison-list-toggle');
    var comparisonListHide = $('#comparison-list-hide');

    comparisonListToggle.on( 'click', function() {
        $( '.main' ).addClass( 'comparison-list--active' );
    });

    comparisonListHide.on( 'click', function() {
        $( '.main' ).removeClass( 'comparison-list--active' );
    });

    var profileNav = $('.profile-nav');
    var profileNavToggle = $('.profile-toggle');

    profileNavToggle.on('click', function() {
        $(this).parent().toggleClass('profile-nav--opened');
    });

    profileNav.on('click', function() {
        $(this).toggleClass('profile-nav--opened');
    });

    $( 'a.button.social-share' ).on( 'click', function(e) {
        e.preventDefault();

        windowPopup( $(this).attr( 'href' ), 500, 300 );
    });

    var jsSocialShares = document.querySelectorAll( '.a.button.social-share' );
    if (jsSocialShares) {
        [].forEach.call(jsSocialShares, function(anchor) {
            anchor.addEventListener( 'click', function(e) {
                e.preventDefault();

                windowPopup( this.href, 500, 300 );
            });
        });
    }

    function windowPopup( url, width, height ) {
        var left = (screen.width / 2) - (width / 2);
        var top = (screen.height / 2) - (height / 2);

        window.open(
            url,
            '',
            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left
        );
    }

});
