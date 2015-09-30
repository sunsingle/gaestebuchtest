<?php
namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\GBEntry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Form\GBEntryType;
use Symfony\Component\Form\FormFactoryInterface;

class GuestbookManager
{
	private $emanager	= null;
	private $mEntriesPerPage;
	
	private $errormessage = "";
	
	function __construct($maxEntries,EntityManager $em){
		$this->mEntriesPerPage = $maxEntries;
		$this->emanager = $em;
	}
	
	public function getErrorMessage(){
		return $this->errormessage;
	}
	
	public function testService(){
		return "TEstergebnis (Entries: ".$this->mEntriesPerPage.")";
	}
	
	/**
	 * @param GBEntry $entry
	 */
	public function getGuestbookFullEntry(GBEntry $entry)
	{
		$ref = null;
		if ($entry != null){
			if ($entry->getRef() != -1){
				$ref = $this->getGuestbookEntry($entry->getRef());
			}
			return array("entry" => $entry, "ref" => $ref);
		}
		$this->errormessage = "Entry could not be found!";
		return false;
	}
	
	/**
	 * @param integer $id
	 * @return object|null The entity instance or NULL if the entity can not be found
	 */
	public function getGuestbookEntry($id)
	{
		$entity = $this->emanager->getRepository("AppBundle:GBEntry")->find($id);
		if ($entity == null)
			$this->errormessage = "Kein Gästebucheintrag zur ID $id gefunden!";
		return $entity;
	}
	
	/**
	 * 
	 * @param FormInterface $form
	 * @param EntityRepository $entity
	 */
	public function editGuestbookEntry(FormInterface $form, GBEntry $entity)
	{
		if ($form->isValid()){
			if ($entity->getRef() != -1){
				$this->deleteGuestbookEntry($this->getGuestbookEntry($entity->getRef()));
			}
			$refId = $this->addGuestbookEntryAsData($entity->getName(), $entity->getEmail(), $entity->getEntry(), -2);
			
			$data = $form->getData();
			
			$entity->setRef($refId);
			$entity->setEntry($data['entry']);
			
			try{
				$this->emanager->persist($entity);
				$this->emanager->flush();
				return true;
			}
			catch(Exception $ex){
				$this->errormessage = $ex->getMessage();
				return false;
			}
		}
		elseif ($form->isSubmitted()){
			$this->errormessage = (String)$form->getErrors(true);
			return false;
		}
		return null;
	}
	
	/**
	 * @param GBEntry $entry GuestbookEntry to delete
	 * @return boolean success
	 */
	public function deleteGuestbookEntry(GBEntry $entry)
	{
		if (!$entry) {
			$this->errormessage = "Eintrag nicht gefunden!";
			return false;
		}
		try{
			$ref = $entry->getRef();
			if ($ref != -1) {
				$rep = $this->emanager->getRepository('AppBundle:GBEntry');
				$entity = $rep->find($ref);

				if ($ref != -2)
					$this->deleteGuestbookEntry($entity);
				$this->emanager->remove($entry);
				$this->emanager->flush();
			}
		}catch(Exception $ex){
			$this->errormessage = $ex->getMessage();
			return false;
		}
		return true;
	}
	
	/**
	 * @param FormInterface $form
	 * @return int|false ID of inserted data OR success
	 */
	public function addGuestbookEntry(FormInterface $form, $refId = -1)
	{
		if ($form->isValid()){
			$data = $form->getData();
			
			return $this->addGuestbookEntryAsData($data['name'], $data['email'], $data['entry'], $refId);
		}
		elseif ($form->isSubmitted()){
			$this->errormessage = (String)$form->getErrors(true);
		}
	}
	/**
	 * @param String $name
	 * @param String $email
	 * @param String $entry
	 * @param integer $ref
	 * @return int|false ID of inserted data OR success
	 */
	private function addGuestbookEntryAsData($name,$email,$entry,$ref)
	{
		$entity = new GBEntry();
		$entity->setRef($ref);
		$entity->setDate(new \DateTime("now"));
		$entity->setName($name);
		$entity->setEntry($entry);
		$entity->setEmail($email);
			
		try{
			$this->emanager->persist($entity);
			$this->emanager->flush();
	
			return $entity->getId();
		}
		catch(Exception $ex){
			$this->errormessage = $ex->getMessage();
			return false;
		}
	}
	
	/**
	 * @param number $page Actual Page
	 * @param number $sumPages sum pages as reference
	 * @return \Doctrine\ORM\EntityRepository 
	 */
	public function getGuestbook($page = 1, &$sumPages = 1)
	{
		$rep = $this->emanager->getRepository("AppBundle:GBEntry");
		$qb = $rep->createQueryBuilder('a');
		$qb->select('COUNT(a)');
		$qb->where($qb->expr()->not('a.ref=-2'));
		
		$count = $qb->getQuery()->getSingleScalarResult();
		 
		if ($count > $this->mEntriesPerPage)
		{
			$sumPages = ceil($count / $this->mEntriesPerPage);
		
			if(!is_numeric($page))
			{
				if ($page == "lastpage")
					$page = $sumPages;
				else
					$page = 0;
			}
			if ($page > $sumPages)
				$page = $sumPages;
			elseif($page < 1)
				$page = 1;
			
			
			
			$qb = $rep->createQueryBuilder('a');
			$qb->where($qb->expr()->not('a.ref=-2'));
			$qb->addOrderBy("a.id","ASC");
			$qb->setFirstResult(($page-1)*$this->mEntriesPerPage);
			$qb->setMaxResults($this->mEntriesPerPage);

			return $qb->getQuery()->getResult();
			
			return $rep->findBy(
					array('refNot'=>-2),
					array('id'=>'ASC'),
					$this->mEntriesPerPage,
					($page-1)*$this->mEntriesPerPage
					);
		}
		return $rep->findAll();
	}

}