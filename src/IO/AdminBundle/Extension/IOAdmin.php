<?php
namespace IO\AdminBundle\Extension;

class IOAdmin
{
	protected $adminPrefix;
	protected $adminMenu;

	function __construct($configuration)
	{
		$this->adminPrefix = $configuration['admin_prefix'];
		$this->adminMenu = $configuration['admin_menu'];
	}

	public function getConfiguration()
	{
		$ioAdminConfiguration = new \stdClass();
		$ioAdminConfiguration->adminPrefix = $this->adminPrefix;
		$ioAdminConfiguration->adminMenu = $this->adminMenu;

		return $ioAdminConfiguration;
	}
}