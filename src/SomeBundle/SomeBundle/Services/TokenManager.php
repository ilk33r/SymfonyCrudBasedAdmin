<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 31/08/15
 * Time: 16:38
 */

namespace SomeBundle\ClientApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TokenManager {

	private $symfonyToken;
	private $container;

	public function __construct(Request $request, Container $container) {

		$tokenHeader = $request->headers->get('x_SomeBundle_authentication');

		if(!is_null($tokenHeader)) {
			if(!empty($tokenHeader)) {
				$em = $container->get('doctrine');
				$userRepo = $em->getRepository('SomeBundle:User');
				$tokenData = base64_decode($tokenHeader);
				$tokenUserData = explode('-', $tokenData);
				$user = $userRepo->find($tokenUserData[0]);


				if($tokenData == $user->getAccessToken()) {

					$symfonyToken = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
					$this->symfonyToken = $symfonyToken;
					$this->container = $container;
				}
			}
		}
	}

	public function onKernelRequest($event) {

		if($this->symfonyToken) {
			$this->container->get('security.token_storage')->setToken($this->symfonyToken);
		}
	}
} 