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
        // j'initialise ma session
        $session = $request->getSession();

        // je recupere mon tableaux panier
        $panier = $session->get('panier', []);

        // je recupere mon total des article
        $total = $session->get('total', []);

        // je recupere mon le quantité d'article
        $qte = $session->get('qte', []);
        
        
        Stripe::setApiKey($stripeSk);

        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('cart/pay/success', name: 'success_url')]
    public function paySuccess(): Response
    {

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
