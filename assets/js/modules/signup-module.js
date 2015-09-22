angular.module('signupModule', ['ui.router', 'authService'])
    .controller('signupCtrl', ['$scope', '$state', 'AuthService', 'toaster', function($scope, $state, AuthService, toaster) {
        // Even existing users may want to link other accounts with their main account
        /*if(AuthService.isAuthenticated()) {
            console.log('user already authenticated tries to sign in');
            $state.go('main');
            return;
        }*/
        $scope.passwordVisible = false;
        $scope.signUpData = {
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

                toaster.pop('success', 'Authenticated successfully');
            }, function(error) {
                console.log("cannot authenticate with", provider, error);

                toaster.pop('error', 'Cannot authenticate with ' + provider, error.data.error);
            });
        };

        $scope.signUp = function() {
            var result = AuthService.signIn($scope.signUpData, function(data) {
                console.log("authenticated successfully", data);
                toaster.pop('success', 'Signed up successfully');

                $state.go("main");
            }, function(error) {
                $scope.error = error.data;

                toaster.pop('error', 'Cannot sign up', error.data.error);
            });
        };
    }]);