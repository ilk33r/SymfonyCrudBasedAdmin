<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 23:34
 */

namespace IO\ServicesBundle\Helpers;

use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\Filesystem\Filesystem;

class ImageManipulator
{
	private $sourcePath;
	private $sourceFileName;
	private $destinationPath;
	private $quality;
	private $aspectRatio;
	private $destinationType;

	private $sourceType;
	private $sourceWidth;
	private $sourceHeight;
	private $sourceGDImage;
	private $fileSystem;
	private $temporaryImage;
	private $currentImageWidth;
	private $currentImageHeight;

	public function __construct($sourcePath, $sourceFileName, $destinationPath = '', $quality = 60, $aspectRatio = true, $destinationType = '')
	{
		if(!extension_loaded('GD'))
		{
			throw new \Exception('GD Library Not Found File: '. __FILE__ . ' Line: ' . __LINE__, 1000);
		}

		$this->sourcePath = $sourcePath;
		$this->sourceFileName = $sourceFileName;
		$this->destinationPath = (empty($destinationPath)) ? $sourcePath : $destinationPath;
		$this->quality = $quality;
		$this->aspectRatio = $aspectRatio;
		$this->setSourceType();
		$this->fileSystem = new Filesystem();

		if(!$this->fileSystem->exists($this->sourcePath . $this->sourceFileName))
		{
			throw new \Exception('Source file not found File: '. __FILE__ . ' Line: ' . __LINE__, 1002);
		}

		$this->sourceGDImage = $this->createGDImage();
		$this->destinationType = (empty($destinationType)) ? $this->sourceType : $destinationType;
		$sourceInfo			= getimagesize($this->sourcePath . $this->sourceFileName);
		$this->sourceWidth	= $sourceInfo[0];
		$this->sourceHeight	= $sourceInfo[1];
		$this->currentImageWidth = $this->sourceWidth;
		$this->currentImageHeight = $this->sourceHeight;
	}

	private function setSourceType()
	{
		$extension = substr($this->sourceFileName, -3);
		switch($extension)
		{
			case 'jpg':
				$this->sourceType = 'image/jpeg';
				break;
			case 'png':
				$this->sourceType = 'image/png';
				break;
			case 'gif':
				$this->sourceType = 'image/gif';
				break;
			default:
				throw new \Exception('File is not image File: '. __FILE__ . ' Line: ' . __LINE__ . ' Source file name: ' . $this->sourceFileName, 1001);
				break;
		}
	}

	private function createGDImage()
	{
		switch($this->sourceType)
		{
			case 'image/jpeg':
				return imagecreatefromjpeg($this->sourcePath . $this->sourceFileName);
				break;
			case 'image/gif':
				return imagecreatefromgif($this->sourcePath . $this->sourceFileName);
				break;
			case 'image/png':
				return imagecreatefrompng($this->sourcePath . $this->sourceFileName);
				break;
			default:
				throw new \Exception('File is not image File: '. __FILE__ . ' Line: ' . __LINE__ . ' Source type: ' . $this->sourceType, 1001);
				break;
		}
	}

	public function resizeImage($newWidth, $newHeight, $heightBasedAspect = false)
	{
		if($this->aspectRatio)
		{
			if($heightBasedAspect)
			{
				$expectedHeight = $newHeight;
				$expectedWidth	= round($this->sourceWidth * $newHeight / $this->sourceHeight);
			}else{
				$expectedWidth	= $newWidth;
				$expectedHeight = round($newWidth * $this->sourceHeight / $this->sourceWidth);
			}
		}else{
			$expectedWidth	= $newWidth;
			$expectedHeight = $newHeight;
		}

		$this->temporaryImage = $this->createEmptyImage($expectedWidth, $expectedHeight);

		if($this->sourceType == 'image/png')
		{
			imagealphablending($this->sourceGDImage, TRUE);
			imagealphablending($this->temporaryImage, FALSE);
			imagesavealpha($this->temporaryImage, TRUE);
		}

		imagecopyresampled($this->temporaryImage, $this->sourceGDImage, 0, 0, 0, 0, $expectedWidth, $expectedHeight, $this->sourceWidth, $this->sourceHeight);
		$this->currentImageWidth = $expectedWidth;
		$this->currentImageHeight = $expectedHeight;
		return $this;
	}

	public function cropImage($newWidth, $newHeight)
	{
		$bgImage			= imagecreatetruecolor(1000, 1000);
		$black				= imagecolorallocate($bgImage, 0, 0, 0);
		imagefill($bgImage, 0, 0, $black);
		$bigImageDestX		= (1000 - $this->currentImageWidth) / 2;
		$bigImageDesty		= (1000 - $this->currentImageHeight) / 2;

		if($this->temporaryImage)
		{
			imagecopyresampled($bgImage, $this->temporaryImage, $bigImageDestX, $bigImageDesty, 0, 0, $this->currentImageWidth, $this->currentImageHeight, $this->currentImageWidth, $this->currentImageHeight);
		}else{
			imagecopyresampled($bgImage, $this->sourceGDImage, $bigImageDestX, $bigImageDesty, 0, 0, $this->currentImageWidth, $this->currentImageHeight, $this->currentImageWidth, $this->currentImageHeight);
		}

		$destX				= floor((1000 - $newWidth) / 2);
		$destY				= floor((1000 - $newHeight) / 2);
		$croppedImageData	= array(
								'x' => $destX ,
								'y' => $destY,
								'width' => $newWidth,
								'height'=> $newHeight
		);

		$croppedImage		= imagecrop($bgImage, $croppedImageData);

		if($this->sourceType == 'image/png')
		{
			imagealphablending($bgImage, TRUE);
			imagealphablending($croppedImage, FALSE);
			imagesavealpha($croppedImage, TRUE);
		}

		$this->temporaryImage = $croppedImage;
		return $this;
	}

	public function saveImage()
	{
		switch($this->destinationType)
		{
			case 'image/jpeg':
				$imageStatus	= imagejpeg($this->temporaryImage, $this->destinationPath . $this->sourceFileName, $this->quality);
				break;
			case 'image/gif':
				$imageStatus	= imagegif($this->temporaryImage, $this->destinationPath .$this->sourceFileName);
				break;
			case 'image/png':
				$tmpQuality		= ($this->quality > 9)?9:$this->quality;
				imagealphablending($this->temporaryImage, FALSE);
				imagesavealpha($this->temporaryImage, TRUE);
				$imageStatus	= imagepng($this->temporaryImage, $this->destinationPath .$this->sourceFileName, $tmpQuality);
				break;
		}

		if(!$imageStatus)
		{
			throw new \Exception('Image could not saved File: '. __FILE__ . ' Line: ' . __LINE__, 1003);
		}else{
			return $this;
		}
	}

	public function setDestinationPath($path)
	{
		$this->destinationPath = $path;
	}

	private function createEmptyImage($width, $height)
	{
		return imagecreatetruecolor($width, $height);
	}
}