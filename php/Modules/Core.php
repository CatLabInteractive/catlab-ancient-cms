<?php
/**
 * Created by PhpStorm.
 * User: daedeloth
 * Date: 8/05/14
 * Time: 15:33
 */

class Modules_Core
	extends Modules_Page
{
	public function getHTML ()
	{
		$url = $this->objCMS->getAction ();
		$path = CMS_SYSTEM_PATH . 'core/' . $url;

		if (file_exists ($path))
		{
			$extension = end (explode ('.', $path));
			switch (strtolower ($extension))
			{
				case 'js':
					header ('Content-type: application/json');
				break;

				case 'gif':
				case 'png':
				case 'jpg':
				case 'jpeg':
					header ('Content-type: image/' . $extension);
				break;
			}

			return file_get_contents ($path);
		}
		else
		{
			return 'File not found: ' . $path;
		}
	}
} 