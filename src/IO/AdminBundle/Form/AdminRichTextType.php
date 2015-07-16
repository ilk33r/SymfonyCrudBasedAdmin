<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 20:37
 */

namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;

class AdminRichTextType extends AbstractType
{
	public function getParent()
	{
		return 'textarea';
	}

	public function getName()
	{
		return 'admin_richText';
	}
}