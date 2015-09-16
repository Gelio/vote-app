angular.module('logoutModule', ['ui.router', 'authService'])
    .controller('logoutCtrl', ['$scope', '$state', 'AuthService', function($scope, $state, AuthService) {
        $scope.logoutState = 0;

        if(AuthService.isAuthenticated()) {
            AuthService.logout();
            $scope.logoutState = 1;
            $state.go('main');
        } else {
            console.log("error, user already logged out but tried to log out again");
            $scope.logoutState = 2;
            $state.go('main');
        }
    }]);