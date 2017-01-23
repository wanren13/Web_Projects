'use strict';

// Declare app level module which depends on views, and components
angular.module('myApp', [
  'ngRoute',
  'ngCookies',
  'ui.bootstrap'
])

.config(['$routeProvider', function($routeProvider) {
    $routeProvider.
    when('/', {
        templateUrl: 'view1/view1.html',
        controller: 'rootCtrl'
    }).
    when('/reviews', {
        templateUrl: 'view2/view2.html',
        controller: 'rootCtrl'
    }).
    otherwise({redirectTo: '/'});
}])

.controller('rootCtrl', 
  ['$scope', '$rootScope', '$http', '$cookies', 
  function($scope, $rootScope, $http, $cookies) {
    $scope.setid = function (id){
      $cookies.put('selectedID', id);
    };

    $rootScope.ID = $cookies.get('selectedID');

    $http.get('coffees.json').success(function(data) {
        $scope.coffees = data;
    });
}]);
