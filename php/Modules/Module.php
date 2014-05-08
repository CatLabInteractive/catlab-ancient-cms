<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
abstract class Modules_Module
{
	protected $objCMS;
	
	public function __construct ($objCMS)
	{
		$this->objCMS = $objCMS;
		$this->initialize ();
	}

	protected function initialize ()
	{

	}
	
	public static function getModule ($sModule, $objCMS)
	{
		$classname = 'Modules_'.ucfirst ($sModule);
		
		if (class_exists ($classname))
		{
			return new $classname ($objCMS);
		}
		else
		{
			return false;
		}
	}
	
	private function getNavContainers ($showAll)
	{
		// Initialize containers
		$settings = Core_Template::getThemeSettings ();
		if ($settings['navigation'])
		{
			$navigations = array_merge
			(
				$settings['navigation'],
				array
				(
					'draft' => 'Draft'
				)
			);
		}
		else
		{
			$navigations = array 
			(
				'main' => 'Main navigation',
				'draft' => 'Draft'
			);
		}
		
		if (!$showAll)
		{
			unset ($navigations['draft']);
		}
		
		// Loop navigations
		$text = Core_Text::__getInstance ();
		
		foreach ($navigations as $container => $value)
		{
			$output[$container] = new Logic_NavContainer 
			(
				$this->objCMS,
				$container,
				$text->get ($container, 'navigation', 'main', $value)
			);
		}
		
		return $output;
	}
	
