<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiDeckController extends AbstractController
{
    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function apiDeck(
        SessionInterface $session
    ): Response {
        /** @var array<int> $deck */
        $deck = $session->get('deck');

        sort($deck);

        $data = [
            'deck' => $deck,
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }

    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['GET', 'POST'])]
    public function apiDeckShuffle(
        SessionInterface $session
    ): Response {
        $DeckOfCards = new DeckOfCards();

        $deck = $DeckOfCards->shuffle();

        $session->set('deck', $deck);

        $data = [
            'deck' => $deck,
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }

    #[Route('/api/deck/draw', name: 'api_deck_draw', methods: ['GET', 'POST'])]
    public function apiDeckDraw(
        SessionInterface $session
    ): Response {
        $deck = $session->get('deck');

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

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }

    #[Route("/api/deck/draw/{num<\d+>}", name: 'api_deck_draw_number', methods: ['GET', 'POST'])]
    public function apiDeckDrawNumber(
        SessionInterface $session,
        Request $request
    ): Response {
        $num = $request->get('num');

        if (!is_numeric($num)) {
            throw new \InvalidArgumentException('The "num" parameter must be a valid number.');
        }

        $num = (int) $num;
        $remainingCards = $session->get('remainingCards');

        if ($num > $remainingCards) {
            throw new \Exception('Can not get more cards!');
        }

        $deck = $session->get('deck');
        /** @var array<string> $deck */
        $card = new Card($deck, $num);
        $result = $card->drawCards();
        $cards = $result['cards'];
        $remainingCards = count($result['deck']);
        $session->set('deck', $result['deck']);
        $session->set('remainingCards', $remainingCards);

        $data = [
            'cards' => $cards,
            'remainingCards' => $remainingCards,
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }
}
