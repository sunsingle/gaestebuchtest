<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\GBUserType;
use AppBundle\Entity\GBUserEntry;

class LoginController extends Controller
{
	/**
	 * @Route("/login", name="_login")
	 */
	public function loginAction(){
		$session = $this->getRequest()->getSession();
		
		$form = $this->get('form.factory')->create(new GBUserType());
		$error = "";
		 
		$req = $this->getRequest();
		$form->handleRequest($req);
		 
		 
		if ($form->isValid())
		{
			$data = $form->getData();
		
		
			$em = $this->getDoctrine()->getManager();
			
			$rep = $em->getRepository("AppBundle:GBUserEntry");
			
			$response = $rep->findBy(array("nick"=> mysql_real_escape_string($data['nick']), "pass" => md5($data['pass']) ));
			
			if (count($response))
			{
				$session->set("nick",$data['nick']);
				
				return $this->redirect($this->generateUrl("_index"));
			}
			else {
				$error = "Login-Daten nicht korrekt. Bitte versuch es erneut!";
			}
		}
		elseif ($form->isSubmitted())
		{
			$error = (String)$form->getErrors(true);
		}
		$lay = GBEntryController::getLayoutDefinition($session);
		return $this->render('AppBundle:GBEntry:login.html.twig',array(
				'form' => $form->createView(), 
				'error' => $error,
				'csscustom'=>$lay['css'],
				'imgcustom'=>$lay['img']
		));
	}
	/**
	 * @Route("/logout", name="_logout")
	 */
	public function logoutAction(){
		$session = $this->getRequest()->getSession();
		
		$session->set("nick",null);
				
		return $this->redirect($this->generateUrl("_index"));
	}
}