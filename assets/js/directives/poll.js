var pollModule = angular.module('pollModule', []);

pollModule.controller('pollCtrl', ['$scope', function($scope) {
    // access the poll via $scope.poll
    // console.log($scope.poll);

    console.log($scope.poll.data, $scope.poll.labels);
}]);

pollModule.directive('poll', function() {
    return {
        restrict: 'A',
        templateUrl: 'assets/templates/poll-directive.html',
        controller: 'pollCtrl',
        scope: {
            poll: '='
        }
    };
});