<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class GBUserType extends AbstractType 
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('nick',	'text',			array("label" => "Name: ",			"max_length" => 10))
		->add('pass',	'password',		array("label" => "Passwort: ",		"max_length" => 30))
		->add('submit',	'submit',		array("label" => "Login"))
		;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return 'gbuser';
	}
}