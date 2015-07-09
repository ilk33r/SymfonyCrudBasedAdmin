<?php

namespace IO\AdminBundle\Extension;

use IO\AdminBundle\Extension\IOAdmin;

//use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;

class IOAdminTwigExtension extends \Twig_Extension
{
	protected $ioAdmin;

	function __construct(IOAdmin $ioAdmin) {
		$this->ioAdmin = $ioAdmin;
	}

	public function getGlobals() {
		return array(
			'io_admin' => $this->ioAdmin->getConfiguration(),
		);
	}

	public function getName()
	{
		return 'io_admin';
	}

}