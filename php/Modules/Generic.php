<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
abstract class Modules_Generic extends Modules_Module
{
	private $aFields = array ();
	private $aOverview = array 
	(
		array
		(
			'sName' => 'id',
			'sType' => 'id',
			'bTranslated' => false
		)
	);
	
	private $sName = '';
	private $sTableName;
	private $iTransCounter = 0;
	
	/*
		Output (used to hijack the output if RSS is selected
	*/
	public function getHTML ($template = 'index.phpt')
	{
		switch ($this->objCMS->getAction ())
		{
			case 'rss':
				return $this->getRSS ();
			break;
			
			default:
				return parent::getHTML ();
			break;
		}
	}
	
	public function getRSSLink ()
	{
		return $this->objCMS->getUrl ($this->sName, 'rss');
	}
	
	/*
		Field constructors
	*/
	private function addField ($sName, $sType, $isTranslated = false, $inOverview = false)
	{
		$this->aFields[] = array
		(
			'sName' => $sName,
			'sType' => $sType,
			'bTranslated' => (bool)$isTranslated,
			'iTransPart' => $this->iTransCounter
		);
		
		if ($inOverview)
		{
			$this->aOverview[] = array
			(
				'sName' => $sName,
				'sType' => $sType,
				'bTranslated' => (bool)$isTranslated,
				'iTransPart' => $this->iTransCounter
			);
		}
		
		if ($isTranslated && $sName != 'title')
		{
			$this->iTransCounter ++;
		}
	}
	
	protected function setName ($sName)
	{
		$this->sName = $sName;
		$this->sTableName = 'mod_'.$this->sName;
	}
	
	protected function addTitleField ($inOverview = true)
	{
		$this->addField ('title', 'text', true, true);
	}
	
	protected function addTextField ($sName, $translated = true)
	{
		$this->addField ($sName, 'text', $translated);
	}
	
	protected function addDateField ($sName, $inOverview = false)
	{
		$this->addField ($sName, 'date', false, $inOverview);
	}
	
	protected function addHTMLField ($sName, $translated = true)
	{
		$this->addField ($sName, 'html', $translated);
	}
	
	private function getRecordData ($id, $mysqlData = false)
	{
		$db = Core_Database::__getInstance ();
	
		$id = intval ($id);
		$data = $this->getDataFromId ($id, false, $mysqlData);
	
		$out = array ();
		foreach ($this->aFields as $v)
		{
			$out[$v['sName']] = $v;
			if ($v['bTranslated'])
			{
				$out[$v['sName']]['original'] = $data[$v['sName']][LANGUAGE_TAG];
			}
			else
			{
				$out[$v['sName']]['original'] = $data[$v['sName']];
			}
			
			// Check for dates
			if ($v['sType'] == 'date')
			{
				$timestamp = $db->toUnixtime ($out[$v['sName']]['original']);
				if (!$timestamp)
				{
					$timestamp = null;
				}
				$out[$v['sName']]['timestamp'] = $timestamp;
			}
			
			$out[$v['sName']]['value'] = 
				$this->doMakeFieldValueLookNice ($v['sType'], $out[$v['sName']]['original'], true);
		}
		
		return $out;
	}
	
	private function getRecord_HTML ($id, $mysqlData = false)
	{		
		$page = new Core_Template ();
		
		$out = $this->getRecordData ($id, $mysqlData);
		
		$page->set ('data', $out);
				
		return $page->parse ('modules/generic/record.phpt');
	}
	
	private function getRecordList_HTML ($admin = false)
	{
		$text = Core_Text::__getInstance ();
	
		$page = new Core_Template ();
		$page->set ('records', $this->getRecordList (LANGUAGE_TAG, true, $admin));
		
		// Get table headers
		foreach ($this->aOverview as $v)
		{
			$page->addListValue
			(
				'table_headers',
				array 
				(
					$v['sName'], 
					$v['sType'],
					$text->get ($v['sName'], $this->sName, 'generic', $v['sName'])
				)
			);
		}
		
		$page->set ('isAdmin', $admin);
		
		$page->set ('editUrl', $this->objCMS->getAdminUrl ($this->sName, 'edit'));
		$page->set ('removeUrl', $this->objCMS->getAdminUrl ($this->sName, 'remove'));
		$page->set ('readUrl', $this->objCMS->getUrl ($this->sName, 'read'));
		
		return $page->parse ('modules/generic/list.phpt');
	}
	
