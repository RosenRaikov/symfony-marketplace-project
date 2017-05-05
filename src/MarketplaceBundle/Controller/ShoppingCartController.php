<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Product;
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
     * @Route("/session", name="session")
     * @return Response
     */
    public function sessionTest(Request $request)
    {
        $session = $request->getSession();
        $cart = array('2', '3');
        $user = $this->getUser()->getUsername();
        $session->set("$user'sCart", $cart);
        dump($session->get("$user'sCart"));
        $session->remove('cart');
        $cart = $session->get('cart');
        foreach ($cart as $item){
            dump($this->getDoctrine()->getRepository(Product::class)->find($item));
        }

        return new Response();
    }


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
}
