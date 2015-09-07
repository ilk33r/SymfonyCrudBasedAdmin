<?php

namespace SomeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjaxControllerTest extends WebTestCase
{

	public function testSearchPortAction()
	{
		$client = static::createClient();

		$client->request('POST', '/ajax/searchPort/', array(), array(), array(
				'CONTENT_TYPE'          => 'application/json',
				'HTTP_X-Requested-With' => 'XMLHttpRequest')
			, '{"portName":"kie"}');

		$result = $client->getResponse();
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . 'AjaxControllerTest_result.html', 'w');
		fwrite($handle, $result);
		fclose($handle);

		$this->assertTrue($client->getResponse()->isSuccessful());
	}

}
