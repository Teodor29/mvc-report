<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\Card;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

        $deck = $session->get("deck");

        if (!$deck) {
            $DeckOfCards = new DeckOfCards();
            $deck = $DeckOfCards->shuffle();
            $session->set('deck', $deck);
        }

        $card = new Card($deck, 1);
        $result = $card->drawCards();
        $cards = $result['cards'];
        $remainingCards = count($result['deck']);
        $session->set('deck', $result['deck']);
        $session->set('remainingCards', $remainingCards);


        $data = [
            "cards" => $cards,
            "remainingCards" => $remainingCards,
        ];
        
        return $this->render('blackjack.html.twig', $data);
    }

}

