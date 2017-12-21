<?php

class Game {

  private $players = [];
  private $round = 0;
  private $currentPlayerIndex = 0;


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
