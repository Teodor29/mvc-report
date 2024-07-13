<?php

namespace App\Controller;

use App\Card\DeckOfCards;
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
        $end = false;
        $deck = new DeckOfCards();
        $deck->shuffle();
        $playerHand = [];
        $dealerHand = [];
        $playerHand[] = $deck->deal();
        $dealerHand[] = $deck->deal();
        $playerHand[] = $deck->deal();
        $playerScore = $this->calculateScore($playerHand);
        $dealerScore = $this->calculateScore($dealerHand);
        $end = $this->checkGameEnd($playerScore, $dealerScore);

        $session->set('deck', $deck);
        $session->set('playerHand', $playerHand);
        $session->set('dealerHand', $dealerHand);
        $session->set('playerScore', $playerScore);
        $session->set('dealerScore', $dealerScore);

        if ($playerScore > 20) {
            $end = true;
        }
        else if ($dealerScore > 20) {
            $end = true;
        }

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
        $playerHand = $session->get('playerHand');
        $dealerHand = $session->get('dealerHand');
        $playerHand[] = $deck->deal();
        $playerScore = $this->calculateScore($playerHand);
        $end = $this->checkGameEnd($playerScore, $session->get('dealerScore'));

        $session->set('deck', $deck);
        $session->set('playerHand', $playerHand);
        $session->set('playerScore', $playerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $session->get('dealerScore'),
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
        $playerHand = $session->get('playerHand');
        $dealerHand = $session->get('dealerHand');
        $dealerScore = $this->calculateScore($dealerHand);
        $end = $this->checkGameEnd($session->get('playerScore'), $dealerScore);

        while ($dealerScore < 17) {
            $dealerHand[] = $deck->deal();
            $dealerScore = $this->calculateScore($dealerHand);
        }

        $session->set('deck', $deck);
        $session->set('dealerHand', $dealerHand);
        $session->set('dealerScore', $dealerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $session->get('playerScore'),
            'dealerScore' => $dealerScore,
            'end' => $end
        ]);
    }

    private function calculateScore($hand) {
        $score = 0;
        $numAces = 0;
        
        foreach ($hand as $card) {
            $rank = explode(' ', $card)[1];
            if ($rank === 'Knekt' || $rank === 'Drottning' || $rank === 'Kung') {
                $score += 10;
            } elseif ($rank === 'Ess') {
                $score += 11;
                $numAces++;
            } else {
                $score += intval($rank);
            }
        }
        while ($score > 21 && $numAces > 0) {
            $score -= 10;
            $numAces--;
        }
        return $score;
    }

    private function checkGameEnd($playerScore, $dealerScore){
        if ($playerScore > 20 || $dealerScore > 20 || $dealerScore > 16) {
            return true;
        }
    }



}

