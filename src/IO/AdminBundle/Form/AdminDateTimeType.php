<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 19:55
 */


namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AdminDateTimeType extends AbstractType
{
	public function getParent()
	{
		return DateTimeType::class;
	}

	public function getName()
	{
		return 'admin_date_time';
	}
}