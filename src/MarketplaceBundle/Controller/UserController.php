<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Product;
use MarketplaceBundle\Entity\Role;
use MarketplaceBundle\Entity\User;
use MarketplaceBundle\Form\UserType;
use PDO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        if ($form->isValid()) {
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
    public function userProfile()
    {
        $user = $this->getUser();
        $ownedItems = $this->get('owned_items');
        return $this->render('user/profile.html.twig', ['user' => $user, 'ownedItems' => $ownedItems]);
    }


    public function fetchCount(User $user, Product $product)
    {
        $userId = $user->getId();
        $productId = $product->getId();
        $db = $this->connection();
        $query = $db->prepare("SELECT count FROM user_products WHERE user_id = :user AND product_id = :product");
        $query->bindParam(':user', $userId);
        $query->bindParam(':product', $productId);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['count'];
    }

    private function connection()
    {
        return $this->get('doctrine.dbal.connection_factory')->createConnection([
            'driver' => 'pdo_mysql',
            'dbname' => 'symfony_marketplace',
            'user' => 'root',
            'password' => '',
            'host' => '127.0.0.1',
            'port' => '3306'
        ]);
    }

    /**
     * @Route("/forsale/{user}/{product}", name="put_for_sale")
     * @param User $user
     * @param Product $product
     */
    public function putForSale($user, $product){
        $sale = 1;
        $db = $this->connection();
        $query = $db->prepare("UPDATE user_products SET for_sale = :sale WHERE user_id = :user AND product_id = :product");
        $query->bindParam(':user', $user);
        $query->bindParam(':product', $product);
        $query->bindParam(':sale', $sale);
        $query->execute();

        return $this->redirectToRoute('user_profile');
    }

}


