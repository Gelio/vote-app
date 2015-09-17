angular.module('signupModule', ['ui.router', 'authService'])
    .controller('signupCtrl', ['$scope', '$state', 'AuthService', function($scope, $state, AuthService) {
        // Even existing users may want to link other accounts with their main account
        /*if(AuthService.isAuthenticated()) {
            console.log('user already authenticated tries to sign in');
            $state.go('main');
            return;
        }*/
        $scope.passwordVisible = false;
        $scope.signInData = {
            username: '',
            email: '',
            password: ''
        };
        $scope.error = {
            error: false,
            message: ''
        };

        $scope.authenticate = function(provider) {
            AuthService.authenticate(provider, function(data) {
                console.log("successfully authenticated with", provider, data);
            }, function(error) {
                console.log("cannot authenticate with", provider, error);
            });
        };

        $scope.signIn = function() {
            var result = AuthService.signIn($scope.signInData, function(data) {
                console.log("authenticated successfully", data);
                $state.go("main");
            }, function(error) {
                $scope.error = error.data;
            });
        };
    }]);