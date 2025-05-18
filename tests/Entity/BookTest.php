<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testGetAndSetTitle(): void
    {
        $book = new Book();
        $title = "test";

        $book->setTitle($title);

        $this->assertEquals($title, $book->getTitle());
    }

    public function testGetAndSetIsbn(): void
    {
        $book = new Book();
        $isbn = "12345";

        $book->setIsbn($isbn);

        $this->assertEquals($isbn, $book->getIsbn());
    }

    public function testGetAndSetAuthor(): void
    {
        $book = new Book();
        $author = "test";

        $book->setAuthor($author);

        $this->assertEquals($author, $book->getAuthor());
    }

    public function testGetAndSetImage(): void
    {
        $book = new Book();
        $image = "test.jpg";

        $book->setImage($image);

        $this->assertEquals($image, $book->getImage());
    }

    public function testGetId(): void
    {
        $book = new Book();

        $this->assertNull($book->getId());
    }
}