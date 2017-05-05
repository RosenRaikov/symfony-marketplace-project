<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Role;
use MarketplaceBundle\Entity\User;
use MarketplaceBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/user/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $encoder = $this->get('security.password_encoder');

       if ($form->isValid()){
           $hashedPassword = $encoder->encodePassword($user, $user->getPassword());
           $user->setPassword($hashedPassword);
           $userRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_USER']);
           dump($userRole);
           $user->addRole($userRole);
           $user->setWallet(500);
           $em = $this->getDoctrine()->getManager();
           $em->persist($user);
           $em->flush();
           return $this->redirectToRoute('login');
       }

        return $this->render('user/register.html.twig', ['registerForm' => $form->createView()]);
    }

    /**
     * @Route("/profile", name="user_profile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userProfile(){
        $user = $this->getUser();
        return $this->render('user/profile.html.twig', ['user' => $user]);
    }
}
