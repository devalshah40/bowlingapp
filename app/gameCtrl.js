app.controller('gameCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error

    $scope.gameID = $rootScope.gameID;
    $scope.players = $rootScope.players;


    $scope.myCustomValidator = function(playerName){
        if($scope.players.indexOf(playerName) !== -1) {
            return false;
        }
        return true;
    };

    $scope.addPlayer = function (playerName) {
        if(playerName == '') {
            return;
        }
        $scope.myForm.reset();
        $scope.players.push(playerName);
    };


    $scope.startGame = function () {
        Data.post('create-game', {
            players: $scope.players
        }).then(function (results) {
            Data.toast(results);
            $rootScope.gameID = results.gameID;
            $rootScope.players = $scope.players;

            if (results.status == "success") {
                $location.path('game');
            }
        });
    };

});