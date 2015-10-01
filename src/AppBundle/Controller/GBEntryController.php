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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class GBEntryController extends Controller
{ 
	
	/**
	 * @Route("/entry/{gid}", name="_entry")
	 * @ParamConverter("entry", class="AppBundle:GBEntry", options={"id"="gid"})
	 * @param integer $id
	 */
	public function detailAction($entry)
	{
		$mgr = $this->get('guestbook_manager');
		
		$respArray = $mgr->getGuestbookFullEntry($entry);
		
		if (is_array($respArray)){
			return $this->render("gaestebuch/detail.html.twig", array(
					"entry" 	=> $respArray["entry"],
					"changed" 	=> $respArray["ref"]
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
    	$sumPages = 0;

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
    	
    	$entries = array();
    	foreach ($response AS $gbentry){
    		$preTxt = ($gbentry->getRef() != -1) ? "[b][url=".$this->generateUrl("_entry",array("gid"=>$gbentry->getId()))."][color=#700]*** vom Admin bearbeitet ***[/color][/url][/b][br]" : "";
    		$gbentry->setEntry($preTxt.$gbentry->getEntry());
    		$entries[] = $gbentry;
    	}
		return $this->render("gaestebuch/view.html.twig", array(
    			"entries" => $entries,
    			"pagelinks" => $pageLinks
    	));
    }

    /**
     * @Route("/create", name="_create")
     */
    public function createFormAction()
    {
    	$trans = $this->get("translator");
    	$form = $this->get('form.factory')->create(new GBEntryType());
    	
    	$req = $this->getRequest();
    	$form->handleRequest($req);

    	$mgr = $this->get('guestbook_manager');
    		
    	if ($mgr->addGuestbookEntry($form)){
    		$msg = $trans->trans("gbentry.success")."<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[".$trans->trans("page.back.gb")."]</a>";
    			
    		return $this->render(
    			'gaestebuch/create.html.twig',
    			array('form' => $form->createView(), 'overlay' => $msg, 'overlay_display'=>'inherit'));
    	}
    	else {
    		return $this->render(
    			'gaestebuch/create.html.twig',
    			array('form' => $form->createView(), 'error' => $mgr->getErrorMessage(),'mformtitle' => $trans->trans("gbentry.create")));
    	}
    	return $this->render(
    			'gaestebuch/create.html.twig', 
    			array('form' => $form->createView(), 'error' => $mgr->getErrorMessage(),'mformtitle' => $trans->trans("gbentry.create")));
    }

    /**
     * @Route("/delete/{gid}", name="_delete")
     * @ParamConverter("entry", class="AppBundle:GBEntry", options={"id"="gid"})
     */
    public function deleteAction(GBEntry $entry)
    {
    	$session = $this->getUser();
    	 
    	if ($session != null)
    	{
    		$mgr = $this->get('guestbook_manager');
    		if ($mgr->deleteGuestbookEntry($entry)){
    			return $this->redirect($this->getRequest()->headers->get('referer'));
    		}
    		else{
    			return new Response($mgr->getErrorMessage());
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
    	$trans = $this->get("translator");
    	$req = $this->getRequest();
    	$session = $this->getUser();
    	 
    	if ($session!= null)
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
	    				$msg = $trans->trans("gbentry.upd.suc")."<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[".$trans->trans("page.back.gb")."]</a>";
	    				return $this->render(
	    						'gaestebuch/create.html.twig',
	    						array('form' => $form->createView(), 'overlay' => $msg, 'overlay_display'=>'inherit'));
	    			case false:
	    				$error = $mgr->getErrorMessage();
	    			case null:
	    				return $this->render(
	    						'gaestebuch/create.html.twig',
	    						array('form' => $form->createView(), 'error' => $error,'mformtitle' => $trans->trans("gbentry.edit")));
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
}
