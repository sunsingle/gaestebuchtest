<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\GBUserType;
use AppBundle\Entity\GBUserEntry;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LoginController extends Controller
{
	
	/**
	 * @Route("/loginx", name="_loginx")
	 */
	public function loginxAction(){
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
			
			$response = $rep->findBy(array("nick"=> ($data['nick']), "pass" => md5($data['pass']) ));
			
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
	 * @Route("/login", name="_login")
	 * @param Request $request
	 */
	public function loginAction()
	{
		$authenticationUtils = $this->get('security.authentication_utils');
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();
		return $this->render(
				'AppBundle:GBEntry:login.html.twig',
				array(
						// last username entered by the user
						'last_username' => $lastUsername,
						'error'         => $error,
				)
		);
	}
	/**
	 * @Route("/login_check", name="login_check")
	 */
	public function loginCheckAction(){}
	/**
	 * @Route("/logout", name="_logout")
	 */
	public function logoutAction(){
		$session = $this->getRequest()->getSession();
		
		$session->set("nick",null);
				
		return $this->redirect($this->generateUrl("_index"));
	}
}