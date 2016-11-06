<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 04/12/15
 * Time: 11:17
 */

namespace FOSUserBundle\ExtendedUsersBundle\Services;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SecurityService implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

	public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

		if ($request->isXmlHttpRequest()) {
			$result = array('success' => true);
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');

			$referer = $request->headers->get('referer', '');

			$sessionRef = $request->getSession();
			if(is_null($sessionRef)) {

				$nSession = new Session();
				$nSession->start();
				$nSession->set('lastRefererPage', $referer);
			}else{
				$sessionRef->set('lastRefererPage', $referer);
			}

			return $response;
		}

		throw new BadRequestHttpException();

	}
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

		if ($request->isXmlHttpRequest()) {
			$result = array('success' => false, 'message' => $exception->getMessage());
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}

		throw new BadRequestHttpException();
	}

}