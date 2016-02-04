<?php

namespace IO\AdminBundle\Extension;

use IO\AdminBundle\Extension\IOAdmin;
use Twig_Environment;
use Twig_NodeVisitorInterface;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Twig_SimpleTest;
use Twig_TokenParserInterface;

//use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;

/**
 * Class IOAdminTwigExtension
 * @package IO\AdminBundle\Extension
 */
class IOAdminTwigExtension implements \Twig_ExtensionInterface, \Twig_Extension_GlobalsInterface
{
	/**
	 * @var IOAdmin
	 */
	protected $ioAdmin;

	/**
	 * @param IOAdmin $ioAdmin
	 */
	function __construct(IOAdmin $ioAdmin) {
		$this->ioAdmin = $ioAdmin;
	}

	/**
	 * @return array
	 */
	public function getGlobals() {
		return array(
			'io_admin' => $this->ioAdmin->getConfiguration(),
		);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'io_admin';
	}

	/**
	 * Initializes the runtime environment.
	 *
	 * This is where you can load some file that contains filter functions for instance.
	 *
	 * @param Twig_Environment $environment The current Twig_Environment instance
	 *
	 * @deprecated since 1.23 (to be removed in 2.0), implement Twig_Extension_InitRuntimeInterace instead
	 */
	public function initRuntime(Twig_Environment $environment)
	{
	}

	/**
	 * Returns the token parser instances to add to the existing list.
	 *
	 * @return Twig_TokenParserInterface[]
	 */
	public function getTokenParsers()
	{
		return array();
	}

	/**
	 * Returns the node visitor instances to add to the existing list.
	 *
	 * @return Twig_NodeVisitorInterface[] An array of Twig_NodeVisitorInterface instances
	 */
	public function getNodeVisitors()
	{
		return array();
	}

	/**
	 * Returns a list of filters to add to the existing list.
	 *
	 * @return Twig_SimpleFilter[]
	 */
	public function getFilters()
	{
		return array();
	}

	/**
	 * Returns a list of tests to add to the existing list.
	 *
	 * @return Twig_SimpleTest[]
	 */
	public function getTests()
	{
		return array();
	}

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return Twig_SimpleFunction[]
	 */
	public function getFunctions()
	{
		return array();
	}

	/**
	 * Returns a list of operators to add to the existing list.
	 *
	 * @return array An array of operators
	 */
	public function getOperators()
	{
		return array();
	}
}