<?php

namespace App\Card;

/**
 * Class for Deck of cards.
 * It can create a new deck, shuffle it, deal cards, and get the current state of deck.
 */
class DeckOfCards
{
    /**
     * @var array
     */
    private $deck;

    /**
     * Constructor for the DeckOfCards class.
     *
     * @param array
     */
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

    /**
     * Shuffles the deck of cards.
     *
     * @return array
     */
    public function shuffle()
    {
        shuffle($this->deck);

        return $this->deck;
    }

    /**
     * Get the current state of the deck.
     *
     * @return array
     */
    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * Deals a card from the deck.
     *
     * @return string
     */
    public function deal()
    {
        $card = array_shift($this->deck);

        return $card;
    }
}
