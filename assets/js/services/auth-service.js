angular.module('authService', ['satellizer']).
    service('AuthService', ['$http', '$auth', function($http, $auth) {
        var self = this;




        this.login = function(credentials, callbackSuccess, callbackError) {
            $auth.login(credentials)
                .then(function(response) {
                    console.log("successful", response);

                    if(callbackSuccess)
                        callbackSuccess(response);
                })
                .catch(function(error) {
                    console.log("error", error);

                    if(callbackError)
                        callbackError(error);
                });
        };

        this.logout = function() {
            $auth.logout();
        };

        this.authenticate = function(provider) {
            $auth.authenticate(provider);
        };

        this.getPayload = function() {
            if(!$auth.isAuthenticated())
                return false;

            return $auth.getPayload();
        };

        this.getToken = function() {
            if(!$auth.isAuthenticated())
                return false;

            return $auth.getToken();
        };

        this.isAuthenticated = function() {
            return $auth.isAuthenticated();
        };
    }]);