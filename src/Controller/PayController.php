<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayController extends AbstractController
{
    #[Route('/cart/pay', name: 'pay')]
    public function pay($stripeSk, Request $request): Response
    {
        $cube = function ($paniers){
            $result=[];
                foreach($paniers as $panier){
                    array_push($result,[
                    'quantity' => $panier['qte'],
                    'price_data' => [
                        'currency' => 'EUR',
                        'product_data' => [
                            'name' => $panier['article']->getNom(),
                        ],
                        'unit_amount' => $panier['article']->getPrix().'00'
                    ]]);
                }
                return $result;
        };
        // j'initialise ma session
        $session = $request->getSession();

        // je recupere mon tableaux panier
        $paniers = $session->get('panier', []);

        // je recupere mon total des article
        $total = $session->get('total', []);

        // je recupere mon le quantité d'article
        $qte = $session->get('qte', []);
        
        
        Stripe::setApiKey($stripeSk);

        $session = Session::create([
            'line_items' => [
                $cube($paniers)
            ],
            'mode' => 'payment',
            'customer_email' => $this->getUser()->getEmail(),
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR']
            ]
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('cart/pay/success', name: 'success_url')]
    public function paySuccess(Request $request): Response
    {
        // j'initialise ma session
        $session = $request->getSession();

        $session->remove('panier','total','qte');

        return $this->render('pay/index.html.twig',[
        ]);
    }

    #[Route('cart/pay/cancel', name: 'cancel_url')]
    public function payCancel(): Response
    {
        return $this->render('pay/cancel.html.twig', [
        ]);
    }
}
