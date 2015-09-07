<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 04/09/15
 * Time: 10:12
 */

namespace IO\ServicesBundle\Helpers;


class XorEncryptionHelper {

	public static function getEncryptedPassword($password, $salt) {
		return bin2hex(hash_pbkdf2('sha1', $password, $salt, 20000, 160, true));
	}

	public static function encryptText($text, $decryptionKey) {
		$encryptedText				= self::xorEncryption(base64_encode($text), $decryptionKey);
		$base64EncodedString		= base64_encode($encryptedText);
		return $base64EncodedString;
	}

	public static function decryptText($text, $decryptionKey) {
		$base64DecodedString		= base64_decode($text);
		$decryptedText				= self::xorEncryption($base64DecodedString, $decryptionKey);
		return base64_decode($decryptedText);
	}

	public static function xorEncryption($string, $key) {
		$outText		= '';
		$i 				= 0;

		foreach(str_split($string) as $char)
		{
			$outText	.= chr( ord($char) ^ ord( $key{$i++ % strlen($key)} ));
		}

		return $outText;
	}

}