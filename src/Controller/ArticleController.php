<?php

namespace App\Controller;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\EditArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager){
           $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/article", name="create_article")
     * @param Request $request
     * @return Response
     */
    public function createArticle(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            # Association de l'article au user : setOwner()
            //

            # Association de l'article à la category : setOwner()
            //

            $article->setCreatedAt(new \DateTime());

            # Coder ici la logique pour uploader la photo
            //

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success','Article ajouter!');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/form_article.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/modifier/article/{id}", name="edit_article")
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function editArticle(Article $article, Request $request): Response
    {
        # Supprimer le edit form et utiliser ArticleType (configurer les options) : pas besoin de dupliquer un form
        $form = $this->createForm(EditArticleType::class, $article)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            # Créer une nouvelle propriété dans l'entité : setUpdatedAt()

            $this->entityManager->persist($article);
            $this->entityManager->flush();

        }

        return $this->render('article/edit_article.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/voir/article/{id}", name="show_article")
     * @param Article $singleArticle
     * @return Response
     */
    public function showArticle(Article $singleArticle): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->find($singleArticle->getId());

        return $this->render('article/show_article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin/supprimer/article/{id}", name="delete_article")
     * @param Article $article
     * @return Response
     */
    public function deleteArticle(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $this->addFlash('success','Article supprimé !');

        return $this->redirectToRoute('dashboard');
    }
}
