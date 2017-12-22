app.controller('bowlingCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.playerName = '';
    $scope.players = [];


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

            if (results.status == "success") {
                $location.path('game/'+results.gameID);
            }
        });
    };

    Data.get('load-last-games')
     .then(function (results) {
        $scope.lastGames = results.games;
    });
});