<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/add-admin/{id}', name: 'add_admin_role')]
    public function addAdminRole(User $user, EntityManagerInterface $entityManager): Response
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $roles = $user->getRoles();
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_pannel');
    }
}
