'use strict';

angular.module('myApp.view2', [])

.controller('View2Ctrl', 
    ['$scope', '$routeParams', 
    function($scope, $routeParams) {
    $scope.coffeeID = $routeParams.coffeeID;
}]);
