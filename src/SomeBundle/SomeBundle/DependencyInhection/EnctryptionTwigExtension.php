<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 04/09/15
 * Time: 11:19
 */

namespace SomeBundle\DependencyInjection;

use \Twig_Extension as BaseTwigExtension;
use \Twig_SimpleFilter;
use IO\ServicesBundle\Helpers\XorEncryptionHelper;


class EnctryptionTwigExtension extends BaseTwigExtension {

	protected $stringEncryptionKey;

	public function __construct($stringEncryptionKey) {

		$this->stringEncryptionKey = $stringEncryptionKey;
	}

	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('encryptString', array($this, 'encryptString')),
			new Twig_SimpleFilter('decryptString', array($this, 'decryptString')),
			new Twig_SimpleFilter('updateAdvertisingFormImageValue', array($this, 'updateAdvertisingFormImageValue')),
		);
	}

	public function encryptString($decryptedString) {

		return XorEncryptionHelper::encryptText($decryptedString, $this->stringEncryptionKey);
	}

	public function decryptString($encryptedString) {

		return XorEncryptionHelper::decryptText($encryptedString, $this->stringEncryptionKey);
	}

	public function updateAdvertisingFormImageValue($valueString) {

		$splittedString = explode('/web/adContent/', $valueString);
		return $splittedString[1];
	}

	public function getName()
	{
		return 'some_bundle_twig_extension';
	}
} 