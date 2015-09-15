var sidebarModule = angular.module('sidebarModule', ['authService']);

sidebarModule.directive('sidebar', function() {
    return {
        restict: 'A',
        templateUrl: 'assets/templates/sidebar.html',
        controller: 'sidebarCtrl'
    };
});

sidebarModule.controller('sidebarCtrl', ['$scope', '$http', 'AuthFactory', function($scope, $http, AuthFactory) {
    $scope.credentials = {
        email: '',
        password: ''
    };

    $scope.login = function() {
        AuthFactory.login($scope.credentials);
    };

}]);