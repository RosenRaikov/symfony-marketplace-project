<?php

namespace MarketplaceBundle\Controller;

use MarketplaceBundle\Entity\Comment;
use MarketplaceBundle\Entity\Product;
use MarketplaceBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    /**
     * @Route("/products/{id}/comment/add", name="leave_comment_form")
     * @Method("GET")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function leaveCommentFormAction(Product $product)
    {
        return $this->render('comments/leave_comment.html.twig', [
            'reviewForm' => $this->createForm(CommentType::class)->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/products/{id}/comment/add", name="leave_comment_process")
     * @Method("POST")
     *
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function leaveCommentProcess(Product $product, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isValid()) {
            $comment->setProduct($product);
            $comment->setAddedOn(new \DateTime());
            $comment->setUser($user->getUsername());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('single_product', ['id' => $product->getId()]);
        }

        return $this->render(':product:single_product.html.twig', [
            'product' => $product
        ]);
    }
}
