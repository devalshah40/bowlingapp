<?php
require_once "Game.php";
require_once "Player.php";

$app->get('/game/:id', function ($id) {

  $game = new Game($id);
  sendGameResponse($game);
});

$app->post('/add-pin', function () use ($app) {
  $r = json_decode($app->request->getBody());

  $db = new DbHandler();

  $game = new Game($r->details->game_id);
  $pin = $r->details->pin;
  $currentPlayer = $game->currentPlayer();

  $table_name = "scores";
  $details = [
    'bowler_id' => $currentPlayer->id,
    'frame_no' => $currentPlayer->currentFrame() + 1  
  ];

  if($currentPlayer->currentChance() == 0) {
    $details['first_score'] = $pin;
  } else if($currentPlayer->currentChance() == 1) {
    $details['second_score'] = $pin;
  } else {
    $details['bonus_score'] = $pin;
  }

  $game->roll($pin);
  $details['frame_score'] = $currentPlayer->frameScore($details['frame_no'] - 1);

  if(isset($details['first_score'])) {
    $column_names = array_keys($details);
    $db->insertIntoTable($details, $column_names, $table_name);
  } else {
    $column_name = (isset($details['second_score'])) ? 'second_score' : 'bonus_score';
    $db->query("
      UPDATE scores set 
        $column_name = " . $pin . "
       WHERE `bowler_id` = '". $details['bowler_id'] ."' AND 
          `frame_no` = '". $details['frame_no'] ."' ");
  }

  $final_score = $currentPlayer->score();
  if(isset($final_score)) {
    $db->query("
      UPDATE bowlers set 
        final_score = " . $final_score. "
       WHERE `id` = '". $currentPlayer->id ."' ");
  }

  sendGameResponse($game);
});

$app->post('/create-game', function () use ($app) {
  $r = json_decode($app->request->getBody());
  $db = new DbHandler();

  $gameDetails = $db->getOneRecord("select id from game order by id desc");
  $gameName = 'Game ' . ($gameDetails['id'] + 1);

  $table_name = "game";
  $details = [
    'game_name' => $gameName
  ];
  $column_names = array('game_name');
  $gameID = $db->insertIntoTable($details, $column_names, $table_name);
  if ($gameID != NULL) {
    $table_name = "bowlers";
    $column_names = array('game_id', 'bowler_name');

    foreach ($r->players as $player) {
      $details = [
        'game_id' => $gameID,
        'bowler_name' => $player
      ];

      $db->insertIntoTable($details, $column_names, $table_name);
    }

    $response["status"] = "success";
    $response["message"] = $gameName . " is created successfully";
    $response["gameID"] = $gameID;

    echoResponse(200, $response);
  } else {
    $response["status"] = "error";
    $response["message"] = "Failed to create Game. Please try again";
    echoResponse(201, $response);
  }
});

function sendGameResponse($game) {

  $players = [];
  foreach ($game->players as $key => $player) {
    $players[$key]['name'] = $player->name;
    $players[$key]['scores'] = $player->scoreString();
    $players[$key]['frameWiseScore'] = $player->frameWiseScore();
    $players[$key]['final_score'] = $player->score();
    $players[$key]['round'] = $player->round;
  }

  $response["status"] = "success";
  $response["message"] = "Data is loaded successfully";
  $response["players"] = $players;
  $response["game"] = [
    'currentFrame' => $game->currentPlayer()->currentFrame() + 1,
    'currentChance' => $game->currentPlayer()->currentChance() + 1,
    'totalCurrentChance' => $game->currentPlayer()->numberOfChanceInCurrentFrame(),
    'currentPlayerName' => $game->currentPlayer()->name,
    'standingPin' => $game->currentPlayer()->standingPin(),
    'status' => $game->status(),
    'id' => $game->id,
    'name' => $game->name,
  ];

  echoResponse(200, $response);
}

$app->get('/load-last-games', function () {

  $db = new DbHandler();
  $gameDetails = $db->query("SELECT  * FROM game g order by g.`id` desc");

  $games = [];
  foreach ($gameDetails as $key =>  $game) {
    $games[$key]['id'] = $game['id'];
    $games[$key]['name'] = $game['game_name'];
  }
  $response["games"] = $games;

  echoResponse(200, $response);
});

?>