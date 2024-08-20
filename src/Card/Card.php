<?php

namespace App\Card;

/**
 * Class for card.
 * It can draw a specified number of cards from a deck.
 */
class Card
{
    /**
     * @var array
     * @var int
     */
    private $deck;
    private $num;

    /**
     * Constructor for the Card class.
     *
     * @param array
     * @param int
     */
    public function __construct($deck, $num)
    {
        $this->deck = $deck;
        $this->num = $num;
    }

    /**
     * Draws a number of cards from the deck.
     *
     * @return array
     */
    public function drawCards()
    {
        $removedCards = array_splice($this->deck, 0, $this->num);

        return ['deck' => $this->deck, 'cards' => $removedCards];
    }
}
