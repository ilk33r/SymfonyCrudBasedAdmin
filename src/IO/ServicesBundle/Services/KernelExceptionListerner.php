<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 27/10/15
 * Time: 22:33
 */

namespace SomeBundle\Services;


use AppKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Scotty\ClientApiBundle\Entity\User;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class KernelExceptionListerner {

	private $container;
	//private $logger;

	const LINE_ENDING = "\n";


	public function __construct(Container $container) {

		$this->container = $container;
		//$this->logger = $container->get('logger');
	}

	public function onKernelException(GetResponseForExceptionEvent $event) {

		$exceptionCode = $event->getException()->getCode();
		if($exceptionCode != 404) {

			$message = $event->getException()->getMessage();
			$file = $event->getException()->getFile();
			$line = $event->getException()->getLine();
			$url = $event->getRequest()->getRequestUri();
			$method = $event->getRequest()->getMethod();
			$headers = $event->getRequest()->headers;
			$host = $headers->get('host');
			$connection = $headers->get('connection');
			$userAgent = $headers->get('user_agent');
			$authorizationString = 'ANONYMOUS';
			$userId = 0;
			$userName = '';

			if($this->container->has('security.authorization_checker')) {

				try {

					$authorizationStatus = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
				}catch (AuthenticationCredentialsNotFoundException $e) {
					$authorizationStatus = false;
				}

				if($authorizationStatus) {

					$authorizationString = 'AUTHORIZED';

					if($this->container->has('security.token_storage')) {

						$user =  $this->container->get('security.token_storage')->getToken();

						if(!is_null($user)) {

							/** @var User $userData */
							$userData = $user->getUser();
							$userId = $userData->getId();
							$userName = $userData->getUsername();
						}

					}
				}

			}

			$socketPath = $this->container->getParameter('exceptionListenerSocketPath');

			try {
				$monitorServerSock = stream_socket_client('unix://' . $socketPath, $errno, $errstr, 30);
			}catch (ContextErrorException $e) {
				return;
			}

			/*$logData = "HALLO\nCODE $exceptionCode\nMESSAGE $message\nFILE $file\nLINE $line\nURL $url\nMETHOD $method\nHOST $host\nCONNECTION $connection\nUSERAGENT $userAgent\nAUTHORIZATION $authorizationString\nUSERID $userId\nUSERNAME $userName\nCREATEANALYTICS\nCLOSECONNECTION\n";
			$logger = $this->container->get('logger');
			$logger->info($logData);*/

			if ($monitorServerSock !== false) {

				$this->ParseMonitorResponses($monitorServerSock);

				try {
					fwrite($monitorServerSock, 'CODE ' . $exceptionCode . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try {
					fwrite($monitorServerSock, 'MESSAGE ' . $message . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try {
					fwrite($monitorServerSock, 'FILE ' . $file . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'LINE ' . $line . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'URL ' . $url . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'METHOD ' . $method . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'HOST ' . $host . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'CONNECTION ' . $connection . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'USERAGENT ' . $userAgent . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'AUTHORIZATION ' . $authorizationString . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'USERID ' . $userId . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'USERNAME ' . $userName . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'CREATEANALYTICS' . self::LINE_ENDING);
					$this->ParseMonitorResponses($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

				try{
					fwrite($monitorServerSock, 'CLOSECONNECTION' . self::LINE_ENDING);
					fclose($monitorServerSock);
				} catch (ContextErrorException $e) {
					fclose($monitorServerSock);
					return;
				}

			}

		}
	}

	private function ParseMonitorResponses($socketPointer) {

		$buff = fgets($socketPointer, 128);
		$response = explode(self::LINE_ENDING, $buff);

		//$this->logger->info('Socket server response is ' . $buff);

		if($response[0] == 'WILLKOMMEN') {
			fwrite($socketPointer, 'HALLO' . self::LINE_ENDING);
			$this->ParseMonitorResponses($socketPointer);
		}
	}

}