<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardsController extends AbstractController
{
    #[Route('/card', name: 'card')]
    public function card(): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route('/card/deck', name: 'deck')]
    public function deck(SessionInterface $session): Response
    {
        /** @var array<int> $deck */
        $deck = $session->get('deck');
        dump($deck);

        if (empty($deck)) {
            $DeckOfCards = new DeckOfCards();
            $deck = $DeckOfCards->shuffle();
            $session->set('deck', $deck);
        }

        sort($deck);

        $data = [
            'deck' => $deck,
        ];

        return $this->render('deck.html.twig', $data);
    }

    #[Route('/card/deck/shuffle', name: 'shuffle')]
    public function shuffle(SessionInterface $session): Response
    {
        $DeckOfCards = new DeckOfCards();

        $deck = $DeckOfCards->shuffle();

        $session->set('deck', $deck);

        $data = [
            'deck' => $session->get('deck'),
        ];

        return $this->render('shuffle.html.twig', $data);
    }

    #[Route('/card/deck/draw', name: 'draw')]
    public function draw(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (empty($deck)) {
            $DeckOfCards = new DeckOfCards();
            $deck = $DeckOfCards->shuffle();
            $session->set('deck', $deck);
        }

        /** @var array<string> $deck */
        $card = new Card($deck, 1);
        $result = $card->drawCards();
        $cards = $result['cards'];
        $remainingCards = count($result['deck']);
        $session->set('deck', $result['deck']);
        $session->set('remainingCards', $remainingCards);

        $data = [
            'cards' => $cards,
            'remainingCards' => $remainingCards,
        ];

        return $this->render('draw.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: 'drawNumber')]
    public function drawNumber(int $num, SessionInterface $session): Response
    {
        $remainingCards = $session->get('remainingCards');
        if ($num > $remainingCards) {
            throw new \Exception('Can not get more cards!');
        }

        $deck = $session->get('deck');
        /** @var array<string> $deck */
        $card = new Card($deck, $num);
        dump($card);
        $result = $card->drawCards();
        $cards = $result['cards'];
        $remainingCards = count($result['deck']);
        $session->set('deck', $result['deck']);
        $session->set('remainingCards', $remainingCards);

        $data = [
            'cards' => $cards,
            'remainingCards' => $remainingCards,
        ];

        return $this->render('draw.html.twig', $data);
    }

    #[Route('/card/deck/reset', name: 'reset')]
    public function reset(SessionInterface $session): Response
    {
        $session->clear();

        return $this->render('card.html.twig');
    }
}
