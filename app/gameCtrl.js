app.controller('gameCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $routeParams) {
    //initially set those objects to null to avoid undefined error


    Data.get('game/' + $routeParams.id)
    .then(function (results) {
        Data.toast(results);
        //$rootScope.gameID = results.gameID;
        $scope.players = results.players;
        $scope.game = results.game;

    });
    
    $scope.roll = function (pin) {
        details = {
            "game_id":$scope.game.id,
            "pin":pin
        };

        Data.post('add-pin', {
            details: details
        }).then(function (results) {
            // Data.toast(results);
            
            if (results.status == "success") {
                $scope.players = results.players;
                $scope.game = results.game;
            }
        });
    };

    $scope.numberOfChanceInFrame = function(frame) {
        if ( frame === 9 ) {
            return 3;
        }
        return 2;
    };

    $scope.gotoLogin = function() {
        $location.path('game-login');
    };
});