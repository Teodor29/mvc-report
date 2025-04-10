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
}
