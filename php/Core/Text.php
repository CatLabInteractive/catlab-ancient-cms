<?php

class Core_Text
{

	protected $cache;
	protected $root_dir;
	protected $backup = false;
	protected $inFile, $inSection;
	protected $tag;

	public static function __getInstance ()
	{
		static $in;
		if (empty ($in)) 
		{
			$in = new Core_Text ();
		}
		return $in;
	}

	public function __construct ($language = null, $baseText = 'en')
	{
		if ($baseText && defined ('CMS_SYSTEM_PATH'))
		{
			$this->backup = new Core_Text ($baseText, false);
		}
		
		// Take text
		if (isset ($language))
		{
			$this->root_dir = CMS_SYSTEM_PATH.'language/'.$language;
			$this->tag = $language;
		}
		
		elseif (defined ('CMS_SYSTEM_PATH') && defined ('LANGUAGE_TAG')) {
			$this->root_dir = CMS_SYSTEM_PATH.'language/'.LANGUAGE_TAG;
			$this->tag = LANGUAGE_TAG;
		}

		else 
		{
			echo 'Language directory not defined.';
			exit ();
		}

	}
	
	public function setLanguage ($language)
	{
		$this->root_dir = BASE_URL.LANGUAGE_DIR.$language;
		$this->tag = $language;
		
		// Remove cache
		$this->cache = array ();
	}
	
	public function setFile ($file)
	{
	
		$this->inFile = $file;
	
	}
	
	public function setSection ($section)
	{
	
		$this->inSection = $section;
	
	}

	public function get ($id, $section = null, $file = null, $alternative = null)
	{
		// Section & fill
		if (empty ($section))
		{
			$section = $this->inSection;
		}
		
		if (empty ($file))
		{
			$file = $this->inFile;
		}

		// Check if the file is loaded already
		if (empty ($this->cache[$file])) 
		{
			$this->load_file ($file);
		}

		// Check if the id exist
		if (empty ($this->cache[$file][$section][$id])) 
		{
			if ($this->backup && $this->backup->get ($id, $section, $file, false))
			{
				return $this->backup->get ($id, $section, $file, false);
			}
			elseif ($alternative === null) 
			{
				return 'Text Not Found: '.$id.' ('.$file.', '.$section.', '.$this->root_dir.')';
			}
			else 
			{
				return Core_Tools::output_varchar ($alternative);
			}
		}
		else {
			return $this->cache[$file][$section][$id];
		}
	}
	
	public function getSection ($section, $file)
	{
	
		// Check if the file is loaded already
		if (empty ($this->cache[$file]))
		{
			$this->load_file ($file);
		}
		
		$output = array ();
		
		if (!empty ($this->cache[$file][$section])) 
		{
			foreach ($this->cache[$file][$section] as $k => $v) 
			{
				if (!empty ($v)) {
					$output[$k] = $v;
				}
			}
		}
		return $output;
	}
	
	public function getFile ($inFile, $return = 'error')
	{
	
		$file = $this->root_dir.'/templates/'.$inFile.'.txt';
		
		if (is_readable ($file)) 
		{
			return file_get_contents ($file);
		}
		
		elseif ($this->backup && $this->backup->getFile ($inFile, false))
		{
			return $this->backup->getFile ($inFile);
		}
		
		else {
			if ($return === 'error')
			{
				return ('404: Unable to load file '.$file.'.');
			}
			
			else return $return;
		}
	}
	
	public function getTemplate ($file, $fields = array ())
	{
		return Core_Tools::putIntoText
		(
			$this->getFile ($file),
			$fields
		);
	}

	protected function load_file ($file)
	{
		$f = $this->root_dir.'/'.$file.'.ini';
		if (is_readable ($f)) {
			$this->cache[$file] = @parse_ini_file ($f, true);
		}
	}
	
	public function getClickto ($txt)
	{
		return Array
		(
			$this->get ('clickto1', 'main', 'main').' ',
			$this->get ('clickto2', 'main', 'main'),
			' '.$txt
		);
	}

	public function getLanguages ()
	{
		/*
		$o = array ();
		$dir = scandir (LANGUAGE_DIR);
		foreach ($dir as $file)
		{
			if ($file != '.' && $file != '..')
			{
				$o[] = $file;
			}
		}
		return $o;
		*/
		return array ('en', 'nl');
	}
	
	public function getCurrentLanguage ()
	{
		return $this->tag;
	}
}

?>
