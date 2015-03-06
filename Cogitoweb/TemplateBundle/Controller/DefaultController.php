<?php

namespace Cogitoweb\TemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CogitowebTemplateBundle:Default:index.html.twig', array());
    }
}
