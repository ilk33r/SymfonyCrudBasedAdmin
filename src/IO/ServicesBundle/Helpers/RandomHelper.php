<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 00:39
 */

namespace IO\ServicesBundle\Helpers;

class RandomHelper
{
	public static function generateAlphaNumeric($characterCount)
	{
		$characters		= 'abcdefghijklmnopqrstuvwxyz0123456789';

		$result			= '';
		for ($i = 0; $i < $characterCount; $i++)
		{
			$result		.=	$characters[mt_rand(0, strlen($characters) - 1)];
		}

		return $result;
	}

	public static function generateRandomInteger($min = 0, $max = 0)
	{
		if($max == 0)
		{
			return mt_rand();
		}else{
			return mt_rand($min, $max);
		}
	}
}