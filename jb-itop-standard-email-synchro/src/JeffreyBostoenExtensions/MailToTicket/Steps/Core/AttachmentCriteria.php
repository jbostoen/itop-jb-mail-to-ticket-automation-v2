<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260421
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\{
	Base,
	PolicyBehavior
};

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};

// iTop internals.
use SetupUtils;


/**
 * Class AttachmentCriteria. A step which validates if attachments meet certain criteria.
 * 
 * @details The default implementation includes:
 * - Image dimensions: too small images (which are likely elements of an email signature) get ignored.
 * - Image dimensions: too large images get resized, if possible.
 * - Files of a certain MIME type, will get ignored.
 */
abstract class AttachmentCriteria extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'step_attachment_criteria';
	
	/**
	 * @inheritDoc
	 *
	 * @details
	 * Removes image attachments which are too small and also resizes images which are too large using php-gd
	 */
	public static function Execute() : void {
		
		// Checking if an undesired title pattern is found
		
			$oEmail = ProcessingHelper::GetMail();
			
			// Ignore attachment or downsize?
			$iMinWidth = static::GetStepSetting('image_min_width');
			$iMaxWidth = static::GetStepSetting('image_max_width');
			$iMinHeight = static::GetStepSetting('image_min_height');
			$iMaxHeight = static::GetStepSetting('image_max_height');
			
			static::Trace(".. Min/max dimensions: {$iMinWidth}x{$iMinHeight} / {$iMaxWidth}x{$iMaxHeight}");

			// Determine if the conditions must be checked.
			$bDoCheckIfImageDimensionTooSmall = true;
			$bDoCheckIfImageDimensionTooLarge = true;
			
			// Remove images which are too small.
			if($iMinWidth < 1 || $iMinWidth < 1) {
				static::Trace(".. Min dimensions can not be negative and should be at least 1x1 px.");
				$bDoCheckIfImageDimensionTooSmall = false;
			}
			
			if($iMaxWidth < 0 || $iMaxHeight < 0) {
				static::Trace(".. Max dimensions can not be negative.");
				$bDoCheckIfImageDimensionTooLarge = false;
			}
			
			if(function_exists('imagecopyresampled') == false) {
				static::Trace(".. php-gd seems to be missing. Resizing is not possible.");
				$bDoCheckIfImageDimensionTooLarge = false;
			}


			$sMimeTypes = static::GetStepSetting('exclude_mimetypes');
			$aMimeTypes = preg_split(static::NEWLINE_REGEX, $sMimeTypes);

			static::Trace('.. Excluded MIME types: '.implode(', ', $aMimeTypes));

			foreach($oEmail->aAttachments as $sAttachmentRef => &$aAttachment) {
				
				static::Trace('... Processing attachment ref '.$sAttachmentRef.' / MIME type '.$aAttachment['mimeType']);
							
				if(static::IsImage($aAttachment['mimeType']) == true) {
					
					$aImgInfo = static::GetImageSize($aAttachment['content']);
					if($aImgInfo !== false) {
						
						$iWidth = $aImgInfo[0];
						$iHeight = $aImgInfo[1];
						
						// Image too small?
						if($bDoCheckIfImageDimensionTooSmall == true && ($iWidth < $iMinWidth || $iHeight < $iMinHeight)) {
							
							// Unset
							static::Trace("... Image too small; unsetting {$sAttachmentRef}");
							unset($oEmail->aAttachments[$sAttachmentRef]);
							continue;
							
						}
						else {
							static::Trace("... Image not too small.");
						}
						
						// Image too large?
						if($bDoCheckIfImageDimensionTooLarge == true && ($iWidth > $iMaxWidth || $iHeight > $iMaxHeight)) {
							
							// Resize
							static::Trace("... Image too large; resizing {$sAttachmentRef}");
							$aAttachment = static::ResizeImageToFit($aAttachment, $iWidth, $iHeight, $iMaxWidth, $iMaxHeight);
							
						}
						else {
							static::Trace("... Image not too large.");
						}
					
					}
					else {
						static::Trace("... Could not determine dimensions of {$aAttachment['filename']}");
					}
					
				}
				else {
					static::Trace("... Attachment {$aAttachment['filename']} is not an image.");
				}
				
				
				// Ignore certain MIME types (This could include images).
				if(in_array($aAttachment['mimeType'], $aMimeTypes) == true) {
					
					static::Trace('... Ignore this attachment (excluded MIME type).');
					
					// Removing attachment
					unset($oEmail->aAttachments[$sAttachmentRef]);
								
				}
				
			}
		
	}
	
	/**
	 * Checks whether a MimeType is an image which can be processed by iTop (PHP GD)
	 *
	 * @param String $sMimeType
	 *
	 * @return Boolean
	 */
	public static function IsImage($sMimeType) {
				
		if(function_exists('gd_info') == false) {
			return false; // no image processing capability on this system
		}
		
		$bRet = false;
		$aInfo = gd_info(); // What are the capabilities
		switch($sMimeType)
		{
			case 'image/gif':
				return $aInfo['GIF Read Support'];
				break;
			
			case 'image/jpeg':
				return $aInfo['JPEG Support'];
				break;
			
			case 'image/png':
				return $aInfo['PNG Support'];
				break;

		}
		
		return $bRet;
	}
	
	/*
	 * Resize an image attachment so that it fits in the given dimensions.
	 *
	 * @param Array $aAttachment The original image stored as an attached array (content / mimetype / filename)
	 * @param Int $iWidth image's original width
	 * @param Int $iHeight image's original height
	 * @param Int $iMaxImageWidth Maximum width for the resized image
	 * @param Int $iMaxImageHeight Maximum height for the resized image
	 *
	 * @return Array The modified attachment array with the resized image in the 'content'
	 */
	public static function ResizeImageToFit($aAttachment, $iWidth, $iHeight, $iMaxImageWidth, $iMaxImageHeight)
	{
		$img = false;
		switch($aAttachment['mimeType']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$img = @imagecreatefromstring($aAttachment['content']);
				break;
			
			default:
				// Unsupported image type, return the image as-is
				static::Trace("... Warning: unsupported image type: '{$aAttachment['mimeType']}'. Cannot resize the image, original image will be used.");
				return $aAttachment;
		}
		if ($img === false) {
			static::Trace("... Warning: corrupted image: '{$aAttachment['filename']} / {$aAttachment['mimeType']}'. Cannot resize the image, original image will be used.");
			return $aAttachment;
		}
		else {
			// Let's scale the image, preserving the transparency for GIFs and PNGs
			$fScale = min($iMaxImageWidth / $iWidth, $iMaxImageHeight / $iHeight);

			$iNewWidth = round($iWidth * $fScale);
			$iNewHeight = round($iHeight * $fScale);
			
			static::Trace("... Resizing image from ($iWidth x $iHeight) to ($iNewWidth x $iNewHeight) px");
			$new = imagecreatetruecolor($iNewWidth, $iNewHeight);
			
			// Preserve transparency
			if(($aAttachment['mimeType'] == 'image/gif') || ($aAttachment['mimeType'] == 'image/png'))
			{
				imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
				imagealphablending($new, false);
				imagesavealpha($new, true);
			}
			
			imagecopyresampled($new, $img, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);
			
			ob_start();
			switch ($aAttachment['mimeType']) {
				case 'image/gif':
					imagegif($new); // send image to output buffer
					break;
				
				case 'image/jpeg':
					imagejpeg($new, null, 80); // null = send image to output buffer, 80 = good quality
					break;
				 
				case 'image/png':
					imagepng($new, null, 5); // null = send image to output buffer, 5 = medium compression
					break;
			}
			$aAttachment['content'] = ob_get_contents();
			@ob_end_clean();
			
			imagedestroy($img);
			imagedestroy($new);
			
			static::Trace("... Resized image is ".strlen($aAttachment['content'])." bytes long.");
				
			return $aAttachment;
		}
				
	}
		
	
	/*
	 * Resize an image attachment so that it fits in the given dimensions.
	 *
	 * @param String $sImageData Image data
	 *
	 * @return Array Array with image dimensions
	 */
	public static function GetImageSize($sImageData) {
		
		if(function_exists('getimagesizefromstring') == true ) {
			// PHP 5.4.0 or higher
			$aRet = @getimagesizefromstring($sImageData);
		}
		elseif(ini_get('allow_url_fopen')) {
			// work around to avoid creating a tmp file
			$sUri = 'data://application/octet-stream;base64,'.base64_encode($sImageData);
			$aRet = @getimagesize($sUri);
		}
		else {
			// Need to create a tmp file
			$sTempFile = tempnam(SetupUtils::GetTmpDir(), 'img-');
			@file_put_contents($sTempFile, $sImageData);
			$aRet = @getimagesize($sTempFile);
			@unlink($sTempFile);
		}
		return $aRet;
		
	}
	
	
}