	/*
		Generate navigation
	*/
	public function getNavigation ($showAll = false)
	{
		$db = Core_Database::__getInstance ();

		// Fetch the navigation from database
		$output = array ();
		try
		{		
			$pages = $db->getDataFromQuery
			(
				$db->customQuery 
				("
					SELECT
						n.n_id,
						n.n_container,
						n.n_p_id,
						n.n_level,
						n.n_options,
						n.n_module,
						m.m_title,
						m.m_text
					FROM
						navigation n
					LEFT JOIN
						meta2content m 
					ON
						(m.c_id = n.n_id AND m.m_type = 'page' AND m_lang = '".LANGUAGE_TAG."')
					ORDER BY
						n_level ASC, n_p_id ASC, n_sorting ASC
				")
			);
		}
		catch (Core_DBError $e)
		{
			if ($e->getCode () == MYSQL_TABLE_NOT_EXIST)
			{
				Installation::doMysqlInitialSetup ();
			}
			else
			{
				echo $e;
			}
			
			$pages = array ();
		}
		
		// Make the output
		$id2children = array ();
		
		$output = $this->getNavContainers ($this->objCMS, $showAll);
		
		// Pages
		foreach ($pages as $page)
		{
			// Create the field array
			$module = self::getModule ($page['n_module'], $this->objCMS);
			
			if ($module)
			{
				$field = new Logic_Navigation ($module->getNavigationRow ($page));
			}
			else
			{
				$field = new Logic_Navigation ($this->getNavigationRow ($page));
			}
			
			// Add to the output 
			if ($showAll || !$field->isEmpty ())
			{
				// Put it in the right parent
				if ($page['n_level'] == 0 && !empty ($page['n_container']))
				{
					if (!isset ($output[$page['n_container']]))
					{
						$output[$page['n_container']] = new Logic_NavContainer ($this->objCMS, $page['n_container'], $page['n_container']);
					}
					$output[$page['n_container']]->appendChild ($field);
					$id2children[$page['n_id']] = $field;
				}
				elseif (isset ($id2children[$page['n_p_id']]))
				{
					$id2children[$page['n_p_id']]->appendChild ($field);
					$id2children[$page['n_id']] = $field;
				}
				// Orphaned pages
				elseif ($showAll)
				{
					$output['draft']['aItems'][$page['n_id']] = $field;
				}
			}
		}
		
		return $output;
	}
	
	protected function getNameInUrl ($name)
	{
		$name = strtolower ($name);
		return str_replace (' ', '_', $name);
	}
	
	public function getNavigationRow ($page)
	{
		return array
		(
			'id' => $page['n_id'],
			'sName' => Core_Tools::output_varchar (!empty ($page['m_title']) ? $page['m_title'] : 'unnamed - '.$page['n_id']),
			'sUrl' => $this->objCMS->getUrl ($page['n_module'], 'view', $page['n_id'], null, $this->getNameInUrl ($page['m_title'])),
			'sEditUrl' => $this->objCMS->getAdminUrl ('pages', 'edit', $page['n_id']),
			'aChildren' => array (),
			'sClass' => ($page['n_module'] == 'page' ? 'page' : 'page_gear'),
			'sTarget' => '_self',
			'sTargetLoc' => 'internal',
			'bShow' => !empty ($page['m_title'])
		);
	}

	public function getOutput ()
	{
		return null;
	}
	
	/*
		Show this modules content
	*/
	public function getContent ()
	{
		return '<p>Invalid module: content not found.</p>';
	}
	
	public function getCMSContent ()
	{
		return '<p>Invalid module: cms content not found.</p>';
	}
	
	/*
		Returns a new Core_Template object with some preset values
	*/
	protected function getTemplate ()
	{
		$page = new Core_Template ();
		
		$page->set ('objCMS', $this->objCMS);
		$page->set ('objSettings', $this->objCMS->getSettings ());		
		$page->set ('objText', $this->objCMS->getText ());
		
		return $page;
	}
	
	protected function getHeader ()
	{
		$header = $this->getTemplate ();
		
		// Some default settings
		$header->set ('path_icons', CMS_FULL_URL.'core/icons/');
		$header->set ('path_static', CMS_FULL_URL.'core/');
		
		$header->set ('url_upload', $this->objCMS->getAdminUrl ('upload'));
		
		$rss = $this->getRSSLink ();
		if (!empty ($rss))
		{
			$header->set ('rss_link', $rss);
		}
		
		return $header->parse ('blocks/header.phpt');
	}
	
	public function getModuleName ()
	{
		$class = get_class ($this);
		$class = explode ('_', $class);
		return $class[count ($class) - 1];
	}
	
	public function getHTML ($template = 'index.phpt')
	{
		$page = $this->getTemplate ();
		
		$jsf = $this->getJSFile ();
		if (!empty ($jsf))
		{
			$page->set ('module_jsfile', $jsf);
		}
		
		$csf = $this->getCSSFile ();
		if (!empty ($csf))
		{
			$page->set ('module_cssfile', $csf);
		}
		
		// Fetch the navigation
		$page->set ('navigation', $this->getNavigation ());
		
		// Fetch the content
		$page->set ('content', $this->getContent ());
		
		// Start with the header
		$page->set ('header', $this->getHeader ());
		
		return $page->parse ($template);
	}
	
	public function getJSON ()
	{
		return json_encode
		(
			array
			(
				'status' => 'failed',
				'message' => "This module doesn't provide any JSON actions."
			)
		);
	}
	
	/*
		Admin part
	*/
	public function getEditorJSON ()
	{
		return json_encode
		(
			array
			(
				'status' => 'failed',
				'message' => "This module does not have any options."
			)
		);
	}
	
	public function getPluginEditor ()
	{
		return '<p class="page_nosettings">This module does not have settings</p>';
	}
	
	public function getEditorActions ()
	{
		return null;
	}
	
	protected function getJSFile ()
	{
		return false;
	}
	
	protected function getCSSFile ()
	{
		return false;
	}
	
	public function showAdminTab ()
	{
		return false;
	}
	
	public function canHaveWidgets ()
	{
		return false;
	}
	
	public function getRSSLink ()
	{
		return null;
	}
	
	public function getWidgets ()
	{
		$db = Core_Database::__getInstance ();
	}
	
	protected function setMetaContent ($sType, $langk, $id, $sTitle, $sText)
	{
		$id = intval ($id);
		
		$db = Core_Database::__getInstance ();
	
		// Check if exist
		$chk = $db->select ('meta2content', array ('m_id'), "m_type = '".$sType."' AND m_lang = '{$langk}' AND c_id = {$id}");

		if (count ($chk) == 0)
		{
			$db->insert
			(
				'meta2content',
				array
				(
					'c_id' => $id,
					'm_type' => $sType,
					'm_lang' => $langk,
					'm_title' => $sTitle,
					'm_text' => $sText
				)
			);
		}
		else
		{
			$db->update
			(
				'meta2content',
				array
				(
					'm_title' => $sTitle,
					'm_text' => $sText
				),
				"m_id = ".$chk[0]['m_id']
			);
		}
	}
	
	protected function getMetaContent ($sTableName, $id, $allLanguages = false)
	{
		$fields = "n.n_id, n.n_container, n.n_p_id, n.n_level, n.n_module, n.n_options, ";
		$joins = "";
		
		$id = intval ($id);

		if (!$allLanguages)
		{
			$fields .= LANGUAGE_TAG.".m_title AS title, ";
			$fields .= LANGUAGE_TAG.".m_text AS text, ";
		
			$joins .= "LEFT JOIN meta2content ".LANGUAGE_TAG." ON (".LANGUAGE_TAG.".m_type = '{$sTableName}' ".
				"AND ".LANGUAGE_TAG.".m_lang = '".LANGUAGE_TAG."' AND ".LANGUAGE_TAG.".c_id = n.n_id) ";
		}
		else
		{
			$languages = $this->objCMS->getAllLanguages ();
			foreach ($languages as $k => $v)
			{
				$fields .= "{$k}.m_title AS title_{$k}, ";
				$fields .= "{$k}.m_text AS text_{$k}, ";
			
				$joins .= "LEFT JOIN meta2content {$k} ON ({$k}.m_type = '{$sTableName}' ".
					"AND {$k}.m_lang = '{$k}' AND {$k}.c_id = n.n_id) ";
			}
		}
		
		$fields = substr ($fields, 0, -2);
		
		$sql = "SELECT $fields FROM navigation n $joins WHERE n.n_id = {$id} ";
		
		$db = Core_Database::__getInstance ();
		$l = $db->getDataFromQuery ($db->customQuery ($sql));
		
		if (count ($l) == 1)
		{
			return $l[0];
		}
		else
		{
			return false;
		}
	}
}
?>
