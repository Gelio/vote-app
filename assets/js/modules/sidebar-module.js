var sidebarModule = angular.module('sidebarModule', ['authService']);

sidebarModule.directive('sidebar', function() {
    return {
        restict: 'A',
        templateUrl: 'assets/templates/sidebar.html',
        controller: 'sidebarCtrl'
    };
});

sidebarModule.controller('sidebarCtrl', ['$scope', '$http', 'AuthService', function($scope, $http, AuthService) {
    $scope.credentials = {
        email: '',
        password: ''
    };


    $scope.login = function() {
        AuthService.login($scope.credentials, function() {
            $scope.credentials.email = '';
            $scope.credentials.password = '';
        });
    };

    $scope.logTokenInfo = function() {
        console.log(AuthService.getPayload(), AuthService.getToken());
    };

}]);