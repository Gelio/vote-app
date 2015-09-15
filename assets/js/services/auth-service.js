angular.module('authService', ['satellizer']).
    factory('AuthFactory', ['$http', '$auth', function($http, $auth) {
        var auth = {

        };


        auth.login = function(credentials) {
            console.log(credentials);
            $auth.login(credentials)
                .then(function(response) {
                    console.log("successful", response);
                })
                .catch(function(error) {
                    console.log("error", error);
                });
            /*
            $http.post('assets/php/response.txt', {
                email: credentials.email,
                password: credentials.password
                }).then(function (response) {
                    // success
                    var loginStatus = parseInt(response.data);
                    if (loginStatus === 0)
                        console.log('not set');
                    else if (loginStatus === 1)
                        console.log('logged in');
                    else if (loginStatus === 2)
                        console.log('incorrect data');
                    else
                        console.log('unknown response', response);
                },
                function (response) {
                    // error
                    console.log('error logging in', response);
                });
            */
        };

        return auth;


    }]);