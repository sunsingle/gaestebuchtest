<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/style/{name}", name="_style")
     */
    public function styleAction($name)
    {
        $session = $this->getRequest()->getSession();
        $session->set("lay", $name);
        
        $referer = $this->getRequest()->headers->get('referer');
        
        if ($referer != "")
        	return $this->redirect($referer);
        
        return $this->redirect($this->generateUrl("_index"));
    }
    
    
}
