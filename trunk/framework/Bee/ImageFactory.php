<?php
/*
 * Copyright 2008-2010 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @deprecated
 */
class Bee_ImageFactory {
	const IMG_GIF = 1;
	const IMG_JPG = 2;
	const IMG_PNG = 3;
	
	const POSITION_TOP = 1;
	const POSITION_CENTER = 2;
	const POSITION_BOTTOM = 3;
	const POSITION_LEFT = 4;
	const POSITION_RIGHT = 5;

	const CACHE_DIRECTORY_PREFIX = ".cache";
	
	private static $mimeType;
	private static $header;
	
	private static $sourcePath;
	private static $sourceFilename;
	private static $watermarkFilename;
	private static $cacheFilename;
	
	private static $width;
	private static $height;
	
	private static $stretch = false;
	
	
	private static function getSourcePathAndFilename() {
		return self::$sourcePath.'/'.self::$sourceFilename;
	}
	
	/**
	 * Returns MIME Type of the file
	 */
	private static function setMimeType() {
		$fileTag = strtolower(pathinfo(self::getSourcePathAndFilename(), PATHINFO_EXTENSION));
		switch ($fileTag) {
			case "gif":
				self::$mimeType = IMG_GIF;
//				self::$header = "Content-Type: image/gif";
				self::$header = "Content-Type: image/png";
				break;
			case "jpg":
			case "jpeg":
//				self::$header = "Content-Type: image/jpeg";
				self::$header = "Content-Type: image/png";
				self::$mimeType = IMG_JPG;
				break;
			case "png":
				self::$header = "Content-Type: image/png";
				self::$mimeType = IMG_PNG;
				break;
			default:
				self::$mimeType = 0;
		}
	}
	
	
	
	private static function isCachedImageOutdated() {
//		return true;
		if (filemtime(self::$cacheFilename) < filemtime(self::getSourcePathAndFilename())) {
			return true;
		}
        return false;
	}
	
	
	
	private static function buildCacheFilename() {
		// build cache-filename
		self::$cacheFilename = self::$sourcePath.'/'.self::CACHE_DIRECTORY_PREFIX.'/'.self::$sourceFilename;
		// add dimensions to cacheFilename
		self::$cacheFilename .= "_d";
		if (self::$width!==false) {
			self::$cacheFilename .= self::$width; 
		}

		if (self::$height!==false) {
			self::$cacheFilename .= "x".self::$height; 
		}
        if (self::$stretch) {
            self::$cacheFilename .= '_stretched';
        }
	}
	
	
	
	/**
	 * Sends resized, watermarked and labeled image to browser.
	 * 
	 * path:		path to the image without ending /
	 * filename:	filename of the image
	 * 
	 * Size parameters define the maximum dimensions of the image.
	 * If only width or height is set, the other one will be calculated.
	 * If no size parameter is set, the original size will be used.
	 */
	public static final function createImage($path, $filename, $width=false, $height=false, $watermarkFile=false, $label=false, $stretch=false) {
		ini_set('memory_limit', '-1');

		// build full filename
		self::$sourcePath = $path;
		self::$sourceFilename = $filename;
		
		self::$width = $width;
		self::$height = $height;
        self::$stretch = $stretch;

		
		// create directory for caches
		if (!file_exists($path."/".self::CACHE_DIRECTORY_PREFIX)) {
			umask(0000);
			mkdir($path."/".self::CACHE_DIRECTORY_PREFIX);
		}
		
		self::buildCacheFilename();
		self::setMimeType();

		if (file_exists(self::getSourcePathAndFilename())) {
			
			if (!$width && !$height && !$watermarkFile && !$label) {
				// do nothing here!
			}
			// create cached file if it does not exist or older than orignial file
			else if (!file_exists(self::$cacheFilename) || self::isCachedImageOutdated()) {
				switch (self::$mimeType) {
					case IMG_GIF:
						$image = self::buildImage(imagecreatefromgif(self::getSourcePathAndFilename()));
						//if (!empty($label)) $image = $this->labelImage($image);
						imagecolortransparent($image, 0);
						imagegif($image, self::$cacheFilename);
//						imagepng($image, self::$cacheFilename);
						break;
					case IMG_JPG:
						$image = self::buildImage(imagecreatefromjpeg(self::getSourcePathAndFilename()));
						//if (!empty($label)) $image = $this->labelImage($image);
						imagejpeg($image, self::$cacheFilename);
//						imagepng($image, self::$cacheFilename);
						break;
					case IMG_PNG:
						$image = self::buildImage(imagecreatefrompng(self::getSourcePathAndFilename()));
						//if (!empty($label)) $image = $this->labelImage($image);
						imagecolortransparent($image, 0);
						imagepng($image, self::$cacheFilename);
						break;
					default:
						// filetype is not supported so I suggest returning a blank image
						self::buildBlankImage();
						exit();
				}
				imagedestroy($image);
			}
			// passthru cached file
			$file = fopen(self::$cacheFilename, 'rb');
			header(self::$header);
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header('Pragma: no-cache' );
			header("Expires: 1");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
			header("Content-Length: ".filesize(self::$cacheFilename));
			fpassthru($file);
			exit();	
		} else {
			// file does not exist
			// I suggest returning a blank image
			self::buildBlankImage();
		}
	}
	
	
	private static function buildImage($srcImage) {
		$srcWidth = imagesx($srcImage);
		$srcHeight = imagesy($srcImage);
		// calculate picture dimensions
		$doResize = true;
		if (!self::$stretch) {
			$doResize = self::$width<$srcWidth||self::$height<$srcHeight;
		}
		if ($doResize) {
			if (self::$width && self::$height) {
				$ratioX = self::$width / $srcWidth;
				$ratioY = self::$height / $srcHeight;
				if ($ratioX <= $ratioY) { $ratio = $ratioX; } else { $ratio = $ratioY; }
				self::$height = $ratio * $srcHeight;
				self::$width = $ratio * $srcWidth;
			} else if (self::$width && !self::$height) {
				$ratio = self::$width / $srcWidth;
				self::$height = $ratio * $srcHeight;
			} else if (self::$height && !self::$width) {
				$ratio = self::$height / $srcHeight;
				self::$width = $ratio * $srcWidth;
			} else if (!self::$width && !self::$height) {
				self::$width = $srcWidth;
				self::$height = $srcHeight;
			}
		} else {
			self::$width = $srcWidth;
			self::$height = $srcHeight;
		}
		$image = imagecreatetruecolor(self::$width, self::$height);
		if (self::$width!=$srcWidth||self::$height!=$srcHeight) {
			imagecopyresampled($image, $srcImage, 0, 0, 0, 0, self::$width, self::$height, $srcWidth, $srcHeight);
		} else {
			$image = $srcImage;
		}
		// if ($watermark) $image = $this->addWatermark($image, self::$width, self::$height);
		return $image;
	}	
	
	
	private static function buildBlankImage() {
		$srcImage = imagecreatetruecolor(self::$width, self::$height);
		$image = self::buildImage($srcImage);
		imagecolortransparent($image, 0);
		header("Content-Type: image/png");
		imagepng($image);
		imagedestroy($image);		
	}
}
?>
