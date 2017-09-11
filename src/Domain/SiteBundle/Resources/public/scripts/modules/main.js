define(['jquery', 'tools/reportTracker', 'selectize', 'velocity', 'velocity-ui', 'select2'], function( $, ReportTracker ) {
    'use strict';

    var reportTracker = new ReportTracker;

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
        { e: navToggle, p: { translateX: 100 }, o: { duration: 200, sequenceQueue: false} },
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

    $( document ).on( 'click', function (e) {
        var navToggleButton = $( '#nav-toggle' );

        if ( !nav.is(e.target) && nav.has(e.target).length === 0 &&
            !navToggleButton.is(e.target) && navToggleButton.has(e.target).length === 0 ) {
            nav.removeClass( 'nav--opened' );
            $.Velocity.RunSequence(closeNav, { mobileHA: true });
            $( 'body' ).removeClass( 'body--no-scroll' );
        }
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

    // search in map controls
    var autoSearchMap = $( '#auto-search-in-map' );
    var redoSearchMap = $( '#redo-search-in-map' );

    function getMapTranslateY() {
        var toolBar    = $( '.toolbar' );
        var translateY = toolBar.position().top + toolBar.height() - resultsMap.position().top;
        var mapHeight  = $( window ).height() - (toolBar.position().top + toolBar.height());

        resultsMap.css( 'height', mapHeight );
        resultsMap.css( 'bottom', translateY );

        return translateY  + 'px';
    }

    var openMapSequence = [
        { e: showMap, p: { translateX: 0, translateY: 120 }, o: { duration: 400, easing: "easeOutCubic", complete: triggerMapResize } },
        { e: resultsMap, p: { translateY: function() {return getMapTranslateY()} }, o: { duration: 600, delay: 200, easing: "easeOutCubic", sequenceQueue: false } },
        { e: hideMap, p: { translateX: 0, translateY: -120 }, o: { duration: 200, easing: "easeOutCubic", complete: openMapSequenceHideBlock } }
    ];

    var closeMapSequence = [
        { e: hideMap, p: { translateX: 0, translateY: 120 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false, complete: closeMapSequenceHideBlock } },
        { e: resultsMap, p: { translateX: 0, translateY: 0 }, o: { duration: 600, delay: 200, easing: "easeOutCubic", sequenceQueue: false } },
        { e: showMap, p: { translateX: 0, translateY: 0 }, o: { duration: 300, easing: "easeOutCubic", complete: triggerMapResize } },
    ];

    var openMapDeskSequence = [
        { e: showMap, p: { translateX: 500, translateY: 0 }, o: { duration: 200, easing: "easeOuCubic", sequenceQueue: false } },
        { e: hideMap, p: { translateX: -520, translateY: 0 }, o: { duration: 200, easing: "easeOutCubic", complete: triggerMapResize } }
    ];

    var closeMapDeskSequence = [
        { e: hideMap, p: { translateX: 0, translateY: 0 }, o: { duration: 300, easing: "easeOutCubic", sequenceQueue: false } },
        { e: showMap, p: { translateX: 0, translateY: 0 }, o: { duration: 300, easing: "easeOutCubic", complete: triggerMapResize } },
    ];

    var resizeSequenceDevice = [
        { e: showMap, p: { translateX: 0, translateY: 0 }, o: { duration: 0, easing: "easeOutCubic" } },
        { e: hideMap, p: { translateX: 0, translateY: 120 }, o: { duration: 0, easing: "easeOutCubic" } },
    ];

    var resizeSequenceDesktop = [
        { e: showMap, p: { translateX: 500, translateY: 0 }, o: { duration: 0, easing: "easeOutCubic" } },
        { e: hideMap, p: { translateX: 0, translateY: 0 }, o: { duration: 0, easing: "easeOutCubic" } },
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
            var businessId = itemId.split( '-' ).slice( -1 )[0] ;

            $( '[data-item-id = #' + itemId + ']' ).removeAttr( 'disabled' );

            reportTracker.trackEvent( 'removeCompareButton', businessId );

            item.velocity( { opacity: 1, top: 100 }, { display: "none" } );
        });

        compareAdd.on( 'click', function() {
            $( this ).attr( 'disabled', 'disabled' );
            var itemId = $( this ).data( 'item-id' );
            var businessId = itemId.split( '-' ).slice( -1 )[0] ;
            reportTracker.trackEvent( 'addCompareButton', businessId );
            $( itemId ).velocity( { opacity: 1, top: 0 }, { display: "flex" } );
        });

        var formInputSelector         = '.form__field input, .form__field textarea';
        var formInput                 = $( formInputSelector );
        var formInputEmailSelector    = '.form__field input[type="email"]';
        var formInputPasswordSelector = '.form__field input[type="password"]';
        var loginForm                 = $( '.login-form' );

        $.fn.checkInputValue = function () {
            if ( $( this ).val() ) {
                $( this ).parent().addClass( 'field-active' );
            } else {
                $( this ).parent().removeClass( 'field-active field-filled' );
                $( this ).parent().find( 'label' ).removeClass( 'label-active' );
            }
        };

        formInput.each(function () {
            $( this ).checkInputValue();
        });

        if ( loginForm.length > 0 && loginForm.find( formInputEmailSelector ).val() ) {
            loginForm.find( formInputPasswordSelector ).parent().addClass( 'field-active' );
        }

        $( document).on( 'focus', formInputSelector, function () {
            $(this).parent().addClass( 'field-active' );
            $(this).parent().find( 'label' ).addClass( 'label-active' );
        });

        $( document).on( 'blur', formInputSelector, function () {
            $( this ).checkInputValue();
        });

        $(document).on('input', formInputEmailSelector, function () {
            var form = $( this ).parents( 'form' );

            if ( $( this ).val() ) {
                if ( navigator.userAgent.toLowerCase().indexOf( 'chrome' ) >= 0 ) {
                    form.find( formInputPasswordSelector + ':-webkit-autofill' ).each(function () {
                        $( this ).parent().addClass( 'field-active' );
                    });
                } else {
                    setTimeout(function () {
                        form.find( formInputPasswordSelector ).each(function () {
                            if ( $( this ).val() ) {
                                $( this ).parent().addClass( 'field-active' );
                            }
                        });
                    }, 50);
                }
            }
        });

        $('.form input, .form textarea').each(function() {
            var $this;
            $this = $(this);
            if ($this.prop('value').length !== 0){
                $this.parent().addClass('field-active');
            }
        });

        $('#domain_business_bundle_business_profile_form_type_areas, #domain_business_bundle_business_profile_form_type_paymentMethods, #domain_business_bundle_business_profile_form_type_tags, #domain_business_bundle_business_profile_form_type_localities, #domain_business_bundle_business_profile_form_type_neighborhoods').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: true
        });

        var singleSelects = $('#domain_business_bundle_business_profile_form_type_country, #domain_business_bundle_business_profile_form_type_catalogLocality, #domain_page_bundle_feedback_form_type_subject');

        singleSelects.select2();

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
                    $('.main__container').removeClass('container__no-scroll');
                } else {
                    $this.addClass('modal--opened');
                    $('body').addClass('body--no-scroll');
                    $('.main__container').addClass('container__no-scroll');
                }
                if (settings.show) {
                    $this.addClass('modal--opened');
                    $('body').addClass('body--no-scroll');
                    $('.main__container').addClass('container__no-scroll');
                }
                if (settings.close) {
                    $this.removeClass('modal--opened');
                    $('body').removeClass('body--no-scroll');
                    $('.main__container').removeClass('container__no-scroll');
                }
            });
        };

        $(document).on('click', '[data-show-modal-id]', function() {
            var $this, modal_id;
            $this = $(this);
            modal_id = $this.data('show-modal-id');

            $("#" + modal_id + ".modal").modalFunc();
            $this.siblings().removeClass('modal--opened');

            if ( modal_id == 'writeReviewModal' ) {
                handleReportTracker( 'reviewClick' );
            }

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
                $( '.main__container' ).removeClass( 'container__no-scroll' );
            } else {
                cats.addClass( 'categories--opened' );
                $.Velocity.RunSequence(openCats, { mobileHA: true });
                $( 'body' ).addClass( 'body--no-scroll' );
                $( '.main__container' ).addClass( 'container__no-scroll' );
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
    var searchFloatBottom = $( '#search-float-bottom' );
    var hideBannerButton  = $( '#ad-close' );
    var searchResultBlock = $( '#searchResults' );

    hideBannerButton.on( 'click', function() {
        $( this ).parent().remove();
        showMap.removeClass( 'floating-offset' );
        searchResultBlock.removeClass( 'float-ad-spacing' );
    });

    if ( searchFloatBottom.length ) {
        var googleTagBlock = searchFloatBottom.find( 'div:nth-child(2)' );

        if ( googleTagBlock.is( ':visible' ) ) {
            hideBannerButton.removeClass( 'hidden' );
            searchResultBlock.addClass( 'float-ad-spacing' );
        } else {
            hideBannerButton.addClass( 'hidden' );
            searchResultBlock.removeClass( 'float-ad-spacing' );
        }

        googletag.pubads().addEventListener( 'slotRenderEnded', function( event ) {
            if ( googleTagBlock.length && event.slot.getSlotElementId() == googleTagBlock.attr( 'id' ) ) {
                if ( $( '#search-float-bottom' ).length ) {
                    if ( event.isEmpty ) {
                        hideBannerButton.addClass( 'hidden' );
                        searchResultBlock.removeClass( 'float-ad-spacing' );
                    } else {
                        hideBannerButton.removeClass( 'hidden' );
                        searchResultBlock.addClass( 'float-ad-spacing' );
                    }
                } else {
                    googletag.destroySlots( [event.slot] );
                }
            }
        });
    }

//media querie conditional scripts

    var mediaquery = window.matchMedia("(min-width: 804px)");
    var mapContainer = '#map';
    var hasMap = $( mapContainer ).length;

    if (mediaquery.matches) {
      var mapStateSize = 'desktop';
      var sources = document.querySelectorAll('#bgvid source');
      var video = document.querySelector('#bgvid');
      
      for (var i = 0; i<sources.length;i++) {
          sources[i].setAttribute('src', sources[i].getAttribute('data-src'));
      }

      if (video) {
          video.load();
      }

      searchFloatBottom.remove();
    } else {
      var mapStateSize = 'device';
    }

    var mapState = 'default';

    showMap.on( 'click', function() {
        var mediaquery = window.matchMedia( '(min-width: 804px)' );

        if ( mediaquery.matches ) {
            $.Velocity.RunSequence( openMapDeskSequence, { mobileHA: true } );
            showMap.removeClass( 'floating-offset' );
            $( 'body' ).addClass( 'body--no-scroll results--map-view' );
            $( '.dropdown-call' ).addClass( 'dropdown-call-button-additional' );
            $( '.featured-categories__list' ).addClass( 'resize-categories-list' );
            $( '.filter__item' ).addClass( 'filter__item-resize' );
            $( '.main' ).addClass( 'main-results-resize' );
        } else {
            $.Velocity.RunSequence( openMapSequence, { mobileHA: true } );
            $( 'body' ).addClass( 'body--no-scroll' );
            $( '.sort__options' ).removeClass( 'sort--on' );
            $( '.sort__options' ).removeClass( 'filter--on' );
        }

        mapState = 'expanded';
    });

    hideMap.on( 'click', function() {
        var mediaquery = window.matchMedia( '(min-width: 804px)' );

        if ( mediaquery.matches ) {
            $.Velocity.RunSequence( closeMapDeskSequence, { mobileHA: true } );
            $( 'body' ).removeClass( 'body--no-scroll results--map-view' );
            $( '.dropdown-call' ).removeClass( 'dropdown-call-button-additional' );
            $( '.featured-categories__list' ).removeClass( 'resize-categories-list' );
            $( '.filter__item' ).removeClass( 'filter__item-resize' );
            $( '.main' ).removeClass( 'main-results-resize' );
        } else {
            $.Velocity.RunSequence( closeMapSequence, { mobileHA: true } );
            $( 'body' ).removeClass( 'body--no-scroll' );

            if ( !$( '#filter-toggle' ).attr( 'disabled' ) ) {
                $( '.sort__options' ).addClass( 'sort--on' );
                $( '.sort__options' ).addClass( 'filter--on' );
            }
        }

        mapState = 'default';
    });

    autoSearchMap.on( 'click', function() {
        switchAutoSearchInMapControl();
    });

    $( window ).resize(function() {
        var mediaQuery = window.matchMedia( "(min-width: 804px)" );
        var mediaQueryTablet = window.matchMedia( "(min-width: 740px)" );

        if ( mapStateSize == 'desktop' ) {
            // desktop -> desktop
            // desktop -> device
            $.Velocity.RunSequence( resizeSequenceDesktop, { mobileHA: true } );
            $.Velocity.RunSequence( closeMapDeskSequence, { mobileHA: true } );
            $( 'body' ).removeClass( 'body--no-scroll results--map-view' );
            $( '.dropdown-call' ).removeClass( 'dropdown-call-button-additional' );
        } else if ( mediaQuery.matches ) {
            // device -> desktop
            $.Velocity.RunSequence( resizeSequenceDevice, { mobileHA: true } );
            $.Velocity.RunSequence( closeMapSequence, { mobileHA: true } );
            $( 'body' ).removeClass( 'body--no-scroll' );
        } else if ( !mediaQueryTablet.matches ) {
            // device -> device
            if ( mapState == 'expanded' ) {
                resultsMap.css( 'transform', 'translateY(' + getMapTranslateY()  + ')' );
            }
        } else {
            // device -> tablet
            $.Velocity.RunSequence( closeMapSequence, { mobileHA: true } );
        }

        if ( mediaQuery.matches ) {
            mapStateSize = 'desktop';
        } else {
            mapStateSize = 'device';
        }
    });

    if ( hasMap ) {
        triggerMapRequestedIfVisible();

        $( window ).scroll(function() {
            triggerMapRequestedIfVisible();
        });
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

        handleReportTracker( 'facebookShare' );

        windowPopup( $( this ).data( 'href' ), 500, 300 );
    });

    if ( typeof twttr !== 'undefined' && twttr ) {
        twttr.ready(
            function ( twttr ) {
                $( 'a.button.button-share--twitter' ).on( 'click', function(e) {
                    twttr.events.trigger( 'click', {});
                    e.preventDefault();

                    windowPopup( $( this ).data( 'href' ), 500, 300 );
                });

                twttr.events.bind( 'click', function(event) {
                    handleReportTracker( 'twitterShare' );
                });
            }
        );
    }

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

    function handleReportTracker( type ) {
        var businessProfile = $( '#businessProfileName' );

        if ( businessProfile ) {
            var businessProfileId = businessProfile.data( 'business-profile-id' );

            if ( businessProfileId ) {
                reportTracker.trackEvent( type, businessProfileId );
            }
        }
    }

    //call
    var phoneCall = '.phone-call';

    $( document ).on( 'click', phoneCall, function() {
        var itemId = $( this ).data( 'id' );
        var type = $( this ).data( 'type' );

        reportTracker.trackEvent( type, itemId );
    });

    $( 'video' ).one( 'play', function() {
        handleReportTracker( 'videoWatched' );
    });

    //slider
    window.onload = function() {
        $( document ).trigger( 'resize' );
        $( '.section--slider' ).removeClass( 'hide-before' );
        $( '.panorama-frame' ).removeClass( 'hide-before' );
    };

    // map controls
    $( document ).on( 'disableAutoSearchInMap', function() {
        disableAutoSearchInMap();
    });

    function showDeviceMapControlButtons() {
        autoSearchMap.css( 'transform', 'translateX(0px) translateY(45px)' );
        redoSearchMap.css( 'transform', 'translateX(0px) translateY(45px)' );

        autoSearchMap.removeClass( 'hidden' );

        if ( !checkAutoSearchInMapEnabled() ) {
            redoSearchMap.removeClass( 'hidden' );
        }
    }

    function showDesktopMapControlButtons() {
        autoSearchMap.css( 'transform', 'translateX(0px) translateY(0px)' );
        redoSearchMap.css( 'transform', 'translateX(0px) translateY(0px)' );

        autoSearchMap.removeClass( 'hidden' );

        if ( !checkAutoSearchInMapEnabled() ) {
            redoSearchMap.removeClass( 'hidden' );
        }
    }

    function hideMapControlButtons() {
        autoSearchMap.addClass( 'hidden' );
        redoSearchMap.addClass( 'hidden' );
    }

    function switchAutoSearchInMapControl() {
        if ( checkAutoSearchInMapEnabled() ) {
            disableAutoSearchInMap();
        } else {
            enableAutoSearchInMap();
        }
    }

    function enableAutoSearchInMap() {
        var checker = autoSearchMap.find( 'i' );

        checker.removeClass( 'fa-square-o' );
        checker.addClass( 'fa-check' );

        redoSearchMap.addClass( 'hidden' );

        $( document ).trigger( 'autoSearchRequestEnabled' );
    }

    function disableAutoSearchInMap() {
        var checker = autoSearchMap.find( 'i' );

        checker.removeClass( 'fa-check' );
        checker.addClass( 'fa-square-o' );

        redoSearchMap.removeClass( 'hidden' );

        $( document ).trigger( 'autoSearchRequestDisabled' );
    }

    function checkAutoSearchInMapEnabled() {
        var checker = autoSearchMap.find( 'i' );

        return checker.hasClass( 'fa-check' );
    }

//    background
    updateBackgroundElements();

    $( window ).resize(function() {
        updateBackgroundElements();
    });

    function updateBackgroundElements() {
        var mediaQueryMobile = window.matchMedia( "(min-width: 768px)" );
        var isDesktop = mediaQueryMobile.matches;

        var backgroundElements = $( '[data-desktop-background]' );

        backgroundElements.each( function() {
            var background = $( this ).data( 'desktop-background' );

            if ( isDesktop ) {
                $( this ).css( 'background-image', 'url(' + background + ')' );
            } else {
                $( this ).css( 'background-image', '' );
            }
        });
    }

    function openMapSequenceHideBlock() {
        triggerMapResize();
        $( '#searchResults' ).css( 'display', 'none' );
        triggerMapRequested();
    }

    function closeMapSequenceHideBlock() {
        $( '#searchResults' ).css( 'display', 'block' );
        resultsMap.css( 'bottom', -window.innerHeight + 'px' );
    }

    function triggerMapResize() {
        if ( typeof google != 'undefined' && googleMapScriptInit ) {
            google.maps.event.trigger( map, 'resize' );
        }
    }

    function triggerMapRequested() {
        $( document ).trigger( 'googleMapScriptRequested' );
    }

    function isScrolledIntoView( element, fullyInView ) {
        var pageTop         = $( window ).scrollTop();
        var pageBottom      = pageTop + $( window ).height();
        var elementTop      = $( element ).offset().top;
        var elementBottom   = elementTop + $( element ).height();

        if ( fullyInView === true ) {
            return ((pageTop < elementTop) && (pageBottom > elementBottom));
        } else {
            return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
        }
    }

    function triggerMapRequestedIfVisible() {
        if ( isScrolledIntoView( mapContainer ) ) {
            triggerMapRequested();
        }
    }

    // working hours folding
    var workingHoursBlock = $( '.highlights__item--hours' );
    var workingHoursTitle = workingHoursBlock.find( 'h3' );

    workingHoursTitle.on( 'click', function () {
        var dayList = workingHoursBlock.find( 'ul' ).first();
        if ( dayList.hasClass( 'hide-children' ) ) {
            dayList.removeClass( 'hide-children' );
            workingHoursTitle.removeClass( 'arrow-down' );
            workingHoursTitle.addClass( 'arrow-up' );
        } else {
            dayList.addClass( 'hide-children' );
            workingHoursTitle.removeClass( 'arrow-up' );
            workingHoursTitle.addClass( 'arrow-down' );
        }
    });
});
