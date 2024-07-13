<?php

namespace App\Card;

class DeckOfCards {

  private $deck;

    public function __construct() {

      $suits = array('♣', '♦', '♥', '♠');
      $ranks = array('Ess', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Knekt', 'Drottning', 'Kung');

      foreach ($suits as $suit) {
          foreach ($ranks as $rank) {
              $this->deck[] = "$suit $rank";
          }
      }
    }
  
    public function shuffle() {

      shuffle($this->deck);

      return $this->deck;
    }

    public function deal() {

      return array_shift($this->deck);
    }

    public function getDeck() {

      return $this->deck;
    }

    

  
  }