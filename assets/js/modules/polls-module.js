var pollsModule = angular.module('pollsModule', ['authService']);

pollsModule.controller('pollsCtrl', ['$scope', 'AuthService', function($scope, AuthService) {

}]);

pollsModule.controller('pollCtrl', ['$scope', '$stateParams', '$http', 'toaster', function($scope, $stateParams, $http, toaster) {
    $scope.poll = null;
    $scope.error = null;
    $scope.userVoted = false;

    console.log($stateParams);

    $http.get(baseUrl + "get_poll.php?id=" + $stateParams.pollID)
        .then(function(response) {
            console.log("getting poll succeeded", response);
            $scope.poll = new Poll($stateParams.pollID,
                response.data.question,
                response.data.options);

            $scope.userVoted = response.data.hasVoted;
        }, function (error) {
            console.log("error while getting the poll", error);
            $scope.error = error.data;
        });
}]);