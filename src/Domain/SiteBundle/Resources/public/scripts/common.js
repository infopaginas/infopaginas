requirejs.config({
    baseUrl: '/bundles/domainsite/scripts/vendors',
    shim: {
        bootstrap : {
            deps: ['jquery'],
        },
        "underscore": {
            exports: "_"
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
        'spin'          : 'spin.min'
    }
});
