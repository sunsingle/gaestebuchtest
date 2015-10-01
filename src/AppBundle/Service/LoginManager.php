<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\GBUserEntry;
use Symfony\Component\DependencyInjection\ContainerInterface AS Container;
class LoginManager 
{
	private $container		= null;
	private $emanager 		= null;
	private $errormessage 	= "";
	private $encoder		= null; 
	
	function __construct(Container $container, EntityManager $em){
		$this->emanager = $em;
	}
	public function getErrorMessage(){
		return $this->errormessage;
	}
	public function add_encoder($enc){
		$this->encoder = $enc;
	}
	
	public function setTheme(GBUserEntry $user)
	{
		$this->emanager->persist($user);
		$this->emanager->flush();
	}
	
	/**
	 * @param FormInterface $form
	 * @return int|false ID of inserted data OR success
	 */
	public function registerUser(FormInterface $form)
	{
		if ($this->encoder == null)
			throw new \Exception("Encoder can not be NULL! Do you added the Encoder by using add_encoder() ??");
		
		if ($form->isValid()){
			$data = $form->getData();
	
			$entity = new GBUserEntry();
			$entity->setNick($data['nick']);
			$entity->setPass($this->encodePassword($entity, $data['password']));
			
				
			try{
				$this->emanager->persist($entity);
				$this->emanager->flush();
			
				return $entity->getUid();
			}
			catch(\Exception $ex){
				$this->errormessage = $ex->getMessage();
				if (strstr($this->errormessage, "Duplicate"))
					$this->errormessage = "Dieser Nick wird bereits verwendet!";
				return false;
			}
		}
		elseif ($form->isSubmitted()){
			$this->errormessage = (String)$form->getErrors(true);
		}
		return false;
	}
	private function encodePassword(GBUserEntry $user, $plainPassword, $salt = false)
	{
		$encoder = $this->encoder->getEncoder($user);
		return $encoder->encodePassword($plainPassword, $salt?$salt:$user->getSalt());
	}
}