<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Product;
use PDO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use MarketplaceBundle\Entity\User;
class ShoppingCartController extends Controller
{
    /**
     * @Route("/addToCart/{id}", name="cart_add")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCart(Request $request, $id){
        $session = $request->getSession();
        $cart = $session->get('cart');
        $cart[] = $id;
        $session->set("cart", $cart);
        return $this->redirectToRoute('single_product', ['id' => $id]);
    }

    /**
     * @Route("/cart", name="cart")
     * @param Request $request
     * @return Response
     */public function viewCart(Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get("cart");
        $products = [];
        $value = 0;
        $calc = $this->get('price_calculator');
        if ($cart != null) {
            foreach ($cart as $id) {
                $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
                $value += $calc->calculate($product);
                $products[] = $product;
            }
        } else{

        }
        return $this->render('cart/cart.html.twig', ['products' => $products, 'value' => $value, 'calc' => $calc]);
    }

    /**
     * @Route("/removeFromCart/{id}", name="remove_from_cart")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromCart(Request $request, $id){
        $session = $request->getSession();
        $cart = $session->get("cart");

        if(($key = array_search($id, $cart)) !== false) {
            unset($cart[$key]);
        }
        $session->set('cart', $cart);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/buy", name="buy")
     * @return Response
     */
    public function buy(Request $request){
        $user = $this->getUser();
        $session = $request->getSession();
        $cart = $session->get("cart");
        $ownedProducts = $user->getProducts();
        foreach ($cart as $product){
            $product = $this->getDoctrine()->getRepository(Product::class)->find($product);
            $calc = $this->get('price_calculator');
            $price = $calc->calculate($product);

            if ($user->getWallet() >= $price) {
                if (in_array($product, $ownedProducts)) {
                    $this->updateCount($user, $product);
                    $this->removeFromCart($request, $product->getId());
                } else {
                    $user->setWallet($user->getWallet() - $price);
                    $user->setProducts($product);
                    $product->setStock($product->getStock()-1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->persist($product);
                    $em->flush();
                    $this->removeFromCart($request, $product->getId());

                }
            }
        }
        return $this->redirectToRoute('cart');
    }


    public function updateCount(User $user, Product $product){
        $currentCount = $this->fetchCount($user, $product);
        $count = $currentCount + 1;
        $userId = $user->getId();
        $productId = $product->getId();
        $db = $this->connection();
        $query = $db->prepare("UPDATE user_products SET count = :count WHERE user_id = :user AND product_id = :product");
        $query->bindParam(':user', $userId);
        $query->bindParam(':product', $productId);
        $query->bindParam(':count', $count);
        $query->execute();
    }


    public function fetchCount(User $user, Product $product){
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

    public function connection(){
        return $this->get('doctrine.dbal.connection_factory')->createConnection([
            'dbname' => 'symfony_marketplace',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ]);
    }
}
