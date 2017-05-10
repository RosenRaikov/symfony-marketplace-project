<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\User;
use MarketplaceBundle\Form\EditUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("/admin/users", name="admin_all_users")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security(expression="has_role('ROLE_ADMIN')")
     */
    public function listUsers()
    {
        return $this->render('admin/allUsers.html.twig', ['users'=>$this->getDoctrine()->getRepository(User::class)->findAll()]);
    }

    /**
     * @Route("/admin/userprofile/{id}", name="admin_user_profile")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security(expression="has_role('ROLE_ADMIN')")
     */
    public function viewUserProfile(User $user){
        $ownedItems = $this->get('owned_items');
        return $this->render('admin/user_profile.html.twig', ['user' => $user, 'ownedItems' => $ownedItems]);
    }

    /**
     * @Route("/admin/remove_product/{user}/{product}", name="admin_remove_product")
     * @param $userId
     * @param $productId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeProduct($user, $product){
        $db = $this->connection();
        $query = $db->prepare('DELETE from user_products WHERE user_id = :user AND product_id = :product');
        $query->bindParam(':user', $user);
        $query->bindParam(':product', $product);
        $query->execute();
        return $this->redirectToRoute('admin_user_profile', ['id' => $user]);
    }

    public function connection(){
        return $this->get('doctrine.dbal.connection_factory')->createConnection([
            'dbname' => 'symfony_marketplace',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ]);
    }

    /**
     * @Route("/admin/edituser/{id}", name="amdin_edit_user")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Security(expression="has_role('ROLE_ADMIN')")
     */
    public function editUser(Request $request, User $user){
        $form = $this->createForm(EditUserType::class);
        $form->handleRequest($request);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_user_profile', ['id' => $user->getId()]);
        }
        return $this->render('/admin/edit_user.html.twig', ['form' => $form->createView(), 'id'=> $user->getId(), 'user' => $user]);
    }
}
