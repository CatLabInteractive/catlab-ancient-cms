<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Modules_Home extends Modules_Page
{
	public function getContent ()
	{
		return $this->getPageHTML ($this->getHomepageRecordId ());
	}
	
	public function getHomepageRecordId ()
	{
		$db = Core_Database::__getInstance ();
		
		$l = $db->select
		(
			'navigation',
			array ('n_id'),
			"n_container = 'main'",
			'n_sorting',
			1
		);
		
		if (count ($l) == 1)
		{
			return $l[0]['n_id'];
		}
		else
		{
			return 1;
		}
	}
}
?>
