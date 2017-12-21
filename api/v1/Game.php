<?php

class Game {

  private $id = 0;
  private $players = [];
  private $round = 0;
  private $currentPlayerIndex = 0;

  public function __construct($game_id) {
    $this->id = $game_id;

    $db = new DbHandler();
//    $gameDetails = $db->query("SELECT  * FROM game g WHERE g.`id` = '$game_id'");

    $bowlerDetails = $db->query("SELECT b.* FROM game g INNER JOIN bowlers b ON g.`id` = b.`game_id` and g.id = '$game_id'");
    foreach ($bowlerDetails as $bowlerDetail) {
      $player = new Player($bowlerDetail['game_id'], $bowlerDetail['id']);
      $this->players[] = $player;
    }
  }


  public function loadGame($game_id) {

    $db = new DbHandler();
    $gameDetails = $db->query("SELECT  * FROM game g WHERE g.`id` = '$game_id'");

    $bowerDetails = $db->query("SELECT  * FROM game g INNER JOIN bowlers b ON g.`id` = b.`game_id` and g.id = '$game_id'");

    $details = [];
    foreach ($bowerDetails as $bowler) {

      $scoreDetails = $db->query("SELECT  * FROM bowlers b INNER JOIN scores s ON s.`bowler_id` = b.`id` AND b.`game_id` = '$game_id' AND b.id = '$game_id' ");


    $details = [];
    foreach ($scoreDetails as $player) {

      $rolls = [];
      if (!empty($player['first_score'])) {
        $details[$player['bowler_id']]['rolls'][] = $player['first_score'];
      }
      if (!empty($player['second_score'])) {
        $details[$player['bowler_id']]['rolls'][] = $player['second_score'];
      }
      if (!empty($player['bonus_score'])) {
        $details[$player['bowler_id']]['rolls'][] = $player['bonus_score'];
      }
    }

    }

    $playerDetails = $db->query("SELECT  * FROM bowlers b INNER JOIN scores s ON s.`bowler_id` = b.`id` AND b.`game_id` = '$game_id' AND b.id = '$player_id' ");


    $details = [];
    foreach ($playerDetails as $player) {

      $rolls = [];
      if (!empty($player['first_score'])) {
        $details[$player['bowler_id']]['rolls'][] = $player['first_score'];
      }
      if (!empty($player['second_score'])) {
        $details[$player['bowler_id']]['rolls'][] = $player['second_score'];
      }
      if (!empty($player['bonus_score'])) {
        $details[$player['bowler_id']]['rolls'][] = $player['bonus_score'];
      }

    }
  }

  public function roll($pin) {
    $this->currentPlayer()->roll($pin);

    if ($this->currentPlayer()->currentChance() === 0 || $this->currentPlayer()->gameIsOver()) {
      $this->currentPlayerIndex++;
      if ($this->currentPlayerIndex >= count($this->players) ) {
        $this->round += 1;
        $this->currentPlayerIndex = 0;
      }
    }
  }

  public function status() {
    if (count($this->players) === 0 ) {
      return "new";
    }
    if ($this->round == 0  &&
      $this->currentPlayerIndex ==0 &&
      $this->currentPlayer()->currentChance() == 0) {
      return "ready";
    }
    if ($this->currentPlayer()->gameIsOver()) {
      return "completed";
    }
    return "in progress";
  }

  public function currentPlayer() {
    return $this->players[$this->currentPlayerIndex];
  }
}

?>
