<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Category;
use MarketplaceBundle\Entity\Promotion;
use MarketplaceBundle\Form\PromotionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PromotionController extends Controller
{
    /**
     * @Route("/promotions", name="promotions")
     * @Security(expression="has_role('ROLE_EDITOR')")
     * @return Response
     */
    public function managePromotions()
    {
        $promotions = $this->getDoctrine()->getRepository(Promotion::class)->findAll();
        return $this->render('promotions/all_promotions.html.twig', ['promotions' => $promotions]);
    }

    /**
     * @Route("/promotions/add", name="promotion_add")
     * @Security(expression="has_role('ROLE_EDITOR')")
     */
    public function addPromotion(Request $request){
        $promotion = new Promotion();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);

        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();
            return $this->redirectToRoute('promotions');
        }
        return $this->render('promotions/promotion_add.html.twig', ['form' => $form->createView(), 'categories' => $categories]);
    }

    /**
     * @Route("/promotions/delete/{id}", name="promotion_delete")
     * @Security(expression="has_role('ROLE_EDITOR')")
     *
     * @param $id
     */
    public function deletePromotion($id){
        $promotion = $this->getDoctrine()->getRepository(Promotion::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($promotion);
        $em->flush();
        return $this->redirectToRoute('promotions');
    }

    /**
     * @Route("/promotions/edit/{id}", name="promotion_edit")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPromotion(Request $request, $id){
        $promotion = $this->getDoctrine()->getRepository(Promotion::class)->find($id);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);

        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();
            return $this->redirectToRoute('promotions');
        }
        return $this->render('promotions/promotion_edit.html.twig', ['form' => $form->createView(), 'categories' => $categories]);
    }
}
