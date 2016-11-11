<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 19:55
 */


namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class AdminNumberType extends AbstractType
{

	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		if ($options['size']) {
			$view->vars['size'] = $options['size'];
		}

		if ($options['step']) {
			$view->vars['step'] = $options['step'];
		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefault('required', false);
		$resolver->setDefault('size', 18);
		$resolver->setDefault('step', '0.05');
		$resolver->setDefault('compound', false);
	}

	public function getParent()
	{
		return NumberType::class;
	}

	public function getName()
	{
		return 'admin_number';
	}

	public function getBlockPrefix() {
		return 'admin_number';
	}
}