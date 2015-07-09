<?php

namespace FOSUserBundle\ExtendedUsersBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FOSUserBundleExtendedUsersBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
