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
		return $this->render('AppBundle:GBEntry:login.html.twig',array(
				'form' => $form->createView(), 
				'error' => $error
		));
	}
	/**
	 * @Route("/login", name="_login")
	 * @param Request $request
	 */
	public function loginAction()
	{
		$t = $this->get("translator");
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
						'error'         => $error!=""?$t->trans("form.log.err"):$error,
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
	
	/**
	 * @Route("/register", name="_register")
	 */
	public function registerAction()
	{
		$t = $this->get("translator");
		$request = $this->getRequest();
		
		$form = $this->createFormBuilder()
			->add("nick", "text")
			->add("password","repeated", array("type" => "password", "invalid_message" => $t->trans("form.pass.inval")))
			->getForm()
		;
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$mgr = $this->get('login_manager');
			$mgr->add_encoder($this->get("security.encoder_factory"));
			
			$result = $mgr->registerUser($form);
	    	switch($result){
	    		case true:
	    			$msg = $t->trans("form.reg.suc")."<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[".$t->trans("page.back.gb")."]</a>";
	    			return $this->render(
	    					'AppBundle:GBEntry:register.html.twig',
	    					array('form' => $form->createView(), 'overlay' => $msg, 'overlay_display'=>'inherit'));
	    		case false:
	    			$error = $mgr->getErrorMessage();
	    		case null:
	    			return $this->render(
	    					'AppBundle:GBEntry:register.html.twig',
	    					array('form' => $form->createView(), 'error' => $error,'mformtitle' => "Eintrag bearbeiten"));
	    		}
			
		}
		
		return $this->render("AppBundle:GBEntry:register.html.twig",array("form"=>$form->createView(),"error"=>$mgr->getErrorMessage()));
	}
	
	private function encodePassword(GBUserEntry $user, $plainPassword, $salt = false)
	{
		$encoder = $this->container->get("security.encoder_factory")
						->getEncoder($user);
		
		return $encoder->encodePassword($plainPassword, $salt?$salt:$user->getSalt());
	}
}