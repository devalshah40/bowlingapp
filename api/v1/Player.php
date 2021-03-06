<?php

class Player {

  public $id = 0;
  public $name = '';
  public $rolls = [];
  public $round = 0;

  const LAST_FRAME = 9;

  public function __construct($game_id, $player_id) {

    $this->rolls = array_fill(0,21,0);
    $db = new DbHandler();

    $players = $db->query("SELECT  * FROM bowlers b WHERE b.`id` = '$player_id'");
    foreach ($players as $player) {
      $this->id = $player['id'];
      $this->name = $player['bowler_name'];
    }
  }

  public function currentFrame() {

    if ($this->round > 18) {
      return self::LAST_FRAME;
    }
    return ($this->round - $this->round % 2) / 2;
  }


  public function currentChance() {

    if ($this->round > 18) {
      return $this->round - 18;
    }
    return $this->round % 2;
  }


  public function numberOfChanceInFrame($frameNo) {

    if ($frameNo === self::LAST_FRAME) {
      return 3;
    }
    return 2;
  }


  public function numberOfChanceInCurrentFrame() {

    return $this->numberOfChanceInFrame($this->currentFrame());
  }


  public function gameIsOver() {

    if ($this->round <= 19) {
      return false;
    }

    if ($this->round === 20) {
      if ($this->rolls[$this->round - 2] == 10) {
        return false;
      }
    }
    return true;
  }

  public function roll($pin) {

    if ($this->gameIsOver()) {
      throw new RuntimeException(" Game is over");
    }
    if ($pin < 0 || $pin > 10) {
      throw new InvalidArgumentException("Invalid number of pin");
    }

    if ($pin > $this->standingPin()) {
      throw new InvalidArgumentException("Not so many standing pin");
    }

    $this->rolls[$this->round] = $pin;

    if ($pin == 10 && $this->round < 17) {
      $this->round++;
    }

    $this->round++;
  }


  // returns the number of standing pins
  public function standingPin() {

    if ($this->round % 2 == 0) {
      return 10;
    }

    if ($this->round == 19 && $this->rolls[$this->round - 1] == 10) {
      return 10;
    }
    return 10 - $this->rolls[$this->round - 1];
  }


  public function frameIsStrike($frameNo) {

    return $this->rolls[$frameNo * 2] == 10;
  }


  public function frameIsSpare($frameNo) {

    return !$this->frameIsStrike($frameNo) && ($this->rolls[$frameNo * 2] + $this->rolls[$frameNo * 2 + 1] === 10);
  }


  public function frameIsNormal($frameNo) {

    return ($this->rolls[$frameNo * 2] + $this->rolls[$frameNo * 2 + 1] < 10);
  }


  public function frameText($frameNo, $i) {

    if (( ($frameNo * 2) + $i) >= $this->round) {
      return ".";
    }
    if ($this->frameIsStrike($frameNo) && $frameNo < 9) {
      if ($i == 0) {
        return "";
      }
      return "X";
    }
    if ($this->frameIsSpare($frameNo)) {
      if ($i == 0) {
        return $this->rolls[$frameNo * 2];
      }
      return "/";
    }
    return $this->rolls[$frameNo * 2 + $i];
  }


  public function frameScore($frameNo) {

    $score = 0;

    if ($frameNo === self::LAST_FRAME) {

      $score += $this->rolls[$frameNo * 2] + $this->rolls[$frameNo * 2 + 1] + $this->rolls[$frameNo * 2 + 2];

    } else if ($this->frameIsStrike($frameNo)) {

      if ($this->frameIsStrike($frameNo + 1)) {

        $score += 20 + $this->rolls[($frameNo + 2) * 2];
      } else {
        $score += 10 + $this->rolls[($frameNo + 1) * 2] + $this->rolls[($frameNo + 1) * 2 + 1];
      }

    } else if ($this->frameIsSpare($frameNo)) {

      $score += 10 + $this->rolls[($frameNo + 1) * 2];

    } else {

      $score += $this->rolls[$frameNo * 2] + $this->rolls[$frameNo * 2 + 1];

    }
    return $score;
  }

  public function frameWiseScore() {
    $scores = [];
    for ($frame = 0; $frame < 10; $frame++) {
      array_push($scores, $this->frameScore($frame));
    }
    return $scores;
  }

  public function scoreString() {
    $scores = [];
    for ($frame = 0; $frame < 10; $frame++) {
      array_push($scores, $this->frameText($frame, 0));
      array_push($scores, $this->frameText($frame, 1));
    }
    array_push($scores, $this->frameText(9, 2));
    return $scores;
  }

  public function score($maxFrame = 10) {
    $score = 0;

    for ($frame = 0; $frame < $maxFrame; $frame++) {
      $score += $this->frameScore($frame);
    }
    return $score;
  }

}

?>
