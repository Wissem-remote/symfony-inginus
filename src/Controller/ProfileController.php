<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $orders = $doctrine->getRepository(Order::class)->findBy(['user' => $this->getUser()]);

        return $this->render('profile/index.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/profile/order', name: 'profile_order')]
    public function order(ManagerRegistry $doctrine): Response
    {
        $orders = $doctrine->getRepository(Order::class)->findBy(['user' => $this->getUser()]);

        return $this->render('profile/order.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/profile/order/check/{id}', name: 'profile_order_detail')]
    public function orderDetail(Order $order): Response
    {
        dump($order);
        return $this->render('profile/order_detail.html.twig', [
            'order' => $order,
        ]);
    }
}
