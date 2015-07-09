<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 06/07/15
 * Time: 19:17
 */

namespace ilkerozcan\PortfolioBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser
{

	public function __construct()
	{
		parent::__construct();
		// your own logic

		$this->roles = array('ROLE_USER');
	}

}