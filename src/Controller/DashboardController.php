<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\EditArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/dashboard", name="dashboard")
     * @return Response
     */
    public function dashboard(): Response
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();

        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('dashboard/dashboard.html.twig',
            [
                'articles' => $articles,
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/admin/supprimer/user/{id}", name="delete_user")
     * @param User $user
     * @return Response
     */
    public function deleteUser(User $user): Response
    {
          $this->entityManager->remove($user);
          $this->entityManager->flush();

          $this->addFlash('success','Utilisateur supprimé !');

          return $this->redirectToRoute('dashboard');
    }

}
