var app = angular.module('myApp', ['ngRoute', 'ngAnimate', 'toaster', 'angularValidator']);

app.config(['$routeProvider',
  function ($routeProvider) {
        $routeProvider.
        when('/game-login', {
            title: 'Start game',
            templateUrl: 'partials/gamelogin.html',
            controller: 'bowlingCtrl'
        }).
        when('/game/:id', {
            title: 'Game',
            templateUrl: 'partials/game.html',
            controller: 'gameCtrl'
        })
            .otherwise({
                redirectTo: '/game-login'
            });
  }]);