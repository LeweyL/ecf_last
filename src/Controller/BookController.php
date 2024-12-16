<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use App\Entity\Comment;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    public function reserve(Book $book): Response
    {
        if ($book->isAvailable()) {
            $book->setIsAvailable(false);
            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livre a été réservé avec succès.');
        } else {
            $this->addFlash('error', 'Le livre n\'est pas disponible.');
        }

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

    
public function addCommentRedirect(int $id, BookRepository $bookRepository): Response
{
    $book = $bookRepository->find($id);

    if (!$book) {
        throw $this->createNotFoundException('Le livre n\'existe pas.');
    }

    return $this->render('book/add_comment.html.twig', [
        'book' => $book
    ]);
}
#[Route('/book/{id}/comment', name: 'submit_comment', methods: ['POST'])]
    public function submitComment(Request $request, Book $book, EntityManagerInterface $em)
    {
        $comment = new Comment();
        $comment->setBook($book);
        $comment->setContent($request->request->get('content'));
        $comment->setAuthor($request->request->get('author'));
        $comment->setCreatedAt(new \DateTime());

        $note = $request->request->get('note');
        if ($note !== null) {
            $comment->setRating((int) $note);
        }

        $em->persist($comment);
        if ($note !== null) {
            $this->updateBookRating($book, $em);
        }

        $em->flush();

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

    private function updateBookRating(Book $book, EntityManagerInterface $em)
    {
        $comments = $book->getComments();
        $totalRating = 0;
        $count = 0;

        foreach ($comments as $comment) {
            if ($comment->getRating() !== null) {
                $totalRating += $comment->getRating();
                $count++;
            }
        }

        if ($count > 0) {
            $book->setRating($totalRating / $count);
            $em->flush();
        }
    }

    public function removeComment(int $bookId, int $commentId): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($bookId);
        $comment = $this->entityManager->getRepository(Comment::class)->find($commentId);

        if (!$book || !$comment || $comment->getBook() !== $book) {
            $this->addFlash('error', 'Impossible de trouver le commentaire ou le livre.');
            return $this->redirectToRoute('book_show', ['id' => $bookId]);
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé avec succès.');

        return $this->redirectToRoute('book_show', ['id' => $bookId]);
    }

}