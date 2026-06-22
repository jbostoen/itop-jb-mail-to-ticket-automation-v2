<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
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
	public static int $iPrecedence = 20;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'step_attachment_criteria';
	
	/**
	 * @inheritDoc
	 *
	 * @details
	 * Removes image attachments which are too small and also resizes images which are too large using php-gd
	 */
	public static function Execute() : void {
		
		// Checking if an attachment meets the criteria.
		
			$oEmail = ProcessingHelper::GetMail();
			
			// Ignore attachment or downsize?
			$iMinWidth = static::GetStepSetting('image_min_width');
			$iMaxWidth = static::GetStepSetting('image_max_width');
			$iMinHeight = static::GetStepSetting('image_min_height');
			$iMaxHeight = static::GetStepSetting('image_max_height');
			
			static::Trace(". Min/max dimensions: {$iMinWidth}x{$iMinHeight} / {$iMaxWidth}x{$iMaxHeight}");

			// Determine if the conditions must be checked.
			$bDoCheckIfImageDimensionTooSmall = true;
			$bDoCheckIfImageDimensionTooLarge = true;
			
			// Remove images which are too small.
			if($iMinWidth < 1 || $iMinWidth < 1) {
				static::Trace('Min dimensions can not be negative and should be at least 1x1 px.');
				$bDoCheckIfImageDimensionTooSmall = false;
			}
			
			if($iMaxWidth < 0 || $iMaxHeight < 0) {
				static::Trace('Max dimensions can not be negative.');
				$bDoCheckIfImageDimensionTooLarge = false;
			}
			
			if(function_exists('imagecopyresampled') == false) {
				static::Trace('php-gd seems to be missing. Resizing is not possible.');
				$bDoCheckIfImageDimensionTooLarge = false;
			}


			$sMimeTypes = static::GetStepSetting('exclude_mimetypes');
			$aMimeTypes = preg_split(static::NEWLINE_REGEX, $sMimeTypes);

			static::Trace('Excluded MIME types: %1$s', implode(', ', $aMimeTypes));

			foreach($oEmail->aAttachments as $sAttachmentRef => &$aAttachment) {
				
				static::Trace('Processing attachment ref "%1$s" (MIME type = "%2$s"), file name "%3$s"',
					$sAttachmentRef,
					$aAttachment['mimeType'],
					$aAttachment['filename']
				);
						
				// - Ignore certain MIME types (This could include images).
					
					if(in_array($aAttachment['mimeType'], $aMimeTypes)) {
						
						static::Trace('Ignore this attachment (excluded MIME type).');
						
						// Removing attachment
						unset($oEmail->aAttachments[$sAttachmentRef]);
									
					}


				// - Resize images if needed.
				//   Ignore images if they're too small.

					if(CreateOrUpdateTicket::IsImage($aAttachment['mimeType'])) {
						
						$aImgInfo = static::GetImageSize($aAttachment['content']);
						if($aImgInfo !== false) {
							
							$iWidth = $aImgInfo[0];
							$iHeight = $aImgInfo[1];
							
							// Image too small?
							if($bDoCheckIfImageDimensionTooSmall && ($iWidth < $iMinWidth || $iHeight < $iMinHeight)) {
								
								// - Unset the attachment, ignore the image.
									static::Trace('Image too small, ignore.');
									unset($oEmail->aAttachments[$sAttachmentRef]);
									continue;
									
							}
							
							// Image too large?
							if($bDoCheckIfImageDimensionTooLarge && ($iWidth > $iMaxWidth || $iHeight > $iMaxHeight)) {
								
								// Resize
								static::Trace('Resize image (dimensions too large).');
								$aAttachment = static::ResizeImageToFit($aAttachment, $iWidth, $iHeight, $iMaxWidth, $iMaxHeight);
								
							}
						
						}
						else {
							static::Trace('Could not determine image dimensions.');
						}
						
					}
					
				

				// - Check image size (technical limit).
				//   Do this after resizing, to avoid hitting technical limits with large images which could be resized to a smaller size.

					$iMaxSize = ProcessingHelper::GetMaxAllowedPacketSize() - 500; // "Leaving some room for other data in the query".
					$iSize = strlen($aAttachment['content']);
				
					if($iSize > $iMaxSize) {
						
						// - Unset the attachment, ignore the image.
						static::Trace('File size too large (max allowed packet = %1$s).', $iMaxSize);
						unset($oEmail->aAttachments[$sAttachmentRef]);
						continue;
						
					}

					// @todo Possible new features: manually setting a file size limit too; and/or saving to a directory.

				
			}
		
	}
	

	
	/*
	 * Resize an image attachment so that it fits in the given dimensions.
	 *
	 * @param array $aAttachment The original image stored as an attached array (content / mimetype / filename).
	 * @param int $iWidth image's original width.
	 * @param int $iHeight image's original height.
	 * @param int $iMaxImageWidth Maximum width for the resized image.
	 * @param int $iMaxImageHeight Maximum height for the resized image.
	 *
	 * @return array The modified attachment array with the resized image in the 'content'
	 */
	public static function ResizeImageToFit(array $aAttachment, int $iWidth, int $iHeight, int $iMaxImageWidth, int $iMaxImageHeight) : array {

		$img = false;
		switch($aAttachment['mimeType']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$img = @imagecreatefromstring($aAttachment['content']);
				break;
			
			default:
				// Unsupported image type, return the image as-is.
				// This should actually not be happening.
				static::Trace('Warning: Cannot resize the image, original image will be used. Type "%1$s" is not supported.', $aAttachment['mimeType']);
				return $aAttachment;
		}

		if($img === false) {
			static::Trace('Warning: corrupted image. Cannot resize the image, original image will be used.');
			return $aAttachment;
		}
		else {

			// Scale the image, preserving the transparency for GIFs and PNGs.
			$fScale = min($iMaxImageWidth / $iWidth, $iMaxImageHeight / $iHeight);

			$iNewWidth = round($iWidth * $fScale);
			$iNewHeight = round($iHeight * $fScale);
			
			static::Trace('Resizing image from %1$s x %2$s to %3$s x %4$s .', $iWidth, $iHeight, $iNewHeight, $iNewHeight);
			$new = imagecreatetruecolor($iNewWidth, $iNewHeight);
			
			// Preserve transparency.
			if(($aAttachment['mimeType'] === 'image/gif') || ($aAttachment['mimeType'] === 'image/png')) {
				imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
				imagealphablending($new, false);
				imagesavealpha($new, true);
			}
			
			imagecopyresampled($new, $img, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);
			
			ob_start();
			match($aAttachment['mimeType']) {
				'image/gif' => imagegif($new), // send image to output buffer
				'image/jpeg' => imagejpeg($new, null, 80), // null = send image to output buffer, 80 = good quality
				'image/png' => imagepng($new, null, 5), // null = send image to output buffer, 5 = medium compression
			};

			$aAttachment['content'] = ob_get_contents();
			@ob_end_clean();
			
			static::Trace('Resized image is %1$s bytes long.', strlen($aAttachment['content']));
				
			return $aAttachment;
		}
				
	}
		
	
	/*
	 * Resize an image attachment so that it fits in the given dimensions.
	 *
	 * @param string $sImageData Image data
	 *
	 * @return array Array with image dimensions
	 */
	public static function GetImageSize(string $sImageData) : array {
		
		if(function_exists('getimagesizefromstring')) {
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
