(function(){
    var app = angular.module('voteApp', ['ui.router', 'satellizer', 'authService', 'mainModule', 'sidebarModule']);


    //uiRouter
    app.config(['$stateProvider', '$urlRouterProvider', "$authProvider", function($stateProvider, $urlRouterProvider, $authProvider) {
        $authProvider.loginUrl = 'http://localhost/vote-app/assets/php/login.php';
        //$authProvider.httpInterceptor = false; // TODO: remove that after pushing to the server
        //$authProvider.withCredentials = true;

        $urlRouterProvider.otherwise("/");

        $stateProvider
            .state('main', {
                url: '/',
                templateUrl: 'assets/templates/main-page.html',
                controller: 'mainCtrl'
            });
    }]);

    app.controller("appCtrl", ["$scope", function($scope) {
        $scope.username = "";
    }]);

    angular.bootstrap(document, ["voteApp"]);
})();