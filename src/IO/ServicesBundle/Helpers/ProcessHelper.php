<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 17/08/15
 * Time: 16:28
 */

namespace IO\ServicesBundle\Helpers;

class ProcessHelper
{

	public static function execInBackground($kernelRoot, $command) {
		shell_exec($kernelRoot . '/console' . ' ' . $command . ' >/dev/null 2>/dev/null &');
	}

	public static function startWithDelay($kernelRoot, $command, $delay) {
		passthru('sleep ' . $delay . ' && php ' . $kernelRoot . '/console' . ' ' . $command . ' >/dev/null 2>/dev/null &');
	}

}