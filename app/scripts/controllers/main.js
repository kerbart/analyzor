'use strict';

/**
 * @ngdoc function
 * @name analysorApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the analysorApp
 */
angular.module('analysorApp')
  .controller('MainCtrl', function ($scope, $http) {
      // will store all view variables
      $scope.vm = {};

      // will launch analysis
      $scope.launchAnalysis = function() {
          $scope.vm.inProgressMessage = true;
          $scope.vm.errorMessage = false;
              $scope.vm.data = [];
          $http.get("/index.php/analysePhp?directory=" + $scope.vm.directory).then(
              function(response) {
                console.log("response is", response);
                  $scope.vm.inProgressMessage = false;
                  if (response.data.error) {
                    $scope.vm.errorMessage = response.data.error;
                    } else {
                      $scope.vm.data = response.data
                  }
              },
              function(error) {
                  console.log("error is", error);

              }
          );
      }
  });
