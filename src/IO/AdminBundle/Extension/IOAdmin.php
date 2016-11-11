<?php
namespace IO\AdminBundle\Extension;

class IOAdmin
{
	protected $adminPrefix;
	protected $adminMenu;
	protected $pageTitle;

	function __construct($configuration)
	{
		$this->adminPrefix = $configuration['admin_prefix'];
		$this->adminMenu = $configuration['admin_menu'];
		$this->pageTitle = $configuration['page_title'];
	}

	public function getConfiguration()
	{
		$ioAdminConfiguration = new \stdClass();
		$ioAdminConfiguration->adminPrefix = $this->adminPrefix;
		$ioAdminConfiguration->adminMenu = $this->adminMenu;
		$ioAdminConfiguration->pageTitle = $this->pageTitle;

		return $ioAdminConfiguration;
	}
}