<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 18/07/15
 * Time: 12:51
 */

namespace IO\ServicesBundle\Services;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

class MandrillMailer implements MailerInterface
{
	private $apiUrl;
	private $mailData;

	public function __construct($apiUrl, $apiKey, $fromEmail, $fromName)
	{
		$this->apiUrl							= $apiUrl;
		$this->mailData							= new \stdClass();
		$this->mailData->key					= $apiKey;
		$this->mailData->message				= new \stdClass();
		$this->mailData->message->from_email	= $fromEmail;
		$this->mailData->message->from_name		= $fromName;
		$this->mailData->message->autotext		= 'true';
		$this->mailData->message->to			= array();
	}

	public function addToUser($toUserEmail, $toUserName = '')
	{
		$toUser							= new \stdClass();
		$toUser->email					= strtolower($toUserEmail);
		$toUser->name					= $toUserName;
		$toUser->type					= 'to';
		$this->mailData->message->to[]	= $toUser;
	}

	public function sendMail($subject, $body)
	{
		$this->mailData->message->subject		= $subject;
		$this->mailData->message->html			= $body;

		$options		= array(
			'CURLOPT_URL'				=> $this->apiUrl,
			'CURLOPT_POST'				=> true,
			'CURLOPT_POSTFIELDS'		=> json_encode($this->mailData),
			'CURLOPT_RETURNTRANSFER'	=> true,
			'CURLOPT_HEADER'			=> true,
			'CURLOPT_HTTPHEADER'		=> array('Content-Type: application/json')
		);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data		= curl_exec($ch);
		curl_close($ch);

		$this->mailData->message->to[]	= array();
		return json_decode($data);
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
			'confirmationUrl' =>  $url
		));

		$emailData	= $this->getFOSUSerSubjectAndBody($rendered);
		$this->addToUser($user->getEmail(), $user->getUsername());


		$this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], $user->getEmail());
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

	}

	private function getFOSUSerSubjectAndBody($renderedTemplate)
	{
		$response			= new \stdClass();

		echo '-- renderedTemplate Start --' . PHP_EOL;
		print_r($renderedTemplate);
		echo '-- renderedTemplate End --' . PHP_EOL;

		// Render the email, use the first line as the subject, and the rest as the body
		$renderedLines = explode("\n", trim($renderedTemplate));
		$response->subject = $renderedLines[0];
		$response->body = implode("\n", array_slice($renderedLines, 1));

		if (strlen($response->body) == 0 || strlen($response->subject) == 0) {
			throw new \RuntimeException(
				"No message was found, cannot send e-mail." .
				"error can occur when you don't have set a confirmation template or using the default " .
				"without having translations enabled."
			);
		}

		return $response;
	}
}