	private function getBlogView ($showEmpty = false)
	{
		$db = Core_Database::__getInstance ();
	
		$cpid = Core_Tools::getInput ('_GET', 'cpid', 'int');
		$iPage = Core_Tools::getInput ('_GET', 'page', 'int', 1);
		$perPage = 3;
	
		$page = new Core_Template ();
		
		$addWhere = "";
		if (!$showEmpty)
		{
			$addWhere = " WHERE meta2content.m_title <> '' ";
		}
		
		// Count all records
		$total = $db->select
		(
			$this->sTableName,
			array ('COUNT(*) AS aantal')
		);
		
		$total = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					COUNT(*) AS aantal
				FROM
					".$this->sTableName."
				LEFT JOIN
					meta2content
				ON
					".$this->sTableName.".m_id = meta2content.c_id 
					AND meta2content.m_lang = '".LANGUAGE_TAG."'
					AND meta2content.m_type = 'mod_".$this->sName."' 
				$addWhere
			")
		);
		
		$total = $total[0]['aantal'];
		
		$limit = Core_Tools::splitInPages
		(
			$page, 
			$total, 
			$iPage, 
			$perPage,
			$this->objCMS->getUrl ($this->sName, 'blog', null, ($cpid > 0 ? 'cpid='.$cpid : null ).'&page=')
		);
		
