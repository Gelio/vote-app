angular.module('signinModule', ['ui.router', 'authService'])
    .controller('signinCtrl', ['$scope', '$state', 'AuthService', function($scope, $state, AuthService) {
        if(AuthService.isAuthenticated()) {
            console.log('user already authenticated tries to sign in');
            $state.go('main');
            return;
        }

        $scope.authenticate = function(provider) {
            AuthService.authenticate(provider);
        };
    }]);