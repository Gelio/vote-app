(function(){
    var app = angular.module('voteApp', ['ui.router', 'authService', 'mainModule', 'sidebarModule']);


    //uiRouter
    app.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
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