<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\Blackjack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class gameController extends AbstractController {

    #[Route("/game", name: "game")]
    public function game(): Response
    {
        return $this->render('game.html.twig');
    }

    #[Route("/blackjack", name: "blackjack")]
    public function blackjack(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $blackjack = new Blackjack($deck);

        $playerHand = [$blackjack->deal(), $blackjack->deal()];
        $dealerHand = [$blackjack->deal()];

        $playerScore = $blackjack->calculateScore($playerHand);
        $dealerScore = $blackjack->calculateScore($dealerHand);
        $end = $blackjack->checkGameEnd($playerScore, $dealerScore);

        $session->set('deck', $deck);
        $session->set('playerHand', $playerHand);
        $session->set('dealerHand', $dealerHand);
        $session->set('playerScore', $playerScore);
        $session->set('dealerScore', $dealerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end
        ]);
    }

    #[Route("/hit", name: "hit")]
    public function hit(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $deck = $session->get('deck');
        $blackjack = new Blackjack($deck);
        $playerHand = $session->get('playerHand');
        $playerHand[] = $blackjack->deal();

        $playerScore = $blackjack->calculateScore($playerHand);
        $dealerScore = $session->get('dealerScore');
        $end = $blackjack->checkGameEnd($playerScore, $dealerScore);

        $session->set('deck', $deck);
        $session->set('playerHand', $playerHand);
        $session->set('playerScore', $playerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $session->get('dealerHand'),
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end
        ]);
    }

    #[Route("/stand", name: "stand")]
    public function stand(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $deck = $session->get('deck');
        $blackjack = new Blackjack($deck);
        $dealerHand = $session->get('dealerHand');
        $dealerScore = $blackjack->calculateScore($dealerHand);

        while ($dealerScore < 17) {
            $dealerHand[] = $blackjack->deal();
            $dealerScore = $blackjack->calculateScore($dealerHand);
        }

        $playerHand = $session->get('playerHand');
        $playerScore = $session->get('playerScore');
        $end = true;

        $session->set('deck', $deck);
        $session->set('dealerHand', $dealerHand);
        $session->set('dealerScore', $dealerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end
        ]);
    }

    #[Route("/game/doc", name: "doc")]
    public function doc(): Response
    {
        return $this->render('doc.html.twig');
    }
}
