requirejs.config({
    baseUrl: '/bundles/domainsite/scripts/vendors',
    shim: {
        bootstrap : {
            deps: ['jquery'],
        },
        "underscore": {
            exports: "_"
        },
        "jquery" : {
            exports: "$"
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
            deps: ['$']
        },
        'slick' : {
            deps: ['jquery']
        },
        'slider' : {
            deps: ['jquery']
        },
        'star-rating' : {
            deps: ['$']
        }
    },
    paths: {
        modules         : '../modules',
        tools           : '../modules/tools',
        abstract        : '../abstract',
        async           : 'require/async',
        goog            : 'require/goog',
        propertyParser  : 'require/propertyParser',
        'jquery'        : 'jquery.min',
        'jquery-ui'     : 'jquery-ui.min',
        'jquery-mobile' : 'jquery.mobile.custom.min',
        'bootstrap'     : 'bootstrap.min',
        'underscore'    : 'underscore-min',
        'alertify'      : 'alertify.min',
        'spin'          : 'spin.min'
        'slick'         : 'slick.min',
        'photo-gallery' : 'photo-gallery',
        'lightbox'      : 'simple-lightbox.min',
        'select2'       : 'select2.min'

    }
});
