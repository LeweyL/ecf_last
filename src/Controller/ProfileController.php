<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view this page.');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/subscribe/{id}/{duration}', name: 'subscribe_user', methods: ['POST'])]
public function subscribe(User $user, string $duration, EntityManagerInterface $entityManager): Response
{
    if ($duration === '1month') {
        $user->setEndSubscription(new \DateTime('+1 month'));
    } elseif ($duration === '1year') {
        $user->setEndSubscription(new \DateTime('+1 year'));
    }

    $user->setSubscribed(true);
    $entityManager->flush();

    $this->addFlash('success', 'Votre abonnement a été activé avec succès !');
    return $this->redirectToRoute('app_profile');
}

    #[Route('/unsubscribe/{id}', name: 'unsubscribe_user', methods: ['POST'])]
    public function unsubscribe(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setSubscribed(false);
        $user->setEndSubscription(null);
        $entityManager->flush();

        $this->addFlash('success', 'Vous vous êtes désabonné avec succès.');
        return $this->redirectToRoute('app_profile');
    }


    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view this page.');
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
