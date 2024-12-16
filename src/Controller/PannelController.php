<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Book;
use App\Entity\User;
use App\Form\RoomType;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PannelController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $rooms = $entityManager->getRepository(Room::class)->findAll();
        $books = $entityManager->getRepository(Book::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'rooms' => $rooms,
            'books' => $books,
            'users' => $users,
        ]);
    }

    #[Route('/admin/room/{id}/edit', name: 'edit_room')]
    public function editRoom(Room $room, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/edit_room.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/book/{id}/edit', name: 'edit_book')]
public function editBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->render('admin/edit_book.html.twig', [
        'form' => $form->createView(),
        'book' => $book,
    ]);
}


    #[Route('/admin/user/{id}/edit', name: 'edit_user')]
    public function editUser(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/edit_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/user/{id}/toggle-admin-role', name: 'toggle_admin_role', methods: ['POST'])]
    public function toggleAdminRole(User $user, EntityManagerInterface $entityManager): Response
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
        } else {
            $roles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($roles);
        $entityManager->flush();
        return $this->redirectToRoute('edit_user', ['id' => $user->getId()]);
    }

    #[Route('/admin/delete/room/{id}', name: 'delete_room')]
    public function deleteRoom(Room $room, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($room);
        $entityManager->flush();
        
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/delete/book/{id}', name: 'delete_book')]
    public function deleteBook(Book $book, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/delete/user/{id}', name: 'delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/room/add', name: 'add_room')]
    public function addRoom(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/add_room.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/book/add', name: 'add_book')]
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
{
    $book = new Book();
    $book->setAvailability(true); 
    $book->setRating(null);
    $book->setLastComment(null);

    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->render('admin/add_book.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
