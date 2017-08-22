requirejs.config({
    baseUrl: '/bundles/',
    shim: {
        bootstrap : {
            deps: ['jquery'],
        },
        "underscore": {
            exports: "_"
        },
        "jquery" : {
            exports: "jquery"
        },
        'photo-gallery' : {
            deps: ['jquery']
        },
        'comparasion' : {
            deps: ['jquery']
        },
        'modal' : {
            deps: ['jquery']
        },
        'select' : {
            deps: ['jquery']
        },
        'slick' : {
            deps: ['jquery']
        },
        'slider' : {
            deps: ['jquery']
        },
        'lightbox': {
            deps: ['jquery']
        },
        'tool-star-rating' : {
            deps: ['jquery']
        },
        'js-cookie' : {
            exports: 'Cookies'
        },
        'selectize' : {
            deps: ['jquery']
        },
        'velocity' : {
            deps: ['jquery']
        },
        'velocity-ui' : {
            deps: ['velocity']
        },
        'iframetracker' : {
            deps: ['jquery']
        },
        'highcharts' : {
            deps: ['jquery']
        },
        'jquery-ui' : {
            deps: ['jquery']
        },
        'main-redesign' : {
            deps: ['selectize']
        }
    },
    paths: {
        modules         : 'domainsite/scripts/modules',
        tools           : 'domainsite/scripts/modules/tools',
        abstract        : 'domainsite/scripts/abstract',
        async           : 'domainsite/scripts/vendors/require/async',
        goog            : 'domainsite/scripts/vendors/require/goog',
        propertyParser  : 'domainsite/scripts/vendors/require/propertyParser',
        'jquery'        : 'domainsite/scripts/vendors/jquery.min',
        'jquery-ui'     : 'domainsite/scripts/vendors/jquery-ui.min',
        'jquery-mobile' : 'domainsite/scripts/vendors/jquery.mobile.custom.min',
        'js-cookie'     : 'domainsite/scripts/vendors/js.cookie.min',
        'bootstrap'     : 'domainsite/scripts/vendors/bootstrap.min',
        'moment'        : '/bundles/sonatacore/vendor/moment/min/moment.min',
        'dateTimePicker': '/bundles/sonatacore/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min',
        'underscore'    : 'domainsite/scripts/vendors/underscore-min',
        'alertify'      : 'domainsite/scripts/vendors/alertify.min',
        'spin'          : 'domainsite/scripts/vendors/spin.min',
        'slick'         : 'domainsite/scripts/vendors/slick.min',
        'photo-gallery' : 'domainsite/scripts/vendors/photo-gallery',
        'lightbox'      : 'domainsite/scripts/vendors/simple-lightbox.min',
        'select2'       : 'domainsite/scripts/vendors/select2.min',
        'iframetracker' : 'domainsite/scripts/vendors/jquery.iframetracker.min',
        'highcharts'    : 'domainsite/scripts/vendors/highcharts',

        'business/modules' : 'domainbusiness/scripts/modules',
        'business/tools'   : 'domainbusiness/scripts/modules/tools',

        //redesign
        'selectize': 'domainsite/scripts/vendors/min/selectize-min',
        'velocity': 'domainsite/scripts/vendors/min/velocity-min',
        'velocity-ui': 'domainsite/scripts/vendors/min/velocity-ui-min',
        'profile-redesign': '/assetic/js/modules/profile.min',
        'main-redesign': '/assetic/js/modules/main.min',
        'maps-redesign': '/assetic/js/modules/mapSearchPage.min',
        'tools/resetPassword': '/assetic/js/modules/tools/resetPassword.min',
        'tools/starRating': '/assetic/js/modules/tools/starRating.min',
        'tools/search': '/assetic/js/modules/tools/search.min',
        'tools/geolocation': '/assetic/js/modules/tools/geolocation.min',
        'tools/searchMenu': '/assetic/js/modules/tools/searchMenu.min',
        'tools/login': '/assetic/js/modules/tools/login.min',
        'tools/registration': '/assetic/js/modules/tools/registration.min',
        'abstract/view': '/assetic/js/abstract/view.min',
        'tools/spin': '/assetic/js/modules/tools/spin.min',
        'tools/directions': '/assetic/js/modules/tools/directions.min',
        'tools/select': '/assetic/js/modules/tools/select.min',
        'tools/mapSpin': '/assetic/js/modules/tools/mapSpin.min',
        'tools/reportTracker': '/assetic/js/modules/tools/reportTracker.min',
        'tools/slider': '/assetic/js/modules/tools/slider.min',
        'tools/redirect': '/assetic/js/modules/tools/redirect.min',
        'business/tools/interactions': '/assetic/js/modules/tools/interactions.min',
        'business/tools/form': '/assetic/js/modules/tools/form.min',
        'business/tools/phones': '/assetic/js/modules/tools/phones.min',
        'business/tools/workingHours': '/assetic/js/modules/tools/workingHours.min',
        'business/tools/formErrorsHandler': '/assetic/js/modules/tools/formErrorsHandler.min',
        'business/tools/images': '/assetic/js/modules/tools/images.min',
        'business/tools/videos': '/assetic/js/modules/tools/videos.min'
    },
    waitSeconds: 0
});
