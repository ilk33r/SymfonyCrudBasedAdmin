<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 19/07/15
 * Time: 00:00
 */

namespace Ecommerce\StandartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->remove('email');
		$builder->remove('username');
		$builder->remove('plainPassword');
		$builder
			->add('userType', 'choice', array('label'=>'Üyelik Tipi', 'choices' => array(
				'0' => 'Bireysel',
				'1' => 'Kurumsal',
			),
				'mapped'=>false)
			)
			->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
			->add('plainPassword', 'repeated', array(
				'type' => 'password',
				'options' => array('translation_domain' => 'FOSUserBundle'),
				'first_options' => array('label' => 'Şifre'),
				'second_options' => array('label' => 'Şifre (Tekrar)'),
				'invalid_message' => 'Şifre ve şifre tekrar alanları birbirini tutmuyor.',
			))
			->add('realName', 'text', array('label'=>'Ad'))
			->add('realSurname', 'text', array('label'=>'Soyad'))
			->add('taxOffice', 'text', array('label' => 'Vergi Dairesi', 'required' => false))
			->add('taxId', 'number', array('label' => 'Vergi Numarası', 'required' => false))
			->add('gender', 'choice', array('label'=>'Cinsiyet', 'choices' => array(
				'Male'   => 'Erkek',
				'Female' => 'Kadın',
			)))
			->add('birthdate', 'birthday', array(
											'format' => 'dd-MM-yyyy'
										)
			)
			->add('country', 'text', array('label'=>'Ülke'))
			->add('city', 'text', array('label'=>'Şehir'))
			->add('town', 'text', array('label'=>'İlçe'))
			->add('telephoneNumber', 'number', array('label' => 'Telefon'))
			->add('registerMailingList', 'checkbox', array(
														'label' => 'Kampanyalardan haberdar olmak istiyorum',
														'required' => false
													)
			)
			->add('userAgreement', 'checkbox', array(
												'label' => 'Üyelik sözleşmesini kabul ediyorum.',
												'mapped' => false,
												'required' => true
												)
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ecommerce\StandartBundle\Entity\User'
		));
	}

	public function getParent()
	{
		return 'fos_user_registration';
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'ecommerce_user_registration_type';
	}
}