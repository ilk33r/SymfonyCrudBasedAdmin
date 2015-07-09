<?php

namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('username', 'text', array('mapped' => false));
		$builder->add('email', 'email', array('mapped' => false));
		$builder->add('plainPassword', 'password', array('mapped' => false, 'required'=>false));
		$builder->add('userRole', 'choice', array(
			'choices' => array('ROLE_USER'=>'User', 'ROLE_ADMIN'=>'Admin'),
			'mapped' => false,
		));
		$builder->add('enabled', 'choice', array(
			'choices' => array(0=>'No', 1=>'Yes'),
			'mapped' => false
		));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
	/*
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ilkerozcan\PortfolioBundle\Entity\SiteSettings'
        ));
    }*/

    /**
     * @return string
     */
    public function getName()
    {
        return 'io_admin_users';
    }
}
