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

class GBEntryController extends Controller
{ 
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
    		$preTxt = ($gbentry->getRef() != -1) ? "[b][color=#700]*** vom Admin bearbeitet ***[/color][/b][br]" : "";
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
    	$error = "";
    	
    	$req = $this->getRequest();
    	$form->handleRequest($req);
    	
    	
    	if ($form->isValid())
    	{
    		$data = $form->getData();
    		
    		$entry = new GBEntry();
    		$entry->setDate(new \DateTime("now"));
    		$entry->setEmail($data['email']);
    		$entry->setEntry(($data['entry']));
    		$entry->setName($data['name']);
    		
    		$em = $this->getDoctrine()->getManager();
    		
    		$em->persist($entry);
    		$em->flush();
    		
//     		return $this->redirect($this->generateUrl("_index"));
    		$msg = "Dein Gästebucheintrag war erfolgreich!<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[zurück zum Gästebuch]</a>";
    		
    		return $this->render(
    				'gaestebuch/create.html.twig',
    				array('form' => $form->createView(), 'error' => $error, 'overlay' => $msg, 'overlay_display'=>'inherit'));
    	}elseif ($form->isSubmitted()){
    		$error = (String)$form->getErrors(true);
    	}
    	
    	
    	
    	$lay = self::getLayoutDefinition($this->getRequest()->getSession());
    	
    	return $this->render(
    			'gaestebuch/create.html.twig', 
    			array('form' => $form->createView(), 'error' => $error,'csscustom'=>$lay['css'],'imgcustom'=>$lay['img']));
    }

    /**
     * @Route("/delete/{id}", name="_delete")
     */
    public function deleteAction($id)
    {
    	$session = $this->getRequest()->getSession();
    	 
    	if ($session->get("nick",false))
    	{
    		$em = $this->getDoctrine()->getManager();
    		$entity = $em->getRepository('AppBundle:GBEntry')->find($id);
    		if (!$entity) {
    			throw $this->createNotFoundException('Unable to find Phonelist entity.');
    		}
    		$em->remove($entity);
    		$em->flush();
    
    		return $this->redirect($this->getRequest()->headers->get('referer'));
    	}
    	return $this->redirect($this->generateUrl("_login"));
    }
    /**
     * @Route("/edit/{id}", name="_edit")
     */
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository("AppBundle:GBEntry")->find($id);
    	
    	$error = "";
    	
    	$form = $this->createForm(
    			new GBEntryType(true),
    			array("id"=>$id,"name"=>$entity->getName(),"email"=>$entity->getEmail(),"entry"=>$entity->getEntry())
    			);
    	
    	
    	$req = $this->getRequest();
    	$form->handleRequest($req);
    	 
    	$session = $req->getSession();
    	$lay = self::getLayoutDefinition($session);
    
    	if ($session->get("nick",false))
    	{
    		if ($form->isValid())
    		{
    			$data = $form->getData();
    			
    			$entity->setRef(1);
    			$entity->setEntry(mysql_real_escape_string($data['entry']));
    			$em->persist($entity);
    			$em->flush();
    		
//     			$entry = new GBEntry();
//     			$entry->setDate(new \DateTime("now"));
//     			$entry->setEmail($data['email']);
//     			$entry->setEntry();
//     			$entry->setName($data['name']);
//     			$entry->setRef(-2);
    		
//     			$em = $this->getDoctrine()->getManager();
//     			$entry = $em->getRepository('AppBundle:GBEntry')->find($id);
    		
//     			$em->persist($entry);
//     			$em->flush();
    		
    			$msg = "Update erfolgreich!<br /><a href=\"".$this->generateUrl("_index")."/lastpage\">[zurück zum Gästebuch]</a>";
    		
    			return $this->render(
    					'gaestebuch/create.html.twig',
    					array('form' => $form->createView(), 'error' => $error, 'overlay' => $msg, 'overlay_display'=>'inherit'));
    			
    		}
    		elseif ($form->isSubmitted())
    		{
    			$error = (String)$form->getErrors(true);
    		}
    		$em = $this->getDoctrine()->getManager();
    		$entity = $em->getRepository('AppBundle:GBEntry')->find($id);
    		if (!$entity) 
    		{
    			$error = 'Keinen Eintrag zu dieser ID gefunden.';
    		}
    		$em->flush();
    
    		return $this->render(
    			'gaestebuch/create.html.twig', 
    			array('form' => $form->createView(), 'error' => $error,'mformtitle' => "Eintrag bearbeiten",'csscustom'=>$lay['css'],'imgcustom'=>$lay['img']));
    	}
    	return $this->redirect($this->generateUrl("_login"));
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
