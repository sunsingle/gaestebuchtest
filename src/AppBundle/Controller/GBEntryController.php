<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\GBEntry;
use AppBundle\Form\GBEntryType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\SymfonyBridgeAdapter;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use AppBundle\Service\GuestbookManager;

class GBEntryController extends Controller
{ 
	
	/**
	 * @Route("/entry/{id}", name="_entry")
	 * @param integer $id
	 */
	public function detailAction($id)
	{
		$session = $this->getRequest()->getSession();
		$mgr = $this->get('guestbook_manager');
		
		$respArray = $mgr->getGuestbookFullEntry($id);
		
		if (is_array($respArray)){
			$lay = self::getLayoutDefinition($session);
			 
			return $this->render("gaestebuch/detail.html.twig", array(
					"entry" 	=> $respArray["entry"],
					"changed" 	=> $respArray["ref"],
					'csscustom'	=>$lay['css'],
					'imgcustom'	=>$lay['img']
			));
		}
		else{
			return new Response("Nope",404);
		}
	}
	
    /**
     * @Route("/show/{page}", name="_index", defaults={"page" = 1} )
     */
    public function viewAction($page)
    {
    	$sumPages = $login = false;
    	$session = $this->getRequest()->getSession();
    	
    	$mgr = $this->get('guestbook_manager');
    	
    	$response = $mgr->getGuestbook($page,$sumPages);
    	 
    	$pageLinks = array();
    		
    	for($i = 1; $i <= $sumPages; $i++)
    	{
    			$link = array();
    			$link["num"] = $i;
    			$link["active"] = $page!=$i;
    			
    			$pageLinks[] = $link;
    	}


    	if ($session->get("nick",false))
    		$login = true;
    	
    	$entries = array();
    	foreach ($response AS $gbentry){
    		$preTxt = ($gbentry->getRef() != -1) ? "[b][url=".$this->generateUrl("_entry",array("id"=>$gbentry->getId()))."][color=#700]*** vom Admin bearbeitet ***[/color][/url][/b][br]" : "";
    		$gbentry->setEntry($preTxt.$gbentry->getEntry());
    		$entries[] = $gbentry;
    	}
		$lay = self::getLayoutDefinition($session);
    	
    	return $this->render("gaestebuch/view.html.twig", array(
    			"entries" => $entries,
    			"pagelinks" => $pageLinks,
    			"login" => $login,
    			'csscustom'=>$lay['css'],
    			'imgcustom'=>$lay['img']
    	));
    }

    /**
     * @Route("/create", name="_create")
     */
    public function createFormAction()
    {
    	$form = $this->get('form.factory')->create(new GBEntryType());
    	
    	$req = $this->getRequest();
    	$form->handleRequest($req);

    	$mgr = $this->get('guestbook_manager');
    		
    	if ($mgr->addGuestbookEntry($form)){
    		$msg = "Dein Gästebucheintrag war erfolgreich!<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[zurück zum Gästebuch]</a>";
    			
    		return $this->render(
    			'gaestebuch/create.html.twig',
    			array('form' => $form->createView(), 'overlay' => $msg, 'overlay_display'=>'inherit'));
    	}
    	else {
    		return $this->render(
    			'gaestebuch/create.html.twig',
    			array('form' => $form->createView(), 'error' => $mgr->getErrorMessage()));
    	}

    	$lay = self::getLayoutDefinition($this->getRequest()->getSession());
    	
    	return $this->render(
    			'gaestebuch/create.html.twig', 
    			array('form' => $form->createView(), 'error' => $mgr->getErrorMessage(),'csscustom'=>$lay['css'],'imgcustom'=>$lay['img']));
    }

    /**
     * @Route("/delete/{id}", name="_delete")
     */
    public function deleteAction($id)
    {
    	$session = $this->getRequest()->getSession();
    	 
    	if ($session->get("nick",false))
    	{
    		$mgr = $this->get('guestbook_manager');
    		if ($mgr->deleteGuestbookEntry($id)){
    			return $this->redirect($this->getRequest()->headers->get('referer'));
    		}
    		else{
    			// TODO Show Error 
    			// $mgr->getErrorMessage()
    		}
    	}
    	return $this->redirect($this->generateUrl("_login"));
    }
    /**
     * @Route("/edit/{id}", name="_edit")
     */
    public function editAction($id)
    {
    	$req = $this->getRequest();
    	$session = $req->getSession();
    	$lay = self::getLayoutDefinition($session);
    	 
    	if ($session->get("nick",false))
    	{
	    	$mgr = $this->get('guestbook_manager');
	    	$entity = $mgr->getGuestbookEntry($id);
	    	if ($entity){
	    		$form = $this->createForm(
	    				new GBEntryType(true),
	    				array("id"=>$entity->getId() ,"name"=>$entity->getName(),"email"=>$entity->getEmail(),"entry"=>$entity->getEntry())
	    		);
	    		$form->handleRequest($req);
	    		$result = $mgr->editGuestbookEntry($form, $entity);
	    		switch($result){
	    			case true:
	    				$msg = "Update erfolgreich!<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[zurück zum Gästebuch]</a>";
	    				return $this->render(
	    						'gaestebuch/create.html.twig',
	    						array('form' => $form->createView(), 'overlay' => $msg, 'overlay_display'=>'inherit'));
	    			case false:
	    				$error = $mgr->getErrorMessage();
	    			case null:
	    				return $this->render(
	    						'gaestebuch/create.html.twig',
	    						array('form' => $form->createView(), 'error' => $error,'mformtitle' => "Eintrag bearbeiten",'csscustom'=>$lay['css'],'imgcustom'=>$lay['img']));
	    		}
	    	}
	    	else{
	    		$error = $mgr->getErrorMessage();
	    	}
    	}
    	else{
    		return $this->redirect($this->generateUrl("_login"));
    	}
    }
    		 
    public static function getLayoutDefinition(Session $session)
    {
    	$lay = $session->get("lay","def");
    	switch($lay){
    		case "def": 
    			$img = "4"; $css = "def"; break;
    		case "blu":
    			$img = "2"; $css = "blu"; break;
    		case "stn":
    			$img = "3"; $css = "stn"; break;
    		default:
    			$img = "4"; $css = "def";
    	}
    	
        	
    	return array("css" => $css, "img" => $img);
    }
}
