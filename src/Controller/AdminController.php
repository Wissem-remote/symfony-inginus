<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/order', name: 'admin_order')]
    public function order(ManagerRegistry $doctrine): Response
    {
        $order = $doctrine->getRepository(Order::class)->findAll();
        $notificationSend = $doctrine->getRepository(Message::class)->findby(['state' => false]);
        $notificationOrder = $doctrine->getRepository(Order::class)->findby(['state' => false]);
        return $this->render('admin/order.html.twig', [
            'orders' => array_reverse($order),
            'notificationSend' => $notificationSend,
            'notificationOrder' => $notificationOrder
        ]);
    }
    #[Route('/admin/order/delete/{id}', name: 'admin_order_delete')]
    public function orderDelete(ManagerRegistry $doctrine, Order $order)
    {
        $em = $doctrine->getManager();

        $em->remove($order);
        $em->flush();
        $this->addFlash('success_delete', 'la commande à été suprimer');
        return $this->reDirectToRoute('admin_order');
    }
    
    #[Route('/admin/order/update/{id}', name: 'admin_order_update')]
    public function orderUpdate(Order $order, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $form = $this->createFormBuilder($order)
            ->add('stateSending', ChoiceType::class, [
            'choices'  => [
                'En Cours Traitement' => 'En Cours Traitement',
                'En Cours Livraisson' => 'En Cours Livraisson',
                'Déjà Livré' => 'Déjà Livré'
            ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setState(true);
            $em->persist($order);
            $em->flush();

            $this->addFlash('success_message', 'Félicitation votre Commande à été traité');

            return $this->redirectToRoute('admin_order');
        }
        return $this->render('admin/order_update.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/message', name: 'admin_messager')]
    public function message(ManagerRegistry $doctrine): Response
    {
        $message = $doctrine->getRepository(Message::class)->findAll();
        $notificationSend = $doctrine->getRepository(Message::class)->findby(['state' => false]);
        $notificationOrder = $doctrine->getRepository(Order::class)->findby(['state' => false]);
        return $this->render('admin/message.html.twig', [
            'messagers' => array_reverse($message),
            'notificationSend' => $notificationSend,
            'notificationOrder' => $notificationOrder
        ]);
    }

    #[Route('/admin/message/update/{id}', name: 'admin_messager_update')]
    public function messageUpdate(Message $message,Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $form = $this->createFormBuilder()
                ->add('message', TextareaType::class)
                ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data  = $form->getData();
            $info=[];
            $info += $message->getContent();
            array_push($info,[$data['message'],'admin']);
            $message->setState(true);
            $message->setContent($info);
            $em->persist($message);
            $em->flush();

            $this->addFlash('success_message','Félicitation votre Message à été envoyer');

            return $this->redirectToRoute('admin_messager');
        }
        return $this->render('admin/message_update.html.twig', [
            'form' =>  $form->createView(),
            'messager' => $message
        ]);
    }
}
