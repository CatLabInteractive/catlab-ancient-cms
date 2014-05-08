<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Pages_ModuleManager extends Pages_Admin
{
	private $objModule;
	
	public function __construct ($objModule, $objCMS)
	{
		$this->objModule = $objModule;
		parent::__construct ($objCMS);
	}
	
	public function getContent ()
	{
		return $this->objModule->getCMSContent ();
	}
	
	public function getJSON ()
	{
		return $this->objModule->getCMSJSON ();
	}
	
	protected function hasJSFile ()
	{
		return $this->objModule->hasJSFile ();
	}
	
	protected function hasCSSFile ()
	{
		return $this->objModule->hasJSFile ();
	}
}
?>
