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


class cardsController extends AbstractController {
    

   #[Route("/card", name: "card")]
    public function card(): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "deck")]
    public function deck(
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

        sort($deck);

        $data = [
            "deck" => $deck,
        ];

        return $this->render('deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "shuffle")]
    public function shuffle(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $DeckOfCards = new DeckOfCards();

        $deck = $DeckOfCards->shuffle();


        $session->set('deck', $deck);

        $data = [
            "deck" => $session->get("deck"),
        ];

        return $this->render('shuffle.html.twig', $data);
    }

    #[Route("/card/deck/draw", name: "draw")]
    public function draw(
        Request $request,
        SessionInterface $session
    ): Response
    {

        $deck = $session->get("deck");

        if (!$session->get("deck")) {
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
        
        return $this->render('draw.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "drawNumber")]
    public function drawNumber(
        int $num,
        Request $request,
        SessionInterface $session
        ): Response
    {
        $num = $request->get('num');
        $remainingCards = $session->get("remainingCards");
        if ($num > $remainingCards) {
            throw new \Exception("Can not get more than {{$remainingCards}} cards!");
        }
    

        $deck = $session->get("deck");
        $card = new Card($deck, $num);
        $result = $card->drawCards();
        $cards = $result['cards'];
        $remainingCards = count($result['deck']);
        $session->set('deck', $result['deck']);
        $session->set('remainingCards', $remainingCards);


        $data = [
            "cards" => $cards,
            "remainingCards" => $remainingCards,
        ];
        

        return $this->render('draw.html.twig', $data);
    }

    #[Route("/card/deck/reset", name: "reset")]
    public function reset(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $session->clear();

        return $this->render('card.html.twig');
    }

}

