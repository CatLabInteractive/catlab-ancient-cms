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

		if (file_exists ('core/' . $url))
		{
			echo file_get_contents ('core/' . $url);
		}
	}
} 