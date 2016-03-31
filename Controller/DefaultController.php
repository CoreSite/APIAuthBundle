<?php

namespace CoreSite\APIAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreSiteAPIAuthBundle:Default:index.html.twig');
    }
}
