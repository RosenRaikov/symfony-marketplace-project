<?php

namespace MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{

    /**
     * @Route("/user/login", name="login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }


    /**
     * @Route("/user/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('homepage');
    }
}
