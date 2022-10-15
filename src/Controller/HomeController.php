<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticleRepository $article, SessionInterface $session): Response
    {
        $articles = array_reverse($article->findAll(['type' => 'Article']));
        
        $panier = $session->get('panier',[]);
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'panier' => $panier
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

    #[Route('/article/{id}', name: 'article')]
    public function article(Article $article, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        return $this->render('home/article.html.twig', [
            'article' => $article,
            'panier' => $panier
        ]);
    }
}
