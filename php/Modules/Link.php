<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Modules_Link extends Modules_Page
{
	public function getNavigationRow ($page)
	{
		$field = parent::getNavigationRow ($page);
		$options = explode (';', $page['m_text']);
		
		$field['sClass'] = 'page_link';
		
		// Now, let's replace the sUrl
		if (!empty ($options[0]))
		{
			$field['sUrl'] = $options[0];
			
			// If target is set, replace it.
			if (isset ($options[1]))
			{
				$field['sTarget'] = $options[1];
			}
			else
			{
				$field['sTarget'] = '_blank';
			}
		}
		
		$field['sTargetLoc'] = 'external';
		
		return $field;
	}
	
	public function getPluginEditor ($id = null)
	{
		$page = new Core_Template ();
		
		$aContent = $this->getMetaContent ($id, true);
		
		$title = array ();
		$url = array ();
		foreach ($this->objCMS->getAllLanguages () as $k => $v)
		{
			$page->addListValue
			(
				'languages',
				array
				(
					'id' => $k,
					'name' => $v
				)
			);
			
			$title[$k] = Core_Tools::output_input ($aContent['title_'.$k]);
			$url[$k] = Core_Tools::output_input ($aContent['text_'.$k], 250);
		}
		
		$page->set ('title', $title);
		$page->set ('url', $url);
		
		$page->set ('content_action', $this->objCMS->getAdminUrl ('pages', 'edit', $aContent['n_id'], 'output=json'));
		
		return $page->parse ('modules/link/editor.phpt');
	}
	
	public function saveContent ($id)
	{
		$this->doRemoveMetaContent ($id);
		foreach ($this->objCMS->getAllLanguages () as $k => $v)
		{
			$title = Core_Tools::getInput ('_POST', 'title_'.$k, 'varchar');
			$surl = Core_Tools::getInput ('_POST', 'surl_'.$k, 'varchar');
			
			$this->doSaveMetaContent ($id, $k, $title, $surl);
		}
	
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your link has been saved.'
			)
		);
	}
	
	public function getEditorActions ($id = null)
	{
		$page = new Core_Template ();
		$page->set ('remove_url', $this->objCMS->getAdminUrl ('pages', 'delete', $id, 'output=json'));
		return $page->parse ('modules/page/actions.phpt');
	}
	
	public function getContent ()
	{
		return '<h2>Invalid redirection</h2>'.
			'<p>This link is faulty. Please supply a valid link.</p>';
	}
}
?>
