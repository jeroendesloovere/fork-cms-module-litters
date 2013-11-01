<?php

/**
 *
 * @author Yohann Bianchi <nstCactus@gmail.com>
 */
class FrontendLittersHelper{
	/**
	 * Modifier used to generate thumnails just in time (on first use)
	 *
	 * @param string	$pictureUrl	the URL of the picture
	 * @param int		$width		the desired width (null means auto)
	 * @param int		$height		the desired height (null means auto)
	 * @param string	$method		the method used to generate thumbnails (anything else than 'crop' means resize)
	 *
	 * @return mixed
	 */
	public static function createImage($pictureUrl, $width, $height, $method = 'crop'){
		$original 	= PATH_WWW . $pictureUrl;// $pictureUrl = '/frontend/files/litters/original/litters/6/1.jpg'
		$image		= str_replace("/frontend/files/litters/original/", "/frontend/files/litters/${width}x${height}_${method}/", $pictureUrl);

		if(!SpoonFile::exists(PATH_WWW . '/' . $image) && SpoonFile::exists($original)){
			$forceOriginalAspectRatio = $method == 'crop' ? false : true;
			$allowEnlargement = true;

			$thumb = new SpoonThumbnail($original, $width, $height);
			$thumb->setAllowEnlargement($allowEnlargement);
			$thumb->setForceOriginalAspectRatio($forceOriginalAspectRatio);
			$thumb->parseToFile(PATH_WWW . '/' . $image, 100);
		}
		return $image;
	}
}