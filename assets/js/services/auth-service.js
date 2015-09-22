angular.module('authService', ['satellizer']).
    service('AuthService', ['$http', '$auth', function($http, $auth) {
        var self = this;




        this.login = function(credentials, callbackSuccess, callbackError) {
            $auth.login(credentials)
                .then(function(response) {
                    console.log("successful authentication", response);

                    if(callbackSuccess)
                        callbackSuccess(response);
                })
                .catch(function(error) {
                    console.log("error while authenticating", error);

                    if(callbackError)
                        callbackError(error);
                });
        };

        this.logout = function() {
            $auth.logout();
        };

        this.authenticate = function(provider, successCallback, errorCallback) {
            $auth.authenticate(provider)
                .then(function(response) {
                    if(successCallback)
                        return successCallback(response);
                })
                .catch(function(error) {
                    if(errorCallback)
                        return errorCallback(error);
                });
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

        this.signIn = function(userData, callbackSuccess, callbackError) {
            $http.post(baseUrl+"sign-up.php", userData)
                .then(function(response) {
                    // success
                    console.log("success while registering user", response);

                    if(callbackSuccess)
                        return callbackSuccess(response);
                }, function(error) {
                    // error
                    console.log("cannot register user", error);

                    if(callbackError)
                        return callbackError(error);
                });
        };
    }]);