		$recs = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					meta2content.*,
					".$this->sTableName.".*
				FROM
					".$this->sTableName."
				LEFT JOIN
					meta2content
				ON
					".$this->sTableName.".m_id = meta2content.c_id 
					AND meta2content.m_lang = '".LANGUAGE_TAG."'
					AND meta2content.m_type = 'mod_".$this->sName."' 
				$addWhere
				ORDER BY
					".$this->sTableName.".m_id DESC
				LIMIT {$limit['limit']}
			")
		);
		
		foreach ($recs as $v)
		{
			$page->addListValue
			(
				'records',
				$this->getRecord_HTML ($v['m_id'], $v)
			);
		}
		
		return $page->parse ('modules/generic/blog.phpt');
	}
	
	private function getDataForBlogview ($page, $cpid, $iPage, $showEmpty = false, $perPage = 3)
	{
		$db = Core_Database::__getInstance ();
		
		$addWhere = "";
		if (!$showEmpty)
		{
			$addWhere = " WHERE meta2content.m_title <> '' ";
		}
		
		// Count all records
		$total = $db->select
		(
			$this->sTableName,
			array ('COUNT(*) AS aantal')
		);
		
		$total = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					COUNT(*) AS aantal
				FROM
					".$this->sTableName."
				LEFT JOIN
					meta2content
				ON
					".$this->sTableName.".m_id = meta2content.c_id 
					AND meta2content.m_lang = '".LANGUAGE_TAG."'
					AND meta2content.m_type = 'mod_".$this->sName."' 
				$addWhere
			")
		);
		
		$total = $total[0]['aantal'];
		
		$limit = Core_Tools::splitInPages
		(
			$page, 
			$total, 
			$iPage, 
			$perPage,
			$this->objCMS->getUrl ($this->sName, 'blog', null, ($cpid > 0 ? 'cpid='.$cpid : null ).'&page=')
		);
		
		$recs = $db->getDataFromQuery
		(
			$db->customQuery
			("
				SELECT
					meta2content.*,
					".$this->sTableName.".*
				FROM
					".$this->sTableName."
				LEFT JOIN
					meta2content
				ON
					".$this->sTableName.".m_id = meta2content.c_id 
					AND meta2content.m_lang = '".LANGUAGE_TAG."'
					AND meta2content.m_type = 'mod_".$this->sName."' 
				$addWhere
				ORDER BY
					".$this->sTableName.".m_id DESC
				LIMIT {$limit['limit']}
			")
		);
		
		return $recs;
	}
	
	private function getRSS ($showEmpty = false)
	{
		$cpid = Core_Tools::getInput ('_GET', 'cpid', 'int');
		$iPage = Core_Tools::getInput ('_GET', 'page', 'int', 1);
		$perPage = 10;
		
		$page = new Core_Template ();
		
		$text = Core_Text::__getInstance ();
		
		$page->set ('name', $this->objCMS->getSiteTitle () . ' ' .$text->get ('name', 'general', $this->sName));
		
		$recs = $this->getDataForBlogview ($page, $cpid, $iPage, $showEmpty, $perPage);
		
		foreach ($recs as $v)
		{
			$data = $this->getRecordData ($v['m_id'], $v);
			
			$date = $this->getFirstElement ($data, 'date', 'timestamp');
		
			$page->addListValue
			(
				'records',
				array
				(
					'title' => $this->getFirstElement ($data, 'text'),
					'link' => $this->objCMS->getUrl ($this->sName, 'read').$v['m_id'].'/',
					'description' => strip_tags (br2nl ($this->getFirstElement ($data, 'html'))),
					'pubDate' => $date ? date ('r', $date) : null
				)
			);
		}
		
		return $page->parse ('modules/generic/rss.phpt');
	}
	
	private function getFirstElement ($data, $sType = 'title', $field = 'value')
	{
		foreach ($data as $v)
		{
			if ($v['sType'] == $sType)
			{
				if (isset ($v[$field]))
				{
					return $v[$field];
				}
			}
		}
		
		return false;
	}
	
	/*
		Database stuff
	*/	
	private function getRecordList ($sLanguage, $makeLookNice = true, $showEmpty = false)
	{
		$db = Core_Database::__getInstance ();
		
		$addWhere = "";
		if (!$showEmpty)
		{
			$addWhere = ' WHERE meta2content.m_title <> "" ';
		}
		
		try
		{
			$l = $db->getDataFromQuery
			(
				$db->customQuery
				("
					SELECT
						meta2content.m_title,
						meta2content.m_text,
						".$this->sTableName.".*
					FROM
						".$this->sTableName."
					LEFT JOIN
						meta2content
					ON
						".$this->sTableName.".m_id = meta2content.c_id 
						AND meta2content.m_lang = '".$db->escape ($sLanguage)."'
						AND meta2content.m_type = 'mod_".$this->sName."' 
					$addWhere
					ORDER BY
						".$this->sTableName.".m_id DESC
				")
			);
		}
		catch (Core_DBError $e)
		{
			if ($e->getCode () == MYSQL_TABLE_NOT_EXIST)
			{
				$this->createTable ();
			}
			else
			{
				echo $e;
			}
			
			$l = array ();
		}
		
		$out = array ();
		foreach ($l as $record)
		{		
			$content = explode ('|', $record['m_text']);
		
			$out1 = array ();
			foreach ($this->aOverview as $v)
			{
				if (!$v['bTranslated'])
				{
					$out1[$v['sName']] = $record['m_'.$v['sName']];
				}
				elseif ($v['sName'] == 'title')
				{
					$out1['title'] = $record['m_title'];
				}
				else
				{
					$out1[$v['sName']] = isset ($content[$v['iTransPart']]) ? $content[$v['iTransPart']] : null;
				}
				
				// Translate date to human readable date etc;
				$out1[$v['sName']] = $this->makeFieldValueLookNice ($v['sType'], $out1[$v['sName']], $makeLookNice);
			}
			$out[] = $out1;
		}
		
		return $out;
	}
	
	private function createTable ()
	{	
		// fetch all fields
		$fields = array ();
		foreach ($this->aFields as $v)
		{
			if (!$v['bTranslated'])
			{
				switch ($v['sType'])
				{
					case 'date':
						$sType = 'DATETIME';
					break;
					
					case 'text':
						$sType = 'VARCHAR(250)';
					break;
					
					case 'html':
						$sType = 'TEXT';
					break;
				}
			
				$fields[$v['sName']] = $sType;
			}
		}
		Installation::doGenericTableSetup ($this->sTableName, $fields);
	}
	
	/*
		Output
	*/
	
	public function getContent ()
	{
		if (empty ($this->sName))
		{
			throw new Boomerang ('Generic modules must have a name! Call setName in the modules constructor.');
		}
		
		switch ($this->objCMS->getAction ())
		{
			case 'read':
				return $this->getRecord_HTML ($this->objCMS->getRecordId ());
			break;
			
			case 'blog':
				return $this->getBlogView ();
			break;
			
			case 'list':
			default:
				$page = new Core_Template ();
				$page->setTextSection ($this->sName, 'generic');
				$page->set ('list', $this->getRecordList_HTML (false));
				return $page->parse ('modules/generic/overview.phpt');
			break;
		}
	}
	
	public function getCMSContent ()
	{
		if (empty ($this->sName))
		{
			throw new Boomerang ('Generic modules must have a name! Call setName in the modules constructor.');
		}
		
		$page = new Core_Template ();
		$text = $this->objCMS->getText ();
		
		$page->set ('addform', $this->getForm ('add'));

		$id = intval ($this->objCMS->getRecordId ());
		if ($id > 0)
		{
			$page->set ('editform', $this->getForm ('edit', $id));
		}
		
		$page->set ('module_name', $text->get ('name', 'general', $this->sName));
		
		// Fetch all current content
		$page->set ('reclist', $this->getRecordList_HTML (true));
		
		return $page->parse ('modules/generic/manager.phpt');
	}
	
	private function getForm ($action, $id = null)
	{
		if (count ($this->aFields) > 0)
		{
			$page = new Core_Template ();
		
			if ($id > 0)
			{
				$data = $this->getDataFromId ($id, false);
			}
			
			$page->set ('id', $action);
			$page->set ('action', $this->objCMS->getAdminUrl ($this->sName, $action, $id, 'output=json'));
			
			$text = $this->objCMS->getText ();
			$text->setFile ($this->sName);
			
			// Languages
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
			}
			
			// Field
			foreach ($this->aFields as $v)
			{
				$page->addListValue
				(
					$v['bTranslated'] ? 'translated' : 'general',
					array
					(
						'sType' => $v['sType'],
						'sName' => $v['sName'],
						'sLabel' => $text->get ($v['sName'], 'form', $this->sName, $v['sName']),
						'sValue' => $id > 0 ? $data[$v['sName']] : null
					)
				);
			}
		
			return $page->parse ('modules/generic/form.phpt');
		}
		else
		{
			throw new Boomerang ('Generic module must have at least 1 field.');
		}
	}
	
	private function getDataFromId ($id, $makeLookNice = true, $mysqlData = false)
	{
		$db = Core_Database::__getInstance ();
		
		if (!$mysqlData)
		{
			$id = intval ($id);
		
			$l = $db->getDataFromQuery
			(
				$db->customQuery
				("
					SELECT
						meta2content.m_title,
						meta2content.m_text,
						meta2content.m_lang,
						".$this->sTableName.".*
					FROM
						".$this->sTableName."
					INNER JOIN
						meta2content
					ON
						".$this->sTableName.".m_id = meta2content.c_id 
						AND meta2content.m_type = 'mod_".$this->sName."'
					WHERE
						".$this->sTableName.".m_id = {$id}
					ORDER BY
						".$this->sTableName.".m_id DESC
				")
			);
		}
		else
		{
			$l = array ($mysqlData);
		}
		
		// Put each language in an array
		$langs = array ();
		$key = $l[0]['m_lang'];
		foreach ($l as $v)
		{
			$langs[$v['m_lang']] = $v;
			$langs[$v['m_lang']]['content'] = explode ('|', $v['m_text']);
		}
		
		$languages = $this->objCMS->getAllLanguages ();
		
		$out = array ();
		foreach ($this->aFields as $field)
		{
			if ($field['bTranslated'])
			{
				$out[$field['sName']] = array ();
				foreach ($languages as $klang => $lang)
				{
					if (!isset ($langs[$klang]))
					{
						unset ($languages[$klang]);
					}
					else
					{
						if ($field['sName'] == 'title')
						{
							$out[$field['sName']][$klang] = $langs[$klang]['m_'.$field['sName']];
						}
						else
						{
							$out[$field['sName']][$klang] = $langs[$klang]['content'][$field['iTransPart']];
						}
					}
				}
			}
			else
			{
				$out[$field['sName']] = $langs[$key]['m_'.$field['sName']];
			}
			
			$out[$field['sName']] = $this->makeFieldValueLookNice ($field['sType'], $out[$field['sName']], $makeLookNice);
		}
		return $out;
	}
	
	private function makeFieldValueLookNice ($sType, $mValue, $bForEditing)
	{
		if (is_array ($mValue))
		{
			foreach ($mValue as $k => $v)
			{
				$mValue[$k] = $this->doMakeFieldValueLookNice ($sType, $v, $bForEditing);
			}
		}
		else
		{
			$mValue = $this->doMakeFieldValueLookNice ($sType, $mValue, $bForEditing);
		}
		return $mValue;
	}
	
	private function doMakeFieldValueLookNice ($sType, $sValue, $makeLookNice)
	{
		if (!$makeLookNice && $sType != 'html')
		{
			return $sValue;
		}
	
		switch ($sType)
		{
			case 'date':
				$date = strtotime ($sValue);
				if ($date > 0)
				{
					return date ('d/m/Y', $date);
				}
				else
				{
					return null;
				}
			break;
		
			case 'html':
				return Core_Tools::output_html ($sValue);
			break;
	
			default:
				return $sValue;
			break;
		}
	}
	
	public function getCMSJson ()
	{
		// Check for action
		$sAction = $this->objCMS->getAction ();
		
		switch ($sAction)
		{
			case 'add':
				return $this->getAddItem ();
			break;
			
			case 'edit':
				return $this->getEditItem ();
			break;
			
			case 'remove':
				return $this->getRemoveItem ();
			break;
		}
	}
	
	private function getDataFromPost ()
	{
		// Fetch all required fields
		$fields = array ();
		foreach ($this->aFields as $v)
		{
			if (!$v['bTranslated'])
			{
				$fields['m_'.$v['sName']] = Core_Tools::getInput ('_POST', $v['sName'], 'varchar');
			}
		}
		return $fields;
	}
	
	private function getAddItem ()
	{	
		$db = Core_Database::__getInstance ();
	
		$fields = $this->getDataFromPost ();	
		$id = $db->insert ($this->sTableName, $fields);
		
		// Save meta2content
		$this->saveContentFromPost ($id);
		
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your item has been added.',
				'redirect' => $this->objCMS->getAdminUrl ('news', 'edit', $id),
				'listhtml' => $this->getRecordList_HTML (true)
			)
		);
	}
	
	private function getEditItem ()
	{
		$db = Core_Database::__getInstance ();
	
		$id = intval ($this->objCMS->getRecordId ());
		
		// Check if exist
		$chk = $db->select ($this->sTableName, array ('m_id'), "m_id = ".$id);
	
		if (count ($chk) == 1)
		{
			$fields = $this->getDataFromPost ();
			$db->update ($this->sTableName, $fields, "m_id = ".$id);
			
			$this->saveContentFromPost ($id);
		}
		
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your item has been edited.',
				'listhtml' => $this->getRecordList_HTML (true)
			)
		);
	}
	
	private function getRemoveItem ()
	{
		$db = Core_Database::__getInstance ();
		
		$id = intval ($this->objCMS->getRecordId ());
		
		$db->remove ($this->sTableName, "m_id = ".$id);
		$db->remove ('meta2content', "c_id = ".$id." AND m_type = '".$this->sTableName."'");
		
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your item has been removed.',
				'listhtml' => $this->getRecordList_HTML (true)
			)
		);
	}
	
	private function saveContentFromPost ($id)
	{
		$db = Core_Database::__getInstance ();

		// First: take all translatable fields
		$trans = array ();
		foreach ($this->aFields as $v)
		{
			if ($v['bTranslated'])
			{
				$trans[] = $v;
			}
		}
		
		foreach ($this->objCMS->getAllLanguages () as $langk => $lang)
		{
			$sText = "";
			$sTitle = "";
			foreach ($trans as $v)
			{
				if ($v['sName'] == 'title')
				{
					$sTitle = Core_Tools::getInput ('_POST', $v['sName'].'_'.$langk, 'varchar');
				}
				else
				{
					$sInput = Core_Tools::getInput ('_POST', $v['sName'].'_'.$langk, 'varchar');
				
					switch ($v['sType'])
					{
						case 'html':
							$sInput = Core_Tools::fixTinyMCEInput ($sInput);
						break;
					}
				
					// | is the special character here.
					$sText .= str_replace 
					(
						'|',
						'&#124;',
						$sInput
					) . '|';
				}
				$sText = substr ($sText, 0, -1);
			}
			
			$this->setMetaContent ($this->sTableName, $langk, $id, $sTitle, $sText);
		}
	}
	
	public function getPluginEditor ($id = null)
	{
		$id = $this->objCMS->getRecordId ();
	
		$page = new Core_Template ();
		
		$aContent = $this->getMetaContentFromTable ('page', $id, true);
		
		// Get all languages
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
		}
		
		$page->set ('title', $title);
		$page->set ('content_action', $this->objCMS->getAdminUrl ('pages', 'edit', $id, 'output=json'));
		
		// Option
		$page->set ('overview', $aContent['n_options']);
		
		return $page->parse ('modules/generic/plugineditor.phpt');
	}
	
	public function getEditorActions ($id = null)
	{
		$id = $this->objCMS->getRecordId ();
	
		$page = new Core_Template ();
		$page->set ('remove_url', $this->objCMS->getAdminUrl ('pages', 'delete', $id, 'output=json'));
		return $page->parse ('modules/generic/actions.phpt');
	}
	
	public function getEditorJSON ($id = null)
	{
		$id = intval ($this->objCMS->getRecordId ());
		
		$db = Core_Database::__getInstance ();
		
		// Option
		$option = Core_Tools::getInput ('_POST', 'overview_type', 'varchar');
		if ($option)
		{
			$db->update
			(
				'navigation',
				array
				(
					'n_options' => $option
				),
				"n_id = ".$id
			);
		}
		
		foreach ($this->objCMS->getAllLanguages () as $k => $v)
		{
			$sTitle = Core_Tools::getInput ('_POST', 'title_'.$k, 'varchar');
			$this->setMetaContent ('page', $k, $id, $sTitle, null);
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
	
	public function canHaveWidgets ()
	{
		return true;
	}
	
	public function showAdminTab ()
	{
		return true;
	}
	
	public function getNavigationRow ($page)
	{
		/*
				return array
				(
					'id' => $page['n_id'],
					'sName' => Core_Tools::output_varchar (!empty ($page['m_title']) ? $page['m_title'] : 'unnamed - '.$page['n_id']),
					'sUrl' => $this->objCMS->getUrl ($page['n_module'], 'view', $page['n_id']),
					'sEditUrl' => $this->objCMS->getAdminUrl ('pages', 'edit', $page['n_id']),
					'aChildren' => array (),
					'sClass' => ($page['n_module'] == 'page' ? 'page' : 'page_gear'),
					'sTarget' => '_self'
				);
		*/
		switch ($page['n_options'])
		{
			case 'list':
			case 'blog':
				$sAction = $page['n_options'];
			break;
			
			default:
				$sAction = 'list';
			break;
		}
		
		$row = parent::getNavigationRow ($page);
		$row['sUrl'] = $this->objCMS->getUrl ($page['n_module'], $sAction, null, 'cpid='.$page['n_id']);
		return $row;
	}
}
?>
