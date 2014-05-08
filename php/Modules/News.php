<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Modules_News extends Modules_Generic
{
	public function __construct ($objCMS)
	{
		$this->setName ('news');
		
		$this->addTitleField ();
		$this->addDateField ('date', true);
		$this->addHTMLField ('content');
		
		parent::__construct ($objCMS);
	}
}
?>
