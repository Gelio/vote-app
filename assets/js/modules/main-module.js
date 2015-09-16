var mainModule = angular.module('mainModule', ['authService']);

mainModule.controller('mainCtrl', ['$scope', 'AuthService', function($scope, AuthService) {
    $scope.popularPolls = [
        new Poll('Poll 1', [
            {name: 'option 1', amount: 20},
            {name: 'option 2', amount: 80}
        ]),
        new Poll('Poll 2', [
            {name: 'option 1', amount: 20},
            {name: 'option 2', amount: 80}
        ]),
        new Poll('Poll 3', [
            {name: 'option 1', amount: 20},
            {name: 'option 2', amount: 80}
        ])
    ];
}]);