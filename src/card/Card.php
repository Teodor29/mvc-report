<?php

namespace App\Card;
  
class Card {
  private $deck;
  private $num;

  public function __construct($deck, $num) {
    $this->deck = $deck;
    $this->num = $num;
  }

  public function drawCards() {
    $removedCards = array_splice($this->deck, 0, $this->num);
    return array('deck' => $this->deck, 'cards' => $removedCards);
  }
}

