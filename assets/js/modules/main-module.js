var mainModule = angular.module('mainModule', ['authService']);

mainModule.controller('mainCtrl', ['$scope', 'AuthService', function($scope, AuthService) {
    $scope.popularPolls = [
        new Poll(1, 'Poll 1', [
            {name: 'option 1', amount: 20},
            {name: 'option 2', amount: 80}
        ]),
        new Poll(2, 'Poll 2', [
            {name: 'option 1', amount: 20},
            {name: 'option 2', amount: 80}
        ]),
        new Poll(3, 'Poll 3', [
            {name: 'option 1', amount: 20},
            {name: 'option 2', amount: 80}
        ])
    ];
}]);