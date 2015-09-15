angular.module('authService', ['satellizer']).
    factory('AuthFactory', ['$http', '$auth', function($http, $auth) {
        var auth = {

        };


        auth.login = function(credentials) {
            $auth.login(credentials)
                .then(function(response) {
                    console.log("successful", response);
                })
                .catch(function(error) {
                    console.log("error", error);
                });
        };

        return auth;


    }]);