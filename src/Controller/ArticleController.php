<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ArticleController extends AbstractController
{
    #[Route('/admin', name: 'admin')]

    public function index(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findby([ 'type' => 'Article']);
        return $this->render('admin/index.html.twig', [
            'articles' => array_reverse($articles)
        ]);
    }

    #[Route('/admin/tools', name: 'admin_tools')]

    public function tools(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findby(['type' => 'Accessoire']);
        return $this->render('admin/index.html.twig', [
            'articles' => array_reverse($articles)
        ]);
    }

    #[Route('/admin/article/show/{id}', name: 'admin_show_article')]
    public function showArticle(Article $article): Response
    {
        
        return $this->render('admin/show_article.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/admin/article/add', name: 'admin_add_article')]
    public function addArticle(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article;
        $em = $doctrine->getManager();
        $form = $this->createForm(ArticleType::class,$article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Image
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('annonce_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
            }


            // encode the plain password
            $article->setCreateDateAt(new \DateTimeImmutable);
            $em->persist($article);
            $em->flush();
            // do anything else you need here, like send an email
            $this->addFlash('success_update', 'Votre Article à été ajouter');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/add_article.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/article/update/{id}', name: 'admin_update_article')]
    public function upDateArticle(Article $article,ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
   
        $em = $doctrine->getManager();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Image
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                // suprimer la photo déja extant
                $filesystem = new Filesystem();
                $projectDir = $this->getParameter('kernel.project_dir');
                $filesystem->remove($projectDir . '/public/uploads/article/' . $article->getImage() );

                // on créer une nouvelle image
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('annonce_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
            }


            // encode the plain password
            $article->setCreateDateAt(new \DateTimeImmutable);
            $em->persist($article);
            $em->flush();
            // do anything else you need here, like send an email
            $this->addFlash('success_update', 'Votre Article à été modifier');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/add_article.html.twig', [
            'form' => $form->createView(),
            'update' => 'ok'
        ]);
    }
    #[Route('admin/article/delete/{id}', name: 'admin_delete_article')]
    public function deleteAnnonce(Article $article, ManagerRegistry $doctrine): Response
    {


        if ($article->getImage() != null) {
            $filesystem = new Filesystem();
            $projectDir = $this->getParameter('kernel.project_dir');
            $filesystem->remove($projectDir . '/public/uploads/article/' . $article->getImage());
        }

        $em = $doctrine->getManager();

        $em->remove($article);

        $em->flush();

        $this->addFlash('success_delete', 'Votre article à été suprimer');

        return $this->redirectToRoute('admin');
    }

}
