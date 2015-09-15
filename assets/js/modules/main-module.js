var mainModule = angular.module('mainModule', ['authService']);

mainModule.controller('mainCtrl', ['$scope', 'AuthFactory', function($scope, AuthFactory) {
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