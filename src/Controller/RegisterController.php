<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request): Response // response indique que cette fonction doit renvoyer une reponse (return)
    {

        // Nouvelle instance de la class User (entity)
        $user = new User();

        // Matérialisation du formulaire RegisterType::class
        $form = $this->createForm(RegisterType::class,$user);

        // handleRequest sert à recuperer les données du formulaire (email,roles,password)
        $form->handleRequest($request);

        // Si le formulaire et soumis et valide alors on execute le code à l'intérieur
       if ($form->isSubmitted() && $form->isValid()) {
          
        $user = $form->getData();// getData permet de recuperer les donnes du formulaire
   
        
        $user->setPassword( // setPassword permet d envoyer le mot de passe en bdd

            $this->passwordHasher->hashPassword($user,$user->getPassword())
            // hashPassword permet de hasher notre mot de passe
        );

        $this->entityManager->persist($user); // envoie en base de données
        $this->entityManager->flush(); // vide l'entité
        
        return $this->redirectToRoute('app_login');

       }

        return $this->render('register/register.html.twig',[
            'form'=> $form->createView(),// donne en paramettre la variable $form ( qui stock notre formulaire de la class RegisterType)
        ]);
    }
   
}
