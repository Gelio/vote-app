(function(){
    var modules = [
        'ui.router',
        'satellizer',
        'authService',
        'mainModule',
        'sidebarModule',
        'logoutModule',
        'signinModule',
        'pollsModule'
    ];
    var app = angular.module('voteApp', modules);


    //uiRouter
    app.config(['$stateProvider', '$urlRouterProvider', "$authProvider", function($stateProvider, $urlRouterProvider, $authProvider) {
        var baseUrl = "http://localhost/vote-app/assets/php/";
        $authProvider.loginUrl = baseUrl + 'login.php';


        // Auth0
        $authProvider.google({
            url: baseUrl + 'external-auth.php?provider=google',
            clientId: '971255903327-u70mlh2duncr4sent7hc8f0j9s8lebld.apps.googleusercontent.com'
        });


        $urlRouterProvider.otherwise("/");

        $stateProvider
            .state('main', {
                url: '/',
                templateUrl: 'assets/templates/main-page.html',
                controller: 'mainCtrl'
            })
            .state('logout', {
                url: '/logout',
                templateUrl: 'assets/templates/logout.html',
                controller: 'logoutCtrl'
            })
            .state('sign-in', {
                url: '/sign-in',
                templateUrl: 'assets/templates/sign-in.html',
                controller: 'signinCtrl'
            })
            .state('polls', {
                url: '/polls',
                templateUrl: 'assets/templates/polls.html',
                controller: 'pollsCtrl'
            });
    }]);

    app.controller("appCtrl", ["$scope", "AuthService", function($scope, AuthService) {
        $scope.isAuthenticated = function() {
            return AuthService.isAuthenticated();
        };

        $scope.authPayload = function() {
            return AuthService.getPayload();
        };
    }]);

    angular.bootstrap(document, ["voteApp"]);
})();