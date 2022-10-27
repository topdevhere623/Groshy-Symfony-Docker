<?php

namespace Groshy\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
{
    /**
     * @Route("/terms")
     */
    public function termsAction(): Response
    {
        return $this->render('content/terms.html.twig', []);
    }

    /**
     * @Route("/policy")
     */
    public function policyAction(): Response
    {
        return $this->render('content/policy.html.twig', []);
    }
}
