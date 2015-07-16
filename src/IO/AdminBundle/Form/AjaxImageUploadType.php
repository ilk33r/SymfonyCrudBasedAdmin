<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 17:38
 */

namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjaxImageUploadType extends AbstractType
{
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		if ($options['uploadPath']) {
			$view->vars['uploadPath'] = $options['uploadPath'];
			$view->vars['uploadRoute'] = $options['uploadRoute'];
		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefined('uploadPath');
		$resolver->setDefault('uploadPath', '/');
		$resolver->setDefined('uploadRoute');
		$resolver->setDefault('uploadRoute', '');
		$resolver->setDefault('compound', false);
	}

	/*
	public function getParent()
	{
		return 'hidden';
	}*/

	public function getName()
	{
		return 'admin_ajax_file_upload';
	}
}