<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsers()
    {
        return $this->render('admin/allUsers.html.twig', ['users'=>$this->getDoctrine()->getRepository(User::class)->findAll()]);
    }
}
