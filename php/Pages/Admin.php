<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Pages_Admin extends Modules_Module
{
	/*
		Returns a multiple dimension array
		with all (printable) navigation items.
	*/
	public function getNavigation ($showAll = false)
	{
		$text = Core_Text::__getInstance ();
		
		$o = array ();
		$o['main'] = new Logic_NavContainer ($this->objCMS, 'main', $text->get ('navigation', 'admin', 'navigation'));
		
		// Navigation
		$o['main']->appendChild
		(
			new Logic_Navigation
			(
				array
				(
					'id' => 'nav',
					'sName' => 'Navigation',
					'sUrl' => $this->objCMS->getAdminUrl ('navigation')
				)
			)
		);
		
		foreach ($this->objCMS->getAllModules () as $item => $module)
		{
			if ($module->showAdminTab ())
			{
				$o['main']->appendChild
				(
					new Logic_Navigation
					(
						array
						(
							'id' => $item,
							'sName' => $text->get ($item, 'navigation', 'admin'),
							'sUrl' => $this->objCMS->getAdminUrl ($item)
						)
					)
				);
			}
		}
		
		// Logout		
		$o['main']->appendChild
		(
			new Logic_Navigation
			(
				array
				(
					'id' => 'logout',
					'sName' => 'Logout',
					'sUrl' => $this->objCMS->getAdminUrl ('logout', null, null, 'logout=yes')
				)
			)
		);
		
		// Settings
		$o['main']->appendChild
		(
			new Logic_Navigation
			(
				array
				(
					'id' => 'settings',
					'sName' => 'Settings',
					'sUrl' => $this->objCMS->getAdminUrl ('settings')
				)
			)
		);
		
		return $o;
	}
	
	public function getAllPages ()
	{
		return parent::getNavigation (true);
	}
	
	public function getHTML ($template = 'admin/index.phpt')
	{
		return parent::getHTML ($template);
	}
	
	protected function getJSFile ()
	{
		return strtolower ($this->getModuleName ());;
	}
	
	protected function getCSSFile ()
	{
		return strtolower ($this->getModuleName ());;
	}
}
?>
