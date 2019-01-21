<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/


if (!defined ('CMS_DEFAULT_LANG'))
	define ('CMS_DEFAULT_LANG', 'nl');
	
if (!defined ('CMS_DEFAULT_MODULE'))
	define ('CMS_DEFAULT_MODULE', 'home');

class CMS
{
	private $sLanguage = CMS_DEFAULT_LANG;
	private $sModule = CMS_DEFAULT_MODULE;
	private $iRecordId= 0;
	private $bIsCMS	= false;
	private $sAction = 'index';
	private $sOutput = 'html';
	
	private $objSettings = null;
	private $sSiteTitle = 'Undefined Site';

	/*
		Initialise all objects and fetch the right tools.
	*/
	public function __construct ()
	{
		// Set the input query
		if (isset ($_GET['p']))
		{
			$this->setInputQuery ($_GET['p']);
		}
		
		// Define language tag
		define ('LANGUAGE_TAG', $this->sLanguage);
		
		// Define some files
		define ('FILE_UPLOAD_DIR', CMS_CLIENT_PATH.'files/');
		define ('IMAGE_UPLOAD_DIR', FILE_UPLOAD_DIR.'images/');
		define ('IMAGE_THUMB_DIR', FILE_UPLOAD_DIR.'thumbs/');
		
		define ('FILE_UPLOAD_URL', CMS_FULL_URL.'files/');
		define ('IMAGE_UPLOAD_URL', FILE_UPLOAD_URL.'images/');
		define ('IMAGE_THUMB_URL', FILE_UPLOAD_URL.'thumbs/');
		
		$this->objSettings = new Core_SiteSettings ($this);
		$this->objBreadcrumb = new Logic_Breadcrumb ($this);
		
		$this->checkForPasstroughParameter ('ref');
		$this->checkForPasstroughParameter ('bonus');
	}
	
	private function checkForPasstroughParameter ($refn)
	{
		$ref = Core_Tools::getInput ('_GET', $refn, 'varchar', false);
		if ($ref)
		{
			$ref = urldecode ($ref);
		
			setcookie ('cms_ref_'.$refn, $ref, time() + 60*60*6, '/');
			$_COOKIE['cms_ref_'.$refn] = $ref;
		}
	}
	
	public static function getUrl ($sModule = null, $sAction = 'view', $iRecord = null, $sParameters = null, $miscdata = null)
	{
		$text = Core_Text::__getInstance ();
		
		if (empty ($sModule))
		{
			$sModule = CMS_DEFAULT_MODULE;
		}
		
		$slug = $text->getCurrentLanguage ().'/'.
			urlencode ($sModule).'/'.
			urlencode ($sAction).'/'.
			(!empty($iRecord) ? urlencode($iRecord).'/' : null ).
			(!empty ($miscdata) ? urlencode ($miscdata) . '/' : null)
		;
		
		if (CMS_NICE_URLS)
		{
			return CMS_FULL_URL.$slug.(!empty($sParameters) ? '?'.$sParameters : null);
		}
		else
		{
			return 'index.php?p=/'.$slug.(!empty($sParameters) ? '&'.$sParameters : null);
		}
	}
	
	public static function getAdminUrl ($sModule = 'navigation', $sAction = 'view', $iRecord = null, $sParameters = null)
	{
		$text = Core_Text::__getInstance ();
		
		if (empty ($sModule))
		{
			$sModule = '';
		}
		
		$slug = 'cms/'.$text->getCurrentLanguage ().'/'.
			urlencode($sModule).'/'.
			urlencode($sAction).'/'.
			(!empty($iRecord) ? urlencode($iRecord).'/' : null );
		
		if (CMS_NICE_URLS)
		{
			return CMS_FULL_URL.$slug.(!empty($sParameters) ? '?'.$sParameters : null);
		}
		else
		{
			return 'index.php?p=/'.$slug.(!empty($sParameters) ? '&'.$sParameters : null);
		}
	}
	
