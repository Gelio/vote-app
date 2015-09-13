var loginModule = angular.module('loginModule', []);

loginModule.directive('login', function() {
    return {
        restict: 'A',
        templateUrl: 'assets/templates/login.html',
        controller: 'loginCtrl'
    };
});

loginModule.controller('loginCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.login = {
        email: '',
        password: ''
    };

    $scope.checkData = function() {
        $http.post('assets/php/response.txt', {email: $scope.email, password: $scope.password}).
            then(function(response) {
                // success
                var loginStatus = parseInt(response.data);
                if(loginStatus === 0)
                    console.log('not set');
                else if(loginStatus === 1)
                    console.log('logged in');
                else if(loginStatus === 2)
                    console.log('incorrect data');
                else
                    console.log('unknown response', response);
            },
            function(response) {
                // error
                console.log('error logging in', response);
            });
    };

}]);