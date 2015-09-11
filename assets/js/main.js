(function() {
    var app = angular.module('voteApp', ['ngRoute']);

    app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
        $routeProvider.when('/', {
            templateUrl: 'assets/templates/index.html',
            controller: function() {

            }
        })
            .otherwise('/');
    }]);
})();