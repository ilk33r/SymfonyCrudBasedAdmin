<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 18/08/15
 * Time: 21:34
 */

namespace FixVes\DashboardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AjaxDistanceTest extends WebTestCase {

	public function testGetDistanceAction() {

		$client = static::createClient();
		$client->request('POST', '/ajax/getDistance/', array(), array(), array(
			'CONTENT_TYPE'			=> 'application/json',
			'HTTP_X-Requested-With' => 'XMLHttpRequest'
		), '[415, 416]');

		$result = $client->getResponse();
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . 'AjaxControllerTest_distance_result.html', 'w');
		fwrite($handle, $result);
		fclose($handle);

		$this->assertTrue($client->getResponse()->isSuccessful());
	}

}
 