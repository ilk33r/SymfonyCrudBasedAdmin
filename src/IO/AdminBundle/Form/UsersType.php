<?php

namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder->add('username', null, array('mapped' => false));
	    $builder->add('email', EmailType::class, array('mapped' => false));
	    $builder->add('plainPassword', PasswordType::class, array('mapped' => false, 'required'=>false));
	    $builder->add('userRole', ChoiceType::class, array(
		    'choices' => array('ROLE_USER'=>'User', 'ROLE_ADMIN'=>'Admin'),
		    'mapped' => false,
	    ));
	    $builder->add('enabled', ChoiceType::class, array(
		    'choices' => array(0=>'No', 1=>'Yes'),
		    'mapped' => false
	    ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'io_admin_users';
    }
}
