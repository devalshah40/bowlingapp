<?php

class Game {

  public $id = 0;
  public $name;
  public $players = [];
  public $round = 0;
  public $currentPlayerIndex = 0;

  public function __construct($game_id) {
    $this->id = $game_id;

    $db = new DbHandler();
    $gameDetails = $db->query("SELECT  * FROM game g WHERE g.`id` = '$game_id'");
    foreach ($gameDetails as $game) {
      $this->name = $game['game_name'];
    }

    $bowlerDetails = $db->query("SELECT b.* FROM game g INNER JOIN bowlers b ON g.`id` = b.`game_id` and g.id = '$game_id'");
    foreach ($bowlerDetails as $bowlerDetail) {
      $player = new Player($bowlerDetail['game_id'], $bowlerDetail['id']);
      $this->players[] = $player;
    }

    $playerDetails = $db->query("SELECT  * FROM bowlers b INNER JOIN scores s ON s.`bowler_id` = b.`id` AND b.`game_id` = '$game_id' ORDER BY s.id ASC");

    foreach ($playerDetails as $key => $player) {
//      echo "<pre>"; print_r($player);
      if (!is_null($player['first_score'])) {
        $this->roll($player['first_score']);
      }
      if (!is_null($player['second_score'])) {
        $this->roll($player['second_score']);
      }
      if (!is_null($player['bonus_score'])) {
        $this->roll($player['bonus_score']);
      }
    }
//    exit;
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
