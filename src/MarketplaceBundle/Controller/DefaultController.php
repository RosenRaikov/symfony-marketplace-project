<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Category;
use MarketplaceBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(){
        $product = $this->getDoctrine()->getRepository(Product::class)->findBy(['category'=>'1']);
        dump($product);
        return new Response();
    }


    /**
     * @Route("/products")
     * @throws \LogicException
     */
    public function listAllProducts()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        return $this->render('product/test.html.twig', ['products' => $products, 'categories' => $categories, 'calc' => $calc, 'max_promotion' => $max_promotion]);
    }
}
