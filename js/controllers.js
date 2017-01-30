var starter = angular
		.module('starter.controllers', [ 'ngCordova', 'ngStorage' ])

		.controller(
			'ApplicationCtrl',
			function($scope, $ionicLoading, appService) {

		})

		.controller('HomeCtrl', function($scope,$http, $ionicLoading) {

				$scope.breaches = [];
				$scope.mv = {};
				$scope.lauchAnalyse = function () {
					if (!$scope.mv.directoryToAnalyse) {
						alert("Entrez un repertoire non vide : " + $scope.mv.directoryToAnalyse);
						return ;
					}



					 $ionicLoading.show();
					$http.get("/analysor/index.php/analyse?directory=" + $scope.mv.directoryToAnalyse)
					.then(
						function(response) {
							$scope.breaches = response.data;
							 $ionicLoading.hide();
						},
						function(){
								alert("error");
								$ionicLoading.hide();
						});

				}

		})
