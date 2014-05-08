<?php
class Logic_NavContainer extends Logic_Navigation
{
	private $sSlug = null;
	private $sTitle = null;
	private $objCMS = null;
	
	public function __construct ($objCMS, $slug, $title)
	{
		$this->sSlug = $slug;
		$this->sTitle = $title;
		$this->objCMS = $objCMS;
		
		$this->setParent (false, 1);
	}
	
	public function getTitle ()
	{
		return $this->sTitle;
	}
	
	public function getSlug ()
	{
		return $this->sSlug;
	}
	
	public function drawLevel ($level, $sSlugOverwrite = null)
	{
		if (empty ($sSlugOverwrite))
		{
			$sSlugOverwrite = $this->sSlug;
		}
		
		// Get the current page
		$curpage = $this->getCurrentPage ();
	
		if ($level == 1)
		{
			return $this->drawNavigationLevel ($sSlugOverwrite, $curpage);
		}
		else
		{		
			if ($curpage)
			{
				// Get the current page's parent
				$parent = $curpage->getParentOnLevel ($level);
		
				if ($parent)
				{
					return $parent->drawNavigationLevel ($sSlugOverwrite, $curpage);
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
	
	public function getCurrentTitle ($level)
	{
		if ($level == 1)
		{
			return false;
		}
		else
		{
			// Get the current page
			$curpage = $this->getCurrentPage ();
		
			if ($curpage)
			{
				// Get the current page's parent
				$parent = $curpage->getParentOnLevel ($level);
		
				if ($parent)
				{
					return $parent->getTitle ();
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
	
	public function getCurrentPage ()
	{
		if ($this->objCMS->getModuleName () == 'page')
		{
			$record = $this->objCMS->getRecordId ();
		}
		elseif ($this->objCMS->getModuleName () == 'home')
		{
			$module = $this->objCMS->getModule ();
			$record = $module->getHomepageRecordId ();
		}
		else
		{
			// First: check for "current page _GET value"
			$record = Core_Tools::getInput ('_GET', 'cpid', 'int', 0);
			if (!($record > 0))
			{
				// Load the last page from the session
				$record = Core_Tools::getInput ('_SESSION', 'curpage', 'int', 0);
			}
		}

		if ($record > 0)
		{
			$_SESSION['curpage'] = $record;
			return $this->getPageFromId ($record);
		}
		else
		{
			return false;
		}
	}
	

	
	public function __toString ()
	{
		return $this->drawNavigationUl ($this->sSlug, $this->getCurrentPage ());
	}
}
?>
