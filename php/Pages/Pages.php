<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Pages_Pages extends Pages_Admin
{
	public function getContent ()
	{
		$text = Core_Text::__getInstance ();
		$db = Core_Database::__getInstance ();
		
		$id = $this->objCMS->getRecordId ();
		
		// Check for add input
		if ($this->objCMS->getAction () == 'add')
		{
		
			$sModule = Core_Tools::getInput ('_GET', 'module', 'varchar');
			if (!$sModule || !$this->objCMS->moduleExists ($sModule))
			{
				$sModule = 'page';
			}
		
			$id = $db->insert
			(
				'navigation',
				array
				(
					'n_container' => 'draft',
					'n_p_id' => 0,
					'n_level' => 0,
					'n_module' => $sModule
				)
			);
		}
		
		
		
		// Check for module input
		$sModule = Core_Tools::getInput ('_POST', 'pg_module', 'varchar');
		
		if ($sModule && $this->objCMS->moduleExists ($sModule))
		{
			$db->update
			(
				'navigation',
				array
				(
					'n_module' => $sModule
				),
				"n_id = '".$id."'"
			);
		}
	
		$page = new Core_Template ();
		
		// Load the pages
		$aContent = $this->getNavigationContent ($id);
		
		// Set form url
		$page->set ('action_module', $this->objCMS->getAdminUrl ('pages', 'edit', $id));
		
		$aModules = $this->objCMS->getAllModules ();
		foreach ($aModules as $k => $v)
		{
			$page->addListValue 
			(
				'modules', 
				array 
				(
					'id' => $k,
					'title' => $text->get ($k, 'contentModules', 'admin'),
					'selected' => ($aContent['n_module'] == $k)
				)
			);
		}
		
		$module = $this->getPageModule ($id);
		if ($module)
		{
			$page->set ('editor_content', $module->getPluginEditor ($id));
			$page->set ('editor_actions', $module->getEditorActions ($id));
		}
		else
		{
			$page->set ('editor_content', '<p class="page_nosettings">This module does not have settings</p>');
		}
		
		return $page->parse ('pages/pages/pages.phpt');
	}
	
	private function getPageModule ($id)
	{
		$aContent = $this->getNavigationContent ($id);
		if ($aContent)
		{			
			// Load the right "edit form"
			return self::getModule ($aContent['n_module'], $this->objCMS);
		}
		else
		{
			return false;
		}
	}
	
	public function getJSON ()
	{
		$id = $this->objCMS->getRecordId ();
		
		if ($id > 0)
		{
			$module = $this->getPageModule ($this->objCMS->getRecordId ());
			if ($module)
			{
				return $module->getEditorJSON ($this->objCMS->getRecordId ());
			}
			else
			{
				return parent::getJSON ();
			}
		}
	}
	
	protected function getNavigationContent ($id)
	{
		$db = Core_Database::__getInstance ();
		
		// Load the page
		$data = $db->select
		(
			'navigation',
			array ('*'),
			"n_id = '".$id."'"
		);
		
		if (count ($data) == 1)
		{
			return $data[0];
		}
		else
		{
			return false;
		}
	}
}
?>
