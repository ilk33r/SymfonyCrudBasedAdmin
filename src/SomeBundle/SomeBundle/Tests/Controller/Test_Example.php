<?php

namespace SomeBundleTests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SomeControllerTest extends WebTestCase
{

	public function testExample() {

		$client = static::createClient(array('debug' => true));

		$client->request('POST', '/webservices/', array(), array(),
			array(
				'CONTENT_TYPE' => 'application/json',
			)
			,
'{"key": "val"}'
		);

		$result = $client->getResponse();

		$url = 'http://localhost:8000/_profiler/' . $result->headers->get('X-Debug-Token');
		echo $url;

		if($result->isSuccessful()) {
			echo $result;
		}

		$this->assertTrue($result->isSuccessful());
	}



}
