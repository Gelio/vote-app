var pollModule = angular.module('pollModule', []);

pollModule.controller('pollDirectiveCtrl', ['$scope', function($scope) {
    // access the poll via $scope.poll
    // console.log($scope.poll);

}]);

pollModule.directive('poll', function() {
    return {
        restrict: 'A',
        templateUrl: 'assets/templates/poll-directive.html',
        controller: 'pollDirectiveCtrl',
        scope: {
            poll: '=',
            userVoted: '='
        }
    };
});