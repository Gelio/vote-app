define(['angular'], function(angular) {
    var mainModule = angular.module('mainModule', []);

    mainModule.controller('MainCtrl', ['$scope', function($scope) {
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
});