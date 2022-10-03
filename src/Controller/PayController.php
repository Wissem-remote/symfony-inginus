<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;
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

        
        
        
        Stripe::setApiKey($stripeSk);

        $sessions = Session::create([
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

        $session->set('stripe',[$sessions->id]);

        return $this->redirect($sessions->url, 303);
        
    }

    #[Route('cart/pay/success', name: 'success_url')]
    public function paySuccess(Request $request, $stripeSk, ManagerRegistry $doctrine): Response
    {
        // j'initialise ma session
        $session = $request->getSession();

        // je récupere mon tableaux panier
        $paniers = $session->get('panier', []);

        //on requpere la quantité final

        $qte = 0;

        foreach ($paniers as $key => $value) {
            $qte += $value['qte'];
        }

        // je récupere mon total des article
        $total = $session->get('total', []);

        // on récupere id de la dérnier commande passé
        $idStripe = $session->get('stripe',[]);

        if (empty($idStripe)){
            return $this->redirectToRoute('home');
        }

        // on créee un stripe client grace à notre clé
        $client = new StripeClient($stripeSk);

        // on recupére le checkout session en retrive grace id de la dérnier commande
        $result= $client->checkout->sessions->retrieve($idStripe[0]);

        $adressLivraison = $result->customer_details->address;

        $emailLivraison = $result->customer_details->email;
        
        $nameLivraison = $result->customer_details->name;

        $adressDetail = $result->shipping_details->address;

        $nameDetail = $result->shipping_details->name;

        $reference = chr(substr("000" . (rand(1, 9) + 65), -3)).rand(1000,9999);

        $order = new Order;

        $em = $doctrine->getManager();


        $order->setPanier($paniers)
            ->setReference($reference)
            ->setTotal($total[0])
            ->setCreatedateAt(new \DateTimeImmutable)
            ->setUser($this->getUser())
            ->setAdressLivraison([$adressLivraison])
            ->setAdressFacturation([$adressDetail])
            ->setNameLivraison($nameLivraison)
            ->setNameFacturation($nameDetail)
            ->setEmail($emailLivraison)
            ->setState(false)
            ->setStateSending('en cours')
            ->setQuantity($qte);
            
        
        $em->persist($order);

        $em->flush();
        

        return $this->render('pay/index.html.twig',[
            'paniers' => $paniers,
            'total' => $total,
            'adressLivraison' => $adressLivraison,
            'emailLivraisson' => $emailLivraison,
            'nameLivraison' => $nameLivraison,
            'adressDetail' => $adressDetail,
            'nameDetail' => $nameDetail,
            'reference' => $reference
        ]);

        
    }

    #[Route('cart/pay/cancel', name: 'cancel_url')]
    public function payCancel(): Response
    {
        return $this->render('pay/cancel.html.twig', [
        ]);
    }
    #[Route('cart/pay/back', name: 'back_url')]
    public function payBack(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('panier');
        $session->remove('total');
        $session->remove('stripe');
        return $this->redirectToRoute('home');
    }
}
