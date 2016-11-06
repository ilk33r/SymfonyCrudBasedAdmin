<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

$response = $kernel->handle($request);

$response->headers->set('Pragma', 'no-cache');
$response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0');
$response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
$response->headers->set('Expires', 'Mon, 24 Oct 1988 22:30:00 GMT');

$response->headers->set('X-Frame-Options', 'SAMEORIGIN');
$response->headers->set('X-XSS-Protection', '1; mode=block');
$response->headers->set('X-Powered-By', 'ilker özcan');
$response->headers->set('X-Version', '1.0.0');

$response->send();
$kernel->terminate($request, $response);
