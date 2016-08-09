requirejs.config({
    baseUrl: '/bundles/domainbusiness/scripts/vendors',
    shim: {
        bootstrap : {
            deps: ['jquery']
        },
        "underscore": {
            exports: "_"
        },
        'slick' : {
            deps: ['jquery']
        }
    },
    paths: {
        modules         : '../modules',
        tools           : '../modules/tools',
        abstract        : '../abstract', 
        'jquery'        : 'jquery.min',
        'jquery-ui'     : 'jquery-ui.min',
        'jquery-mobile' : 'jquery.mobile.custom.min',
        'bootstrap'     : 'bootstrap.min',
        'underscore'    : 'underscore-min',
        'alertify'      : 'alertify.min',
        'spin'          : 'spin.min',
        'select2'       : 'select2.min',
        'slick'         : 'slick.min',
        'lightbox'      : 'simple-lightbox.min'
    }
});
