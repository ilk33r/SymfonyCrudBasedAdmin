<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 05/10/15
 * Time: 14:40
 */

namespace SomeBundle\UserOnline;

define('LINE_ENDING', "\r\n");

use Symfony\Component\Debug\Exception\ContextErrorException;

/**
 * Class MemcacheServerHelper
 * @package SomeBundle\UserOnline
 */
class MemcacheServerHelper {

	/**
	 * @var string
	 */
	private static $serverCommand = '%s/memcache %s -p %s -s %s';

	/**
	 * @param $kernelRoot
	 * @param $pidFile
	 * @return int
	 */
	public static function GetServerStatus($kernelRoot, $pidFile) {

		$command = vsprintf(self::$serverCommand, array(
			$kernelRoot,
			'status',
			$pidFile,
			'-',
			));
		$processHandle = popen($command, 'r');
		$serverStatusString = fread($processHandle, 64);
		pclose($processHandle);

		//$serverStatusCode = explode(LINE_ENDING, $serverStatusString);
		$statusCode = (int)$serverStatusString;

		/* expect 200 */
		return $statusCode;
	}

	/**
	 * @param $kernelRoot
	 * @param $pidFile
	 * @return int
	 */
	public static function StopServer($kernelRoot, $pidFile) {

		$command = vsprintf(self::$serverCommand, array(
			$kernelRoot, 'stop', $pidFile, '-'));
		$processHandle = popen($command, 'r');
		$serverStatusString = fread($processHandle, 64);
		pclose($processHandle);

		$serverStatusCode = explode(PHP_EOL, $serverStatusString);
		$statusCode = (int)$serverStatusCode[0];

		/* expect 200 or 102*/
		return $statusCode;
	}

	/**
	 * @param $kernelRoot
	 * @param $pidFile
	 * @param $socketPath
	 * @param $logPath
	 * @param $databasePath
	 * @param $videoFolder
	 * @return int
	 */
	public static function StartServer($kernelRoot, $pidFile, $socketPath) {

		$command = vsprintf(self::$serverCommand, array($kernelRoot, 'start',
			$pidFile, $socketPath));

		//shell_exec($command . ' >/dev/null 2>/dev/null &');
		$processHandle = popen($command . ' >/dev/null 2>/dev/null &', 'r');
		$serverStatusString = fread($processHandle, 64);
		pclose($processHandle);

		//$serverStatusCode = explode(LINE_ENDING, $serverStatusString);
		//$statusCode = (int)$serverStatusCode[0];

		sleep(2);


		/* expect 200*/
		return self::GetServerStatus($kernelRoot, $pidFile);
	}

	/**
	 * @param string $socketPath
	 * @param int $timeout
	 * @return bool|resource
	 */
	public static function ConnectMemcacheServer($socketPath, $timeout = 2) {

		try {
			$memcacheServer = stream_socket_client('unix://' . $socketPath, $errno, $errstr, $timeout);
		}catch (ContextErrorException $e) {
			return false;
		}

		if (!$memcacheServer) {
			return false;
		} else {
			self::GetMemcacheResponses($memcacheServer);
			self::WriteMemcacheCommand($memcacheServer, 'HALLO');
			$response = self::GetMemcacheResponses($memcacheServer);
			if(self::CheckServerResponse($response)) {
				return $memcacheServer;
			}else{
				fclose($memcacheServer);
				return false;
			}
		}
	}

	/**
	 * @param resource $socketPointer
	 * @param string $commandName
	 */
	private static function WriteMemcacheCommand($socketPointer, $commandName) {

		fwrite($socketPointer, $commandName . LINE_ENDING);
	}

	/**
	 * @param resource $socketPointer
	 */
	private static function GetMemcacheResponses($socketPointer) {

		$buff = fgets($socketPointer);
		return $buff;
	}

	/**
	 * @param string $response
	 * @return bool
	 */
	public static function CheckServerResponse($response) {
		$responseBag = explode(LINE_ENDING, $response);

		if($responseBag[0] == 'OK') {
			return true;
		}elseif($responseBag[0] == 'FAIL') {
			return false;
		}else{
			return false;
		}
	}

	/**
	 * @param resource $socketPointer
	 * @param string $id
	 * @return string
	 */
	public static function FetchData($socketPointer, $id) {

		self::WriteMemcacheCommand($socketPointer, 'FETCH ' . $id);
		return self::GetMemcacheResponses($socketPointer);
	}

	/**
	 * @param resource $socketPointer
	 * @param string $id
	 * @return string
	 */
	public static function ContainsData($socketPointer, $id) {

		self::WriteMemcacheCommand($socketPointer, 'CONTAINS ' . $id);
		return self::GetMemcacheResponses($socketPointer);
	}

	/**
	 * @param resource $socketPointer
	 * @param string $id
	 * @return string
	 */
	public static function DeleteData($socketPointer, $id) {

		self::WriteMemcacheCommand($socketPointer, 'DELETE ' . $id);
		return self::GetMemcacheResponses($socketPointer);
	}

	/**
	 * @param resource $socketPointer
	 * @return string
	 */
	public static function FlushData($socketPointer) {

		self::WriteMemcacheCommand($socketPointer, 'FLUSH');
		return self::GetMemcacheResponses($socketPointer);
	}

	/**
	 * @param resource $socketPointer
	 * @return string
	 */
	public static function GetStats($socketPointer) {

		self::WriteMemcacheCommand($socketPointer, 'GETSTATS');
		return self::GetMemcacheResponses($socketPointer);
	}

	/**
	 * @param resource $socketPointer
	 * @return string
	 */
	public static function CloseConnection($socketPointer) {

		self::WriteMemcacheCommand($socketPointer, 'CLOSE');
		fclose($socketPointer);
	}

	/**
	 * @param resource $socketPointer
	 * @param string $id
	 * @param string $data
	 * @param int $lifetime seconds
	 * @return bool
	 */
	public static function AddData($socketPointer, $id, $data, $lifetime = 0) {

		self::WriteMemcacheCommand($socketPointer, 'DATA ' . $data);
		$response = self::GetMemcacheResponses($socketPointer);
		$success = self::CheckServerResponse($response);

		if($success) {
			self::WriteMemcacheCommand($socketPointer, 'LIFETIME ' . $lifetime);
			$response = self::GetMemcacheResponses($socketPointer);
			$success = self::CheckServerResponse($response);
		}

		if($success) {
			self::WriteMemcacheCommand($socketPointer, 'SAVE ' . $id);
			$response = self::GetMemcacheResponses($socketPointer);
			$success = self::CheckServerResponse($response);
		}

		return $success;
	}
}