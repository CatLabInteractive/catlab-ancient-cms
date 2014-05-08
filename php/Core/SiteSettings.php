<?php
class Core_SiteSettings
{
	private $aSettings;
	private $sCurLang = 'nl';
	
	public function __construct ($objCMS)
	{
		$db = Core_Database::__getInstance ();
		
		// Set language
		$this->sCurLang = $objCMS->getLanguage ();
		
		try
		{
			$l = $db->select
			(
				'sitesettings',
				array ('*')
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
			
			$l = array ();
		}
		
		// Load "default" settings
		$this->aSettings = $this->getDefaultSettings ();
		
		// Loop trough the settings from database
		foreach ($l as $v)
		{
			if (isset ($this->aSettings[$v['s_key']]))
			{
				if (is_array ($this->aSettings[$v['s_key']]))
				{
					$this->aSettings[$v['s_key']][$v['s_lang']] = $v['s_value'];
				}
				else
				{
					$this->aSettings[$v['s_key']] = $v['s_value'];
				}
			}
		}
	}
	
	/*
		If you need to append a "default value", you're probably doing
		something wrong. All default values should be defined within
		this class!
	*/
	public function getSetting ($key)
	{
		if (isset ($this->aSettings) && isset ($this->aSettings[$key]))
		{
			if (is_array ($this->aSettings[$key]))
			{
				$first = Core_Tools::getArrayFirstValue ($this->aSettings[$key]);
				return isset ($this->aSettings[$key][$this->sCurLang]) ? 
					$this->aSettings[$key][$this->sCurLang] : 
					$first[1];
				
			}
			else
			{
				return $this->aSettings[$key];
			}
		}
		else
		{
			return false;
		}
	}
	
	public function getLanguages ()
	{
		return array
		(
			'en' => 'English',
			'nl' => 'Dutch',
			'es' => 'Spanish'
		);
	}
	
	public function getDefaultSettings ()
	{
		$out = array ();
		
		$out['siteTitle'] = array
		(
			'en' => 'New Website Title',
			'nl' => 'Nieuwe Website Titel'
		);
		
		$out['upload_img_width'] = 800;
		$out['upload_img_height'] = 600;
		$out['upload_img_quality'] = 75;
		$out['upload_thumb_width'] = 100;
		$out['upload_thumb_height'] = 100;
		
		$out['google_analytics'] = false;
		
		return $out;
	}
}
?>
