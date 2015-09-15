requirejs.config({
    baseUrl: '',
    paths: {
        jquery: [
            'node_modules/jquery/dist/jquery.min',
            'assets/libs/jquery.min'
        ],
        angular: [
            'node_modules/angular/angular.min',
            'assets/libs/angular.min'
        ],
        "angular-route": [
            'node_modules/angular-route/angular-route.min',
            'assets/libs/angular.route.min'
        ],
        "angular-ui-router": [
            'node_modules/angular-ui-router/build/angular-ui-router.min',
            'assets/libs/angular-ui-router.min'
        ]
    },

    shim: {
        'angular': {
            exports: 'angular'
        },
        'angular-route': {
            deps: ['angular']
        },
        'angular-ui-router': {
            deps: ['angular']
        }
    }
});