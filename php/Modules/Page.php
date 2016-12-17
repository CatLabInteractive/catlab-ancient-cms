<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Modules_Page extends Modules_Module
{
	public function getContent ()
	{
		return $this->getPageHTML ($this->objCMS->getRecordId ());
	}
	
	public function getPageHTML ($id)
	{
		$content = $this->getMetaContent ($id);
		
		if ($content)
		{
			$check_content = strip_tags ($content['text']);
			$check_content = trim ($check_content);
	
			if (empty ($check_content))
			{
				return null;
			}
		
			$page = new Core_Template ();
			
			$page->set ('title', Core_Tools::output_varchar ($content['title']));
			$page->set ('content', Core_Tools::output_html ($content['text']));
			
			return $page->parse ('modules/page/content.phpt');
		}
	}

	public function getPluginEditor ($id = null)
	{
		$db = Core_Database::__getInstance ();
		$page = new Core_Template ();
		
		$aContent = $this->getMetaContent ($id, true);
		
		// Get all languages
		$content = array ();
		$title = array ();
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

			$title[$k] = Core_Tools::output_form ($aContent['title_'.$k]);
			$content[$k] = Core_Tools::output_html ($aContent['text_'.$k], true);
		}
		
		$page->set ('content', $content);
		$page->set ('title', $title);
		
		$page->set ('content_action', $this->objCMS->getAdminUrl ('pages', 'edit', $aContent['n_id'], 'output=json'));
		
		return $page->parse ('modules/page/editor.phpt');
	}
	
	public function getEditorActions ($id = null)
	{
		$page = new Core_Template ();
		
		$page->set ('remove_url', $this->objCMS->getAdminUrl ('pages', 'delete', $id, 'output=json'));
		
		return $page->parse ('modules/page/actions.phpt');
	}
	
	public function getEditorJSON ($id = null)
	{
		switch ($this->objCMS->getAction ())
		{
			case 'edit':
				return $this->saveContent ($id);
			break;
			
			case 'delete':
				return $this->removePage ($id);
			break;
		}
	}
	
	protected function saveContent ($id)
	{
		$this->doRemoveMetaContent ($id);
		foreach ($this->objCMS->getAllLanguages () as $k => $v)
		{
			$title = Core_Tools::getInput ('_POST', 'title_'.$k, 'varchar');
			$content = Core_Tools::getInput ('_POST', 'content_'.$k, 'varchar');
			
			// Let's process content
			$content = $this->filterContent ($content);
			
			$this->doSaveMetaContent ($id, $k, $title, $content);
		}
	
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your page has been saved.'
			)
		);
	}
	
	private function filterContent ($content)
	{
		return Core_Tools::fixTinyMCEInput ($content);
	}
	
	protected function doRemoveMetaContent ($id)
	{
		$db = Core_Database::__getInstance ();
		$db->remove ('meta2content', "m_type = 'page' AND c_id = '".$id."'");
	}
	
	protected function doSaveMetaContent ($id, $lng, $title, $content)
	{
		$db = Core_Database::__getInstance ();
		
		// Insert the content
		$db->insert
		(
			'meta2content',
			array
			(
				'm_type' => 'page',
				'm_lang' => $lng,
				'c_id' => $id,
				'm_title' => $title,
				'm_text' => html_entity_decode ($content, ENT_NOQUOTES, 'UTF-8')
			)
		);
	}
	
	private function removePage ()
	{
		if (Pages_Navigation::doRemovePage ($this->objCMS->getRecordId ()))
		{
			return json_encode
			(
				array
				(
					'status' => 'success',
					'message' => 'Your page has been removed.',
					'redirect' => $this->objCMS->getAdminUrl ('navigation')
				)
			);
		}
		else
		{
			return json_encode
			(
				array
				(
					'status' => 'failed',
					'message' => 'Something went wrong during the removal of this page.',
					'redirect' => $this->objCMS->getAdminUrl ('navigation')
				)
			);
		}
	}
	
	protected function getMetaContent ($id, $allLanguages = false)
	{
		$fields = "n.n_id, n.n_container, n.n_p_id, n.n_level, n.n_module, n.n_options, ";
		$joins = "";

		$id = intval ($id);
		

		if (!$allLanguages)
		{
			$fields .= LANGUAGE_TAG.".m_title AS title, ";
			$fields .= LANGUAGE_TAG.".m_text AS text, ";
		
			$joins .= "LEFT JOIN meta2content ".LANGUAGE_TAG." ON (".LANGUAGE_TAG.".m_type = 'page' ".
				"AND ".LANGUAGE_TAG.".m_lang = '".LANGUAGE_TAG."' AND ".LANGUAGE_TAG.".c_id = n.n_id) ";
		}
		else
		{
			$languages = $this->objCMS->getAllLanguages ();
			foreach ($languages as $k => $v)
			{
				$fields .= "{$k}.m_title AS title_{$k}, ";
				$fields .= "{$k}.m_text AS text_{$k}, ";
			
				$joins .= "LEFT JOIN meta2content {$k} ON ({$k}.m_type = 'page' ".
					"AND {$k}.m_lang = '{$k}' AND {$k}.c_id = n.n_id) ";
			}
		}
		
		$fields = substr ($fields, 0, -2);
		
		$sql = "SELECT $fields FROM navigation n $joins WHERE n.n_id = {$id} ";
		
		$db = Core_Database::__getInstance ();
		$l = $db->getDataFromQuery ($db->customQuery ($sql));
		
		if (count ($l) == 1)
		{
			// Replace all links
			$out = $l[0];
			
			$out['text'] = isset ($out['text']) ? $this->fixLinksInHTML ($out['text']) : null;
			
			return $out;
		}
		else
		{
			return false;
		}
	}
	
	private function fixLinksInHTML ($input)
	{
		$callback = create_function 
		(
			'$match',
			'
				$out = explode (\'"\', $match[0]);
				
				foreach ($_COOKIE as $kk => $vv)
				{
					if (strpos ($kk, "cms_ref_") !== false)
					{
						$refn = substr ($kk, strlen ("cms_ref_"));
				
						if 
						(
							!strpos ($out[0], $refn."=") && 
							strpos ($out[0], CMS_FULL_URL) === false
						)
						{
							if (strpos ($out[0], "?") > 0)
							{
								$out[0] .= "&".$refn."=".$vv;
							}
							else
							{
								$out[0] .= "?".$refn."=".$vv;
							}
						}
					}
				}
		
				return implode (\'"\', $out);;
			'
		);

		$regexp = '@(http?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@'; 
		$input = preg_replace_callback ($regexp, $callback, $input);
		
		return $input;
	}
	
	public function canHaveWidgets ()
	{
		return true;
	}
}
?>
