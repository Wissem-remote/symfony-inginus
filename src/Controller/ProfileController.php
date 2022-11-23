<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Order;
use App\Entity\User;
use App\Form\MessageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $orders = $doctrine->getRepository(Order::class)->findBy(['user' => $this->getUser()]);

        return $this->render('profile/index.html.twig', [
            'orders' => array_reverse($orders),
        ]);
    }
    #[Route('/profile/order', name: 'profile_order')]
    public function order(ManagerRegistry $doctrine): Response
    {
        $orders = $doctrine->getRepository(Order::class)->findBy(['user' => $this->getUser()]);

        return $this->render('profile/order.html.twig', [
            'orders' => array_reverse($orders),
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
    public function orderMessage(Order $order, Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): Response
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

            // on envoie un email pour confirmer l'envoie message
            $email = (new TemplatedEmail())
            ->from('inginus76@gmail.com')
                ->to($this->getUser()->getEmail())
                ->htmlTemplate('mail/message.html.twig')
                ->context([
                    'name' => $this->getUser()->getName(),
                    'objet' => $data['objet'],
                    'message' =>  $data['message']
                ]);

            $mailer->send($email);
            // on ajoute nos information dans notre objet
            $message->setCreatedAt(new \DateTimeImmutable);
            
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
        $order = $doctrine->getRepository(Order::class)->findby(['user' => $this->getUser()]);
        $message= [];
        foreach ($order as  $value) {
            $orders = $doctrine->getRepository(Message::class)->findBy(['orders' => $value]);
            if(!empty($orders)){
                array_push($message,$orders);
            }
        }
        return $this->render('profile/message.html.twig', [
            'messages' => array_reverse($message),
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
            // dump($info);
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
    #[Route('/profile/profile', name: 'profile_setProfile')]
    public function setProfile( ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $doctrine->getRepository(User::class)->find($this->getUser()->getId());
        $em = $doctrine->getManager();
        
        // dump($user);
        $form = $this->createFormBuilder()
                    ->add('oldPassword', PasswordType::class)
                    ->add('newPassword', PasswordType::class, [
                                // instead of being set onto the object directly,
                                // this is read and encoded in the controller
                                'mapped' => false,
                                'attr' => ['autocomplete' => 'new-password'],
                                'constraints' => [
                                    new NotBlank([
                                        'message' => "S'il vous plait entrer votre mot passe.",
                                    ]),
                                    new Regex([
                                        'pattern' => '/(?=[0-9a-zA-Z.*]+$)^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.{14,}).*$/',
                                        'message' => "Le mot de passe doit contenir 14 caractères et une majuscule un minuscule et un chiffre",
                                    ]),
                                ],
                            ])
                    ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data  = $form->getData();
            $userNewPassword = $userPasswordHasher->hashPassword($user, $data['newPassword']);

            if($userPasswordHasher->isPasswordValid($user, $data['oldPassword'])){
                $user->setPassword($userNewPassword);
                // dump($user);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success_pass',"Félicitation votre mot-passe vient d'etre modifier !");
                
            }else{
                $this->addFlash('warning_pass', "Désoler votre mot-passe ne correspond pas !");
            }
        }
        return $this->render('profile/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
