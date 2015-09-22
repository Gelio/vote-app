var sidebarModule = angular.module('sidebarModule', ['authService']);

sidebarModule.directive('sidebar', function() {
    return {
        restict: 'A',
        templateUrl: 'assets/templates/sidebar.html',
        controller: 'sidebarCtrl'
    };
});

sidebarModule.controller('sidebarCtrl', ['$scope', '$http', 'toaster', 'AuthService', function($scope, $http, toaster, AuthService) {
    $scope.credentials = {
        email: '',
        password: ''
    };


    $scope.login = function() {
        AuthService.login($scope.credentials, function() {
            $scope.credentials.email = '';
            $scope.credentials.password = '';
            toaster.pop('success', 'Signed in successfully');
        }, function (error) {
            toaster.pop('error', 'Error while signing in', error.data.error);
        });
    };

    $scope.logTokenInfo = function() {
        console.log(AuthService.getPayload(), AuthService.getToken());
    };

}]);