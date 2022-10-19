<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticleRepository $article, SessionInterface $session): Response
    {
        $articles = array_reverse($article->findBy(['type' => 'Article'],[],6));
        $tools = array_reverse($article->findBy(['type' => 'Accessoire'],[],6));
        
        $panier = $session->get('panier',[]);
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'tools' => $tools,
            'panier' => $panier
        ]);
    }

    #[Route('/store/{page?1}/{nb?6}', name: 'store')]
    public function store(ArticleRepository $article,$page,$nb): Response
    {
        $articles = $article->findBy(['type' => 'Article']);
        //$articleActuel = $article->findByPage('Article',$nb,($page - 1) * $nb,$prix);
        $articleActuel = $article->findBy(['type' => 'Article'],[],$nb,($page - 1) * $nb);
        $nbArticles =count($articles);
        $nbPage = ceil($nbArticles / $nb);
        
        return $this->render('home/store.html.twig', [
            'nav' => 'store',
            'articles' => $articleActuel,
            'isPagination' =>  $nbArticles > 6 ? true : false,
            'nbPage' => $nbPage,
            'page' => $page,
            'nb' => $nb
        ]);
    }

    #[Route('/tools/{page?1}/{nb?6}', name: 'tools')]
    public function tools(ArticleRepository $article, $page, $nb): Response
    {
        $articles = $article->findBy(['type' => 'Accessoire']);
        //$articleActuel = $article->findByPage('Article',$nb,($page - 1) * $nb,$prix);
        $articleActuel = $article->findBy(['type' => 'Accessoire'], [], $nb, ($page - 1) * $nb);
        $nbArticles = count($articles);
        $nbPage = ceil($nbArticles / $nb);
       
        return $this->render('home/tools.html.twig', [
            'nav' => 'tools',
            'articles' => $articleActuel,
            'isPagination' =>  $nbArticles > 6 ? true : false,
            'nbPage' => $nbPage,
            'page' => $page,
            'nb' => $nb
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('object', TextType::class)
            ->add('message', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data =$form->getData();
            $email = (new TemplatedEmail())
                ->from($data['email'])
                ->to('inginus76@gmail.com')
                ->htmlTemplate('mail/contact.html.twig')
                ->context([
                'name' => $data['name'],
                'mail' => $data['email'],
                'objet' => $data['object'],
                'message' => $data['message']
                ]);

            $mailer->send($email);
            

            $this->addFlash('message_success','Votre Message à été envoyer');

            return $this->redirect($request->getUri());
        }

        return $this->render('home/contact.html.twig', [
            'nav' => 'contact',
            'form' => $form->createView()
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
