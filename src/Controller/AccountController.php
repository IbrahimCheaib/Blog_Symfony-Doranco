<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="account")
     * @return Response
     */
    public function account(): Response
    {
        
        return $this->render('account/account.html.twig'); // render() est une fonction qui prend en paramètre obligatoire une vue
    }
}
