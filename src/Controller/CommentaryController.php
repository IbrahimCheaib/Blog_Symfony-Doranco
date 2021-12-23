<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\CommentaryType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaryController extends AbstractController
{
    /**
     * @Route("/add_comment/?article_id={id}", name="form_comment", methods={"GET|POST"})
     * @param Request $request
     * @return Response
     */
    public function addComment(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentary = new Commentary();

        $form = $this->createForm(CommentaryType::class, $commentary)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentary = $form->getData();

            $commentary->setAuthor($this->getUser());
            $commentary->setCreatedAt(new DateTime());

            $commentary->setArticle($article);

            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez commentez l\'article !');
            return $this->redirectToRoute('show_article', [
                'id' => $article->getId()
            ]);
            
        } // end if

        return $this->render('rendered/form_commentary.html.twig', [
            'form' => $form->createView()
        ]);
    } // end function

} // end class