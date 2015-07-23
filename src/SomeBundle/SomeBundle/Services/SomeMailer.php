<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 18/07/15
 * Time: 12:51
 */

namespace Ecommerce\StandartBundle\Services;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class EcommerceMailer implements MailerInterface
{
	private $container;
	private $router;
	private $templating;
	private $parameters;

	public function __construct(Container $container, RouterInterface $router, EngineInterface $templating, array $parameters)
	{
		$this->container				= $container;
		$this->router					= $router;
		$this->templating				= $templating;
		$this->parameters				= $parameters;
	}

	/**
	 * Send an email to a user to confirm the account creation
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendConfirmationEmailMessage(UserInterface $user)
	{
		$template = $this->parameters['confirmation.template'];
		$url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);
		$rendered = $this->templating->render($template, array(
			'user' => $user,
			'siteName' => $this->container->getParameter('siteName'),
			'confirmationUrl' =>  $url
		));

		$emailData	= $this->getFosUserEmailData($rendered);
		$this->sendEmail($emailData->subject, $emailData->bodyText, $emailData->bodyHtml, $user->getEmail(), $user->getRealName() . ' ' . $user->getRealSurname());
	}

	/**
	 * Send an email to a user to confirm the password reset
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendResettingEmailMessage(UserInterface $user)
	{
		$template = $this->parameters['resetting_password.template'];
		$url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);
		$rendered = $this->templating->render($template, array(
			'user' => $user,
			'siteName' => $this->container->getParameter('siteName'),
			'confirmationUrl' => $url
		));

		$emailData	= $this->getFosUserEmailData($rendered);
		$this->sendEmail($emailData->subject, $emailData->bodyText, $emailData->bodyHtml, $user->getEmail(), $user->getRealName() . ' ' . $user->getRealSurname());
	}

	private function getFosUserEmailData($renderedTemplate)
	{
		$response			= new \stdClass();
		$currentKey			= "";
		$currentValue		= "";
		$parsingValue		= false;
		$valueStart			= false;
		$escapeStart		= false;

		for($i = 0; $i < strlen($renderedTemplate); $i++)
		{
			$character	= substr($renderedTemplate, $i, 1);

			if($parsingValue)
			{
				if($valueStart)
				{
					if(!$escapeStart && $character == '"')
					{
						$valueStart	= false;
						$parsingValue = false;

						$response->$currentKey = $currentValue;
						$currentKey = "";
						$currentValue = "";
					}elseif($escapeStart)
					{
						$currentValue .= $character;
						$escapeStart = false;
					}else{
						if($character == '\\')
						{
							$escapeStart = true;
						}else{
							$currentValue .= $character;
						}
					}
				}else{
					$trimmedCharacter = trim($character);
					if($trimmedCharacter == '"')
					{
						$valueStart = true;
					}
				}
			}else{
				$trimmedCharacter = trim($character);
				if($trimmedCharacter == '=')
				{
					$parsingValue = true;
				}else{
					$currentKey .= $trimmedCharacter;
				}
			}
		}

		if (strlen($response->bodyText) == 0 || strlen($response->subject) == 0) {
			throw new \RuntimeException(
				"error can occur when you don't have set a confirmation template or using the default ".
				"without having translations enabled."
			);
		}

		return $response;
	}

	public function sendEmail($subject, $bodyText, $bodyHtml, $to, $toName = null)
	{
		$emailFrom = $this->container->getParameter('mailerFrom');
		$emailFromName =  $this->container->getParameter('siteName');

		$email		= \Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom($emailFrom)
				->setTo($to, $toName)
				->setPriority(1)
				->setSender($emailFrom, $emailFromName)
				->addPart($bodyText, 'text/plain')
				->addPart($bodyHtml, 'text/html')
		;
		$this->container->get('mailer')->send($email);
	}
}