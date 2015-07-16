<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 16:30
 */

namespace IO\ServicesBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use IO\ServicesBundle\Helpers\TextHelper;
use Symfony\Component\HttpKernel\Kernel;

class AjaxImageUploader
{
	private static $errors = array(
		'FILE_IS_NOT_DECODED_JSON',
		'FILE_IS_NOT_IMAGE',
		'UPLOAD_FOLDER_IS_NOT_EXISTS',
		'UPLOAD_FOLDER_IS_NOT_WRITABLE',
		'AN_ERROR_OCCURED_FOR_CREATING_IMAGE_FILE'
	);

	private $webRootDir = '';

	public function __construct(Kernel $kernel)
	{
		$this->webRootDir = $kernel->getRootDir() . '/../web';
	}

	public function uploadImageFile(Request $request, $path)
	{
		$requestContent = $request->getContent();
		try
		{
			$imageData = json_decode($requestContent);
		}catch (\Exception $e)
		{
			return self::$errors[0];
		}

		$extension				= 'jpg';

		switch($imageData->imageType)
		{
			case 'image/jpeg':
			case 'image/pjpeg':
				$extension		= 'jpg';
				break;
			case 'image/gif':
				$extension		= 'gif';
				break;
			case 'image/png':
				$extension		= 'png';
				break;
			default:
				return self::$errors[1];
				break;
		}

		if($imageData->imageName)
		{
			$imagePrettyName = time() . '-' . TextHelper::slugifyText( substr(strtr($imageData->imageName, array('.'=>'')), 0, -3) ) . '.' . $extension;
			$uploadFolder = $this->webRootDir . $path;

			$fs = new Filesystem();

			if(!$fs->exists($uploadFolder)) {
				return self::$errors[2];
			}

			if(!is_writable($uploadFolder))
			{
				return self::$errors[3];
			}

			$data	= explode(',', $imageData->imageData);
			try
			{
				$fs->dumpFile($uploadFolder . $imagePrettyName, base64_decode($data[1]));
			}catch (IOExceptionInterface $e)
			{
				return self::$errors[4];
			}

			return $imagePrettyName;

		}else{
			return self::$errors[1];
		}
	}
}