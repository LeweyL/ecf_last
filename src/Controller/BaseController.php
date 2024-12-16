<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function index(): RedirectResponse
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        
        return $this->redirectToRoute('app_home', ['_locale' => $locale]);
    }
}