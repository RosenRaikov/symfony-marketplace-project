<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Category;
use MarketplaceBundle\Entity\Product;
use MarketplaceBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="list_categories")
     * @Security(expression="has_role('ROLE_EDITOR')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategories(){
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('product/list_categories.html.twig', ['categories' => $categories]);
    }


    /**
     * @Route("/categories/add", name="category_add")
     * @Security(expression="has_role('ROLE_EDITOR')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCategoryAction(Request $request)
    {

        $category = new Category();
        $categoryform = $this->createForm(CategoryType::class, $category);
        $categoryform->handleRequest($request);
        if ($categoryform->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('list_categories');
        }

        return $this->render('product/category_add.html.twig', ['categoryForm' => $categoryform->createView()]);
    }

    /**
     * @Route("/categories/delete{id}", name="category_delete")
     * @Security(expression="has_role('ROLE_EDITOR')")
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategoryAction(Category $category){
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('list_categories');
    }

}
