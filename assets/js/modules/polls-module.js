var pollsModule = angular.module('pollsModule', ['authService']);

pollsModule.controller('pollsCtrl', ['$scope', 'AuthService', function($scope, AuthService) {

}]);

pollsModule.controller('pollCtrl', ['$scope', '$stateParams', function($scope, $stateParams) {
    console.log($stateParams);
}]);