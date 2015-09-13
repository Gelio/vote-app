(function() {
    var app = angular.module('voteApp', ['ngRoute', 'loginModule', 'mainModule']);

    app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
        $routeProvider.when('/', {
            templateUrl: 'assets/templates/main-page.html',
            controller: 'MainCtrl'
        })
            .otherwise('/');
    }]);
})();