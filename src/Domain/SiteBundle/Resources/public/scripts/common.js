requirejs.config({
    baseUrl: '/bundles/domainsite/scripts/vendors',
    shim: {
        bootstrap : {
            deps: ['jquery'],
        }
    },
    paths: {
        modules         : '../modules',
        tools           : '../modules/tools', 
        'jquery'        : 'jquery.min',
        'jquery-ui'     : 'jquery-ui.min',
        'jquery-mobile' : 'jquery.mobile.custom.min',
        'bootstrap'     : 'bootstrap.min'
    }
});
