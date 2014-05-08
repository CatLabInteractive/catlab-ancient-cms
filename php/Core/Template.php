<?php

class Core_Template
{

	private $values = array ();
	private $lists = array ();

	// Text function
	public function setTextSection ($sTextSection, $sTextFile = null)
	{
		$this->sTextSection = $sTextSection;
		
		if (isset ($sTextFile))
		{
			$this->sTextFile = $sTextFile;
		}
	}
	
	public function setTextFile ($sTextFile)
	{
		$this->sTextFile = $sTextFile;
	}
	
	// Intern function
	private function getText ($sKey, $sSection = null, $sFile = null)
	{
		if (!isset ($this->objText))
		{
			$this->objText = Core_Text::__getInstance ();
		}
		
		return $this->objText->get 
		(
			$sKey, 
			isset ($sSection) ? $sSection : $this->sTextSection, 
			isset ($sFile) ? $sFile : $this->sTextFile
		);
	}

	public function set ($var, $value, $overwrite = false, $first = false)
	{
		$this->setVariable ($var, $value, $overwrite, $first);
	}

	public function setVariable ($var, $value, $overwrite = false, $first = false)
	{
		if ($overwrite)
		{
			$this->values[$var] = $value;
		}
		
		else 
		{
			if (isset ($this->values[$var]))
			{
				if ($first)
				{
					$this->values[$var] = $value.$this->values[$var];
				}
				
				else 
				{
					$this->values[$var].= $value;
				}
			}
			
			else 
			{
				$this->values[$var] = $value;
			}
		}
	}
	
	public function addListValue ($var, $value)
	{
		$this->lists[$var][] = $value;
	}
	
	public function sortList ($list)
	{
		if (isset ($this->lists[$list]))
		{
			asort ($this->lists[$list]);
		}
	}

	public function parse ($template)
	{		
		/* Set static url adress */
		foreach ($this->values as $k => $v)
		{
			$$k = $v;
		}
		
		foreach ($this->lists as $k => $v)
		{
			$n = 'list_'.$k;
			$$n = $v;
		}

		ob_start ();
		
		$template_path1 = CMS_CLIENT_PATH.CMS_THEME_PATH.'templates/';
		$template_path2 = CMS_SYSTEM_PATH.'core/themes/'.CMS_THEME_PATH.'templates/';
		$template_path3 = CMS_SYSTEM_PATH.'core/themes/default/templates/';
		
		if (defined ('CMS_THEME_PATH') && is_readable ($template_path1.$template)) 
		{
			$template_path = $template_path1;
			include $template_path1.$template;
		}
		
		elseif (is_readable ($template_path2.$template))
		{
			$template_path = $template_path2;
			include $template_path2.$template;		
		}
		
		elseif (is_readable ($template_path3.$template)) 
		{
			$template_path = $template_path3;
			include $template_path3.$template;
		}
		
		else 
		{
			echo '<h1>Template not found</h1>';
			echo '<p>'.CMS_SYSTEM_PATH.'default/'.$template.'</p>';
		}
		
		$val = ob_get_contents();
		ob_end_clean();
		
		return $val;
	}
	
	public static function getThemeSettings ()
	{
		$url1 = CMS_CLIENT_PATH.CMS_THEME_PATH.'theme.ini';
		$url2 = CMS_SYSTEM_PATH.'core/themes/'.CMS_THEME_PATH.'theme.ini';
		
		if (file_exists ($url1))
		{
			$settings = parse_ini_file ($url1, true);
			if ($settings)
			{
				return $settings;
			}
			else
			{
				return false;
			}
		}
		elseif (file_exists ($url2))
		{
			$settings = parse_ini_file ($url2, true);
			if ($settings)
			{
				return $settings;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
?>
