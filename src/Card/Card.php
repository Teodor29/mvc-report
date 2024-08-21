<?php

namespace App\Card;

/**
 * Class for card.
 * It can draw a specified number of cards from a deck.
 */
class Card
{
    /**
     * @var array<string>
     */
    private array $deck;

    private int $num;

    /**
     * Constructor for the Card class.
     *
     * @param array<string> $deck
     */
    public function __construct(array $deck, int $num)
    {
        $this->deck = $deck;
        $this->num = $num;
    }

    /**
     * Draws a number of cards from the deck.
     *
     * @return array<string, array<string>>
     */
    public function drawCards(): array
    {
        $removedCards = array_splice($this->deck, 0, $this->num);

        return ['deck' => $this->deck, 'cards' => $removedCards];
    }
}