	/*
		Parse the input and set module & language
	*/
	private function setInputQuery ($sQuery)
	{
		$sInputs = explode ('/', $sQuery);
		
		$iKey = 0;
		
		// Check if CMS
		if (isset ($sInputs[1]) && $sInputs[1] == 'cms')
		{
			$this->bIsCMS = true;
			$iKey = 1;
		}

		if ($sInputs[1] == 'core')
		{
			$this->sModule = 'core';

			array_shift ($sInputs);
			array_shift ($sInputs);
			$this->sAction = implode ('/', $sInputs);

			$this->sOutput = 'html';

			return;
		}
		
		// Module stuff
		if (!empty ($sInputs[$iKey+1]) && array_key_exists ($sInputs[$iKey+1], $this->getAllLanguages ()))
		{
			$this->sLanguage = $sInputs[$iKey+1];
		}
		
		if (!empty ($sInputs[$iKey+2]))
		{
			$this->sModule = $sInputs[$iKey+2];
		}

		if (!empty ($sInputs[$iKey+3]))
		{
			$this->sAction = $sInputs[$iKey+3];
		}
		
		if (!empty ($sInputs[$iKey+4]))
		{
			$this->iRecordId = (int)$sInputs[$iKey+4];
		}
		
		// Output method
		if (isset ($_GET['output']))
		{
			$this->sOutput = $_GET['output'];
		}
	}
	
	/*
		Get module strong
	*/
	public function getModule ()
	{
		if ($this->bIsCMS)
		{
			// Check for login
			$login = Core_Login::__getInstance ();
			
			if ($login->isLogin () && $login->isAdmin ())
			{
				$classname = 'Pages_'.ucfirst($this->sModule);
			}
			else
			{
				$classname = 'Pages_Login';
			}
		}
		else
		{
			$classname = 'Modules_'.ucfirst($this->sModule);
		}
		
		if (class_exists ($classname))
		{
			return new $classname ($this);
		}
		else
		{
			/* Return home (or dashboard) */
			if ($this->bIsCMS)
			{
				// Check if this is a module
				if ($this->moduleExists ($this->sModule))
				{
					return new Pages_ModuleManager (Modules_Module::getModule ($this->sModule, $this), $this);
				}
				else
				{
					return new Pages_Navigation ($this);
				}
			}
			else
			{
				return new Modules_Home ($this);
			}
		}
	}
	
	public function getModuleName ()
	{
		return $this->sModule;
	}
	
	public function getAction ()
	{
		return $this->sAction;
	}

	public function getSubAction ()
	{
		return $this->iRecordId;
	}

	/********************************************
	* MODULES                                   *
	********************************************/
	/*
		Get (available) modules
	*/
	public function moduleExists ($sModule)
	{
		return in_array (ucfirst ($sModule), $this->getModuleList ());
	}
	
	/*
		Return all (available) modules
	*/
	public function getAllModules ()
	{
		$modules = array ('page', 'news', 'gallery', 'link', 'contactform', 'account');
		
		$o = array ();
		foreach ($modules as $k => $v)
		{
			$module = Modules_Module::getModule ($v, $this);
			if ($module)
			{
				$o[$v] = $module;
			}
		}
		
		return $o;
	}
	
	/*
		Returns an array of module names (string)
	*/
	public function getModuleList ()
	{
		$o = array ();
		foreach ($this->getAllModules () as $v)
		{
			$o[] = $v->getModuleName ();
		}
		return $o;
	}
	
	public function isAdmin ()
	{
		return $this->bIsCMS;
	}
	
	/*
		Return all (selected) languages
	*/
	public function getAllLanguages ()
	{
		return array
		(
			'en' => 'English',
			'nl' => 'Nederlands',
			'es' => 'Spaans'
		);
	}
	
	public function getRecordId ()
	{
		return (int)$this->iRecordId;
	}
	
	public function getSettings ()
	{
		return $this->objSettings;
	}
	
	/*
		Get the right text object
	*/
	public function getText ()
	{
		return Core_Text::__getInstance ($this->getLanguage ());
	}
	
	public function getLanguage ()
	{
		return $this->sLanguage;
	}
	
	public function getSiteTitle ()
	{
		return $this->sSiteTitle;
	}
	
	/*
		Returns the CMS HTML
	*/
	public function getOutput ()
	{	
		// Fetch the correct title
		if ($this->bIsCMS)
		{
			$this->sSiteTitle = '[CMS] '.$this->objSettings->getSetting ('siteTitle');
		}
		else
		{
			$this->sSiteTitle = $this->objSettings->getSetting ('siteTitle');
		}
		
		// Get the current module
		$module = $this->getModule ();
		
		if ($this->sOutput == 'json')
		{
			return $module->getJSON ();
		}
		else
		{
			return $module->getHTML ();
		}
	}
}