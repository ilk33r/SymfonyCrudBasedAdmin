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

class AjaxMultipleImageUploadType extends AbstractType
{
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		if ($options['uploadPath']) {
			$view->vars['uploadPath'] = $options['uploadPath'];
			$view->vars['uploadRoute'] = $options['uploadRoute'];
			$view->vars['deleteRoute'] = $options['deleteRoute'];
		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefined('uploadPath');
		$resolver->setDefault('uploadPath', '/');
		$resolver->setDefined('uploadRoute');
		$resolver->setDefault('uploadRoute', '');
		$resolver->setDefined('deleteRoute');
		$resolver->setDefault('deleteRoute', '');
		$resolver->setDefault('compound', false);
	}

	/*
	public function getParent()
	{
		return 'hidden';
	}*/

	public function getName()
	{
		return 'admin_ajax_multiple_file_upload';
	}

	public function getBlockPrefix() {
		return 'admin_ajax_multiple_file_upload';
	}
}