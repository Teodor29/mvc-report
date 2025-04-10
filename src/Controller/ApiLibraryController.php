<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiLibraryController extends AbstractController
{

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