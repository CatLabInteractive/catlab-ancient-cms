<?php
class Logic_Navigation
{
	private $aChildren = array ();
	private $aData = array ();
	
	private $oParent = false;
	private $level = 1;

	public function __construct ($data)
	{
		$this->aData = $data;
	}
	
	public function setParent ($parent, $level)
	{
		$this->oParent = $parent;
		$this->level = $level;
	}
	
	public function getParent ()
	{
		return $this->oParent;
	}
	
	public function getParentOnLevel ($level)
	{
		if ($this->getLevel () == $level)
		{
			return $this;
		}
		elseif (!is_object ($this->oParent))
		{
			return false;
		}
		else
		{
			return $this->oParent->getParentOnLevel ($level);
		}
	}
	
	public function appendChild ($child)
	{
		$child->setParent ($this, $this->getLevel () + 1);
		$this->aChildren[] = $child;
	}
	
	public function isEmpty ()
	{
		return $this->aData['bShow'] == false;
	}
	
	public function getField ()
	{
		return $this->aData;
	}
	
	public function getChildren ()
	{
		return $this->aChildren;
	}
	
	/*
		Check if $page is a child of this.
	*/
	public function isChild ($page)
	{
		if ($this->equals ($page))
			return true;
		
		foreach ($this->getChildren () as $v)
		{
			if ($v->isChild ($page))
				return true;
		}
		
		return false;
	}
	
	public function hasChildren ()
	{
		return count ($this->aChildren) > 0;
	}
	
	public function getId ()
	{
		return $this->aData['id'];
	}
	
	public function getLevel ()
	{
		return $this->level;
	}
	
	public function getTitle ()
	{
		return $this->aData['sName'];
	}
	
	public function equals ($objPage)
	{
		return $this->getId () == $objPage->getId ();
	}
	
	public function __toString ()
	{
		return $this->drawNavigationUl ();
	}
	
	public function drawNavigationUl ($id = null, $curpage = null)
	{
		if (!$this->hasChildren ())
		{
			return '';
		}
		
		$out = '';

		$tabs = 0;
		$level = 1;
		
		$t = "";
		for ($i = 0 - $tabs; $i < ($level*2); $i ++)
		{
			$t .= "\t";
		}
		$out .= $t . '<ul class="level'.($level+1).'" '.(!empty ($id) ? 'id="'.$id.'"' : null).'>' . "\n";

		$out .= $this->drawNavigationLis ($id, $curpage);

		$out .= $t . '</ul>' . "\n";
		
		return $out;
	}

	public function drawNavigationLis ($id = null, $curpage = null)
	{
		$first = true;
		$out = '';
		$t = "";

		foreach ($this->getChildren () as $obj)
		{
			$classname = '';
			if ($curpage && $obj->isChild ($curpage))
			{
				$classname = ' active';
			}

			$v = $obj->getField ();
			if ($first)
			{
				$out .= $t . "\t" . '<li id="'.$id.''.$v['id'].'" class="'.$v['sClass'].' first">' . "\n";
				$first = false;
			}
			else
			{
				$out .= $t . "\t" . '<li id="'.$id.''.$v['id'].'" class="'.$v['sClass'].'">' . "\n";
			}

			$out .= $t . "\t\t" . '<a href="'.$v['sUrl'].'" title="'.$v['sName'].'" target="'.$v['sTarget'].'" class="'.$v['sTargetLoc'].$classname.'">'.$v['sName'] . "</a>\n";

			//echo $t . "\t\t" . ' | <a href="javascript:void(0);" class="handler">Move</a>' . "\n";
			if ($obj->hasChildren ())
			{
				$out .= $obj;
			}

			$out .= $t . "\t" . '</li>' . "\n";
		}

		return $out;
	}
	
	/*
		Based on level and current selection
	*/
	public function drawNavigationLevel ($id, $curpage = null)
	{
		if (!$this->hasChildren ())
		{
			return null;
		}
	
		$out = '';

		$tabs = 0;
		$level = 1;
		
		$t = "";
		for ($i = 0 - $tabs; $i < ($level*2); $i ++)
		{
			$t .= "\t";
		}
		$out .= $t . '<ul class="level'.($level+1).'" '.(!empty ($id) ? 'id="'.$id.'"' : null).'>' . "\n";
	
		$first = true;
		
		foreach ($this->getChildren () as $obj)
		{
			$classname = '';
			if ($curpage && $obj->isChild ($curpage))
			{
				$classname = ' active';
			}
		
			$v = $obj->getField ();
			if ($level == 0 && $first)
			{
				$out .= $t . "\t" . '<li id="'.$id.''.$v['id'].'" class="'.$v['sClass'].' first">' . "\n";
				$first = false;
			}
			else
			{
				$out .= $t . "\t" . '<li id="'.$id.''.$v['id'].'" class="'.$v['sClass'].'">' . "\n";
			}
			
			$out .= $t . "\t\t" . '<a href="'.$v['sUrl'].'" title="'.$v['sName'].'" target="'.$v['sTarget'].'" class="'.$v['sTargetLoc'].$classname.'">'.$v['sName'] . "</a>\n";
			$out .= $t . "\t" . '</li>' . "\n";
		}
		$out .= $t . '</ul>' . "\n";
		
		return $out;
	}
	
	protected function getPageFromId ($id)
	{
		// Just loop until found
		foreach ($this->getChildren () as $v)
		{
			if ($v->getId () == $id)
			{
				return $v;
			}

			$check = $v->getPageFromId ($id);
			if ($check)
			{
				return $check;
			}
		}
		
		return false;
	}
}
?>
