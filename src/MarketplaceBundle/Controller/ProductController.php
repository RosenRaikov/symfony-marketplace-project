<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Category;
use MarketplaceBundle\Entity\Product;
use MarketplaceBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    /**
     * @Route("/products/add", name="product_add")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security(expression="has_role('ROLE_EDITOR')")
     */
    public function createProductAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        if($form->isValid()){
            $product->setAdditionDate(new \DateTime('now'));
            dump($product);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render('product/product_add.html.twig', ['productForm' => $form->createView(), 'categories' => $categories]);
    }

    /**
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAllProducts()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $calc = $this->get('price_calculator');
        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        return $this->render('product/list_all.html.twig', ['products' => $products, 'categories' => $categories, 'calc' => $calc, 'max_promotion' => $max_promotion]);
    }

    /**
     * @Route("/products/{id}", name="single_product")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function singleProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product === null){
            $this->redirectToRoute('homepage');
        }
        $calc = $this->get('price_calculator');
        return $this->render('product/single_product.html.twig', ['product' => $product, 'calc' => $calc]);
    }

    /**
     * @Route("/products/edit/{id}", name="product_edit")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security(expression="has_role('ROLE_EDITOR')")
     */
    public function editProductAction(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        if ($product===null){
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('single_product', ['id' => $product->getId()]);
        }

        return $this->render('product/edit_product.html.twig',
            array('product' => $product,
                'form' => $form->createView(),
                'categories' => $categories
            ));
    }

    /**
     * @Route("/products/delete/{id}", name="product_delete")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security(expression="has_role('ROLE_EDITOR')")
     */
    public function deleteProductAction($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null){
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('product/product_delete.html.twig', ['product' => $product, 'form' => $form->createView()]);
    }

    /**
     * @Route("/products/category/{id}", name="products_by_category")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productsByCategory($id){
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['category'=>$id]);
        $category = $products[0]->getCategory();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $calc = $this->get('price_calculator');
        return $this->render('product/product_category.html.twig', ['products' => $products, 'category' => $category, 'categories' => $categories, 'calc' => $calc]);
    }


    /**
     * @Route("/product/manager", name="products_manager")
     * @Security(expression="has_role('ROLE_EDITOR')")
     */
    public function productManager(){
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $calc = $this->get('price_calculator');
        return $this->render('product/product_manager.html.twig', ['products' => $products, 'calc' => $calc]);
    }
}
