<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GBEntryType extends AbstractType 
{
	private $mEditForm;
	/**
	 * @param boolean $editForm 
	 */
	function __construct($editForm = false){
		$this->mEditForm = $editForm;
	}
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('name',	'text',			array("label" => "Dein Name: ",			"max_length" => 30,"read_only" => $this->mEditForm, "disabled" => $this->mEditForm))
		->add('email',	'email',		array("label" => "Email-Adresse: ",		"max_length" => 30,"read_only" => $this->mEditForm, "disabled" => $this->mEditForm, "required"=>false))
		->add('entry',	'textarea',		array("label" => "Dein Gästebucheintrag: ","attr"=> array("class"=>"form_textarea"),"max_length" => 30))
		->add('submit',	'submit',		array("label" => $this->mEditForm?"Gästebucheintrag ändern":"Gästebucheintag absenden"))
		;
	}
 
	
	/**
	 * @return string
	 */
	public function getName() {
		return 'gbentry';
	}
}