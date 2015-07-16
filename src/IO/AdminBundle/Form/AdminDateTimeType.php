<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 19:55
 */


namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;

class AdminDateTimeType extends AbstractType
{
	public function getParent()
	{
		return 'datetime';
	}

	public function getName()
	{
		return 'admin_date_time';
	}
}