<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Order;
use App\Form\MessageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('profile/order_detail.html.twig', [
            'order' => $order,
        ]);
    }
    #[Route('/profile/order/message/{id}', name: 'profile_order_message')]
    public function orderMessage(Order $order, Request $request, ManagerRegistry $doctrine): Response
    {
        $message = new Message;
        // on créer notre formulaire
        $form = $this->createForm(MessageType::class);
        // on recupére l'entity Manager Interface de Doctrine
        $em = $doctrine->getManager();
        // on recupere les information passer dans notre formulaire passé
        $form->handleRequest($request);
        // on test notre formulaire si il à été validé 
        if ($form->isSubmitted() && $form->isValid()) {
            // je recupere le contenue de mon formulaire ['objet'] et ['message']
            $data = $form->getData();
            
            // on ajoute nos information dans notre objet
            $message->setCreatedAt(new \DateTimeImmutable);
            $message->setUser($this->getUser());
            $message->setOrders($order);
            $message->setState(false);
            $message->setObjet($data['objet']);
            $message->setContent([[$data['message'],'client']]);
            //dump($data);
            
            $em->persist($message);
            $em->flush();

            // on a fait passer un message flash
            $this->addFlash('success_message', 'Votre Message à été envoyer');

            // on redirige vers l'acceuil du profile 
            return $this->redirectToRoute('profile');
        }
        return $this->render('profile/order_message.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/message', name: 'profile_message')]
    public function message(ManagerRegistry $doctrine): Response
    {
        $messages = $doctrine->getRepository(Message::class)->findBy(['user' => $this->getUser()]);

        return $this->render('profile/message.html.twig', [
            'messages' => array_reverse($messages),
        ]);
    }
    #[Route('/profile/message/{id}', name: 'profile_message_detail')]
    public function messageDetail(ManagerRegistry $doctrine,Request $request, Message $message): Response
    {
        $em = $doctrine->getManager();
        $form = $this->createFormBuilder()
            ->add('message', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data  = $form->getData();
            $info = [];
            $info += $message->getContent();
            array_push($info,[$data['message'],'client']);
            $message->setState(false);
            $message->setContent($info);
            dump($info);
            $em->persist($message);
            $em->flush();

            $this->addFlash('success_message', 'Félicitation votre Message à été envoyer');

            return $this->redirectToRoute('profile');
        }
        
        return $this->render('profile/message_detail.html.twig', [
            'messager' => $message,
            'form' => $form->createView()
        ]);
    }
}
