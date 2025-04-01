<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\DeckOfCards;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api')]
    public function api(): Response
    {
        return $this->render('api.html.twig');
    }

    #[Route('/api/quote', name: 'quote')]
    public function apiQuote(): Response
    {
        $quotes = [
            'Success is not final, failure is not fatal: it is the courage to continue that counts.',
            "Believe you can and you're halfway there.",
            "You miss 100% of the shots you don't take.",
            'A year from now you may wish you had started today.',
        ];

        $random_index = array_rand($quotes);
        $random_quote = $quotes[$random_index];

        $timestamp = date('Y-m-d H:i:s');

        $data = [
            'quote' => $random_quote,
            'timestamp' => $timestamp,
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }

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
        Request $request,
        int $num
    ): Response {
        /** @var int $num */
        $num = $request->get('num');
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

    #[Route('/api/game', name: 'api_game', methods: ['GET'])]
    public function apiGame(
        SessionInterface $session
    ): JsonResponse {
        $playerHand = $session->get('playerHand', []);
        $dealerHand = $session->get('dealerHand', []);
        $playerScore = $session->get('playerScore', 0);
        $dealerScore = $session->get('dealerScore', 0);
        $end = $session->get('end', false);

        $gameState = [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end,
        ];

        $response = new JsonResponse($gameState);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }

    #[Route('/api/library/books', name: 'api_library_books', methods: ['GET'])]
    public function getBooks(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();

        return $this->json($books);
    }

    #[Route('/api/library/book/{isbn}', name: 'api_library_book', methods: ['GET'])]
    public function getBook(BookRepository $bookRepository, string $isbn): JsonResponse
    {
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);

        if (!$book) {
            return $this->json(['error' => 'Book not found'], 404);
        }

        return $this->json($book);
    }
}
