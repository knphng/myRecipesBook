<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Recipe;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $recipes = $this->getDoctrine()
          ->getRepository('AppBundle:Recipe')
          ->findAll();

        return $this->render('recipes/index.html.twig', array('recipes' => $recipes));
    }
}
