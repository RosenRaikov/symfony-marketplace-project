<?php

namespace MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{

    /**
     *
     * @Route("/unsecured", name="secured")
     * @return Response
     */
    public function unsecured()
    {
        var_dump('this is unsecured');
        return new Response();
    }

    /**
     * @Route("/secured", name="secured")
     * @Security(expression="has_role('ROLE_USER')")
     * @return Response
     */
    public function secured()
    {
        var_dump('this is secured');
        return new Response();
    }
}
