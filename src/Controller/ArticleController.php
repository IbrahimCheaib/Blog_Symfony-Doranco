<?php

namespace App\Controller;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\EditArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function createArticle(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            $article->setUser($this->getUser()); // avec setUser va modeliser ou paramettrer tous ce qui est dans get user

            

            # Association de l'article au user : setOwner()
            //

            # Association de l'article à la category : setOwner()
            //

            $article->setCreatedAt(new \DateTime());

            # Coder ici la logique pour uploader la photo


            // on recupere le fichier du formulaire grace a getData(). cela nous retourne un objet de type UploadFile.

            $file = $form->get('picture')->getData();

            // dd($file);

            // condition qui verifie si un fichier est present dans le formulaire

            if($file) {

                // Generer une contrainte d'upload. On declare un array avec deux valeurs de type string qui sont les MimeType autorises'.
                // Vous retrouvez tous les MimeType existant sur internet (mozilla developper)
               
                // $allowedMimeType = ['image/jpeg', 'image/png'];  // extensions: jpeg, png ....


                // la fonction native in_array() permet de comparer deux valeurs (2 arguments attendus)
                //if(in_array($file->getMimeType(), $allowedMimeType)) {
                    
                    // nous allons construire le nouveau nom du fichier

                    // on stocke dans une variable $originalFilename le nom du fichier.
                    // on utilise encore une fonction native pathinfo()
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
 
                    #recuperation de l'extension pour pouvoir reconstruire le nom quelques lignes apres.
                    // on utilise la concatenation pour ajouter un point '.' 
                    $extension = '.' . $file->guessExtension();
                    # Assainissement du nom grace au slugger fourni par symfony pour la construction du nouveau nom 
                    // $safeFilename = $slugger->slug($article->getTitle()); 
                    $safeFilename = $slugger->slug($originalFilename);

                    // dump($safeFilename);
                    #construction du nouveau nom 
                    // uniqid() est une fonction native qui permet de generer un id unique.
                    $newFilename = $safeFilename . '_' . uniqid() . $extension;

                    // dd($newFilename);

                    /*
                        on utilise un try catch(){} lorsqu'on appelle une methode qui lance une erreur.
                    
                    */
                    try {


                        /* on appelle la methode move() de UploadFile pour pouvoir deplacer le fichier dans son dossier de destination.
                             le dossier de destination a ete' parametre' dans service.yaml
                        /!\ Attention:
                                La methode move lance une erreur de type FileException.
                                on attrape cette erreur dans le catch(FileException $exception)
                        */
                        $file->move($this->getParameter('uploads_dir'), $newFilename);

                        // On set la nouvelle valeur (nom du fichier) de la propriete' picture de notre objet Article.
                        $article->setPicture($newFilename);

                    } catch (FileException $exception) {
                        // code a executer si une erreur est attrapee'.
                       
                    }

                // }
                // // Si ce n'est pas le bon type de fichier uploade' alors on affiche un message et redirige.
                // else {
                //     $this->addFlash('warning', 'Les types de fichier autorises sont : .jpeg / .png');
                //     return $this->redirectToRoute('create_article');
                // }
            }

            $this->entityManager->persist($article);  // persister les donnes'
            $this->entityManager->flush();   // cest pour vider pour le remplir avec un prochain article

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
