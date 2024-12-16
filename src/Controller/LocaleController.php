<?php

namespace App\Controller;

use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher,
    ) {
    }

    #[Route('/switch-locale/{locale}', name: 'app_switch_locale')]
    public function switchLocale(string $locale): RedirectResponse
    {
        $this->localeSwitcher->setLocale($locale);
        return $this->redirectToRoute('app_home');
}
}