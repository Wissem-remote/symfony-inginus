<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'nav' => 'acceuil'
        ]);
    }

    #[Route('/store', name: 'store')]
    public function store(): Response
    {
        return $this->render('home/store.html.twig', [
            'nav' => 'store'
        ]);
    }

    #[Route('/tools', name: 'tools')]
    public function tools(): Response
    {
        return $this->render('home/tools.html.twig', [
            'nav' => 'tools'
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'nav' => 'contact'
        ]);
    }
}
