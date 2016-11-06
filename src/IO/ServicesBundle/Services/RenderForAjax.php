<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 03/11/2016
 * Time: 10:19
 */

namespace IO\ServicesBundle\Services;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RenderForAjax
 * @package IO\ServicesBundle\Services
 */
class RenderForAjax {

	/**
	 * @var \Twig_Environment
	 */
	private $twigEnv;
	/**
	 * @var bool
	 */
	private $isAjaxRequest;

	/**
	 * RenderForAjax constructor.
	 *
	 * @param \Twig_Environment $twigEnv
	 * @param Request $requestStack
	 */
	public function __construct(\Twig_Environment $twigEnv, RequestStack $requestStack) {

		$this->twigEnv = $twigEnv;
		$request = $requestStack->getCurrentRequest();
		$this->isAjaxRequest = $request->isXmlHttpRequest();
	}

	/**
	 * @param string $template
	 * @param array $params
	 *
	 * @return string
	 */
	public function renderTemplate($template, $params = array()) {

		/** @var \Twig_Template $currentTemplate */
		$currentTemplate = $this->twigEnv->loadTemplate($template);

		if ($this->isAjaxRequest) {

			$responseData = array();
			$globalParams = $this->twigEnv->mergeGlobals($params);
			foreach ($currentTemplate->getBlockNames() as $blockName) {

				$responseData[$blockName] = $currentTemplate->renderBlock($blockName, $globalParams);
			}

			return json_encode($responseData);

		}else{
			return $currentTemplate->render($params);
		}
	}
}