<?php
class Pages_Upload extends Pages_Admin
{
	public function getHTML ()
	{
		$text = Core_Text::__getInstance ();
		$text->setFile ('admin');
		$text->setSection ('upload');
	
		$page = $this->getTemplate ();
		
		$page->set ('header', $this->getHeader ());
		
		// Action
		$sAction = $this->objCMS->getAction ();
		switch ($sAction)
		{
			case 'upload':
				// Do nothing really.
			break;
		
			default:
				$sAction = 'gallery';
			break;
		}
		
		$page->set ('action', $sAction);
		
		// Act on input
		if ($sAction == 'upload' && isset ($_FILES['imageFile']))
		{
			try
			{
				$result = $this->doUploadImage ();
				$page->set ('success', $text->get ('success'));
			}
			catch (Exception $e)
			{
				$page->set ('error', $text->get ('error_'.$e->getCode (), 'upload', 'admin', $e->getMessage ()));
			}
		}
		
		// Form actions
		$page->set ('imgupload_url', $this->objCMS->getAdminUrl ('upload', 'upload'));
		
		// Set some translations
		$page->set ('gallery', $text->get ('gallery'));
		$page->set ('upload', $text->get ('upload'));
		
		// Make gallery content
		$page->set ('list_images', $this->getAllImages ());
		
		return $page->parse ('pages/upload/upload.phpt');
	}
	
	private function getAllImages ()
	{
		$o = array ();
		
		$dir = scandir (IMAGE_THUMB_DIR);
		foreach ($dir as $v)
		{
			if (!is_dir (IMAGE_THUMB_DIR . $v))
			{
				$o[] = array
				(
					'date_modified' => filemtime (IMAGE_THUMB_DIR . $v),
					'file_url' => IMAGE_UPLOAD_URL . $v,
					'thumbnail_url' => IMAGE_THUMB_URL . $v
				);
			}
		}
		
		arsort ($o);
		
		return $o;
	}
	
	private function doUploadImage ()
	{
		$file = $_FILES['imageFile'];
		$doResize = Core_Tools::getInput ('_POST', 'doResize', 'string') == 'yes';
		$forceThumbSize = Core_Tools::getInput ('_POST', 'forceThumbSize', 'string') == 'yes';
		
		// *************************
		// Check for upload errors
		// *************************
		if ($file['error'] != UPLOAD_ERR_OK || !is_uploaded_file ($file['tmp_name']))
		{
			throw new Exception ('File upload error: '.$file['error'], 10);
		}

		// *************************
		// Create image
		// *************************
		$extension = $this->getFileType ($file['name']);

		$size = getimagesize ($file['tmp_name']);
		if (!$size)
		{
			throw new Exception ('This upload is not an image.', 20);
		}
		
		$im = $this->createImageFromFile ($file);
		
		$objSettings = $this->objCMS->getSettings ();
		
		$partname = $this->getNewFilename ();
		$filename = IMAGE_UPLOAD_DIR . $partname;
		$thumbname = IMAGE_THUMB_DIR . $partname . '.jpg';
		
		$quality = $objSettings->getSetting ('upload_img_quality');
		
		// *************************
		// If needed: resize
		// *************************
		if ($doResize)
		{
			$filename .= '.jpg';
			
			$width = $objSettings->getSetting ('upload_img_width');
			$height = $objSettings->getSetting ('upload_img_height');
			
			$zoom = max
			(
				$size[0] / $width,
				$size[1] / $height
			);
			
			$newSize = array
			(
				$zoom > 1 ? floor ($size[0] / $zoom) : $size[0],
				$zoom > 1 ? floor ($size[1] / $zoom) : $size[1]
			);
			
			$newImage = imagecreatetruecolor ($newSize[0], $newSize[1]);
			imagecopyresampled ($newImage, $im, 0, 0, 0, 0, $newSize[0], $newSize[1], $size[0], $size[1]);
			imagejpeg ($newImage, $filename, $quality);
		}
		else
		{
			// Just move the file to the right directory
			$filename .= '.'.$extension;
			move_uploaded_file ($file['tmp_name'], $filename);
			
			// Use the original.
			$file['tmp_name'] = $filename;
		}
		
		// *************************
		// Create the thumbnail
		// *************************
		$this->createThumbnail ($file, $thumbname, $forceThumbSize);
	}
	
	private function getNewFilename ()
	{
		return date ('YmdHis'.rand (100,999));
	}
	
	private function createImageFromFile ($file)
	{
		$extension = $this->getFileType ($file['name']);
		
		switch ($extension)
		{
			case 'jpeg':
			case 'jpg':
				$im = imagecreatefromjpeg ($file['tmp_name']);
			break;
			
			case 'png':
				$im = imagecreatefrompng ($file['tmp_name']);
			break;
			
			case 'gif':
				$im = imagecreatefromgif ($file['tmp_name']);
			break;
			
			default:
				throw new Exception ('Extension not recognized: '.$extension, 30);
			break;
		}
		
		return $im;
	}
	
	private function createThumbnail ($file, $destination, $forceSize = false)
	{
		$objSettings = $this->objCMS->getSettings ();
		
		$filename = $file['tmp_name'];
		
		$size = getimagesize ($filename);
		if (!$size)
		{
			throw new Exception ('This upload is not an image.', 20);
		}
		
		$im = $this->createImageFromFile ($file);
	
		$thumbSize = array
		(
			$objSettings->getSetting ('upload_thumb_width'),
			$objSettings->getSetting ('upload_thumb_height')
		);
		
		$zoom = max
		(
			$size[0] / $thumbSize[0],
			$size[1] / $thumbSize[1]
		);
		
		$newThumbSize = array
		(
			$size[0] / $zoom,
			$size[1] / $zoom
		);
		
		if ($forceSize)
		{
			$newImage = imagecreatetruecolor ($thumbSize[0], $thumbSize[1]);
			
			$color = imagecolorallocate ($newImage, 255, 255, 255);
			imagefill ($newImage, 0, 0, $color);
		
			$offsetX = max (0, ceil (($thumbSize[0] - $newThumbSize[0])) / 2);
			$offsetY = max (0, ceil (($thumbSize[1] - $newThumbSize[1])) / 2);
		}
		else
		{
			$newImage = imagecreatetruecolor ($newThumbSize[0], $newThumbSize[1]);
			
			$offsetX = 0;
			$offsetY = 0;
		}
		
		imagecopyresampled ($newImage, $im, $offsetX, $offsetY, 0, 0, $newThumbSize[0], $newThumbSize[1], $size[0], $size[1]);
		imagejpeg ($newImage, $destination, $objSettings->getSetting ('upload_img_quality'));
	}
	
	private function getFileType ($filename)
	{
		$ext = strtolower (substr ($filename, strrpos ($filename, '.') + 1));
		return $ext;
	}
}
?>
