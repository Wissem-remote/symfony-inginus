<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(Request $request): Response
    {
        // j'initialise ma session
        $session = $request->getSession();


        // j'ajoute mon tableaux panier
        $panier = $session->get('panier', []);

        $total = 0;

        if(!empty($panier)){
            foreach ($panier as  $value) {
                $total += $value['article']->getPrix() * $value['qte'];
            }
        }
        return $this->render('cart/index.html.twig', [
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}/{origin}', name: 'cart_add')]
    public function add(Article $article, $origin,Request $request)
    {
        

        // j'initialise ma session
        $session = $request->getSession();
        
        
        // j'ajoute mon tableaux panier
        $panier = $session->get('panier', []);

        // je verifie si article n'exite pas s'il existe je l'incrémente
        if(empty($panier[$article->getId()])){
            $panier[$article->getId()] = [
                'article' => $article,
                'qte' => 1
            ];
            
        }else{
            $panier[$article->getId()]=[
                'article' => $article,
                'qte' => ++$panier[$article->getId()]['qte']
            ];
        }

        $session->set('panier',$panier);

        return $this->redirectToRoute($origin);
        
    }

    #[Route('/cart/remove/{id}/{origin}', name: 'cart_remove')]
    public function remove(Article $article, $origin, Request $request)
    {
        // j'initialise ma session
        $session = $request->getSession();


        // j'ajoute mon tableaux panier
        $panier = $session->get('panier', []);

        // je verifie si article  est inferieur égal à 1 je le unset 
        if ($panier[$article->getId()]['qte'] <= 1) {
            unset($panier[$article->getId()]);
        } else {
            $panier[$article->getId()] = [
                'article' => $article,
                'qte' => --$panier[$article->getId()]['qte']
            ];
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute($origin);
    }
}
