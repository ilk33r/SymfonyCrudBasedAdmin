<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 20:37
 */

namespace IO\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminRichTextType extends AbstractType
{
	public function getParent()
	{
		return TextareaType::class;
	}

	public function getName()
	{
		return 'admin_rich_text';
	}
}