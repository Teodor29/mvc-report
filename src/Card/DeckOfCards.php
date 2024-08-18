<?php

namespace App\Card;

class DeckOfCards
{
    private $deck;

    // if argument is passed, use that deck, otherwise create a new deck

    public function __construct($deck = null)
    {
        if ($deck) {
            $this->deck = $deck;
        } else {
            $suits = ['♣', '♦', '♥', '♠'];
            $ranks = ['Ess', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Knekt', 'Drottning', 'Kung'];

            foreach ($suits as $suit) {
                foreach ($ranks as $rank) {
                    $this->deck[] = "$suit $rank";
                }
            }
        }
    }

    public function shuffle()
    {
        shuffle($this->deck);

        return $this->deck;
    }

    public function getDeck()
    {
        return $this->deck;
    }

    public function deal()
    {
        $card = array_shift($this->deck);

        return $card;
    }
}
