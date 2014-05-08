<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Pages_Navigation extends Pages_Admin
{
	public function getContent ()
	{
		$page = new Core_Template ();
		
		$page->set ('all_pages', $this->getAllPages ());
		$page->set ('submitAction', $this->objCMS->getAdminUrl ('navigation', 'edit', null, 'output=json'));
		$page->set ('removeUrl', urldecode ($this->objCMS->getAdminUrl ('navigation', 'remove', '{id}', 'output=json')));
		$page->set ('addPageUrl', $this->objCMS->getAdminUrl ('pages', 'add'));
		$page->set ('addLinkUrl', $this->objCMS->getAdminUrl ('pages', 'add', null, 'module=link'));
		
		return $page->parse ('pages/navigation/navigation.phpt');
	}
	
	public function getJSON ()
	{
		$action = $this->objCMS->getAction ();
		
		if ($action == 'edit')
		{
			if (isset ($_POST['nodelist']))
			{
				return $this->submitNavigation ($_POST['nodelist']);
			}
		}
		elseif ($action == 'remove')
		{
			return $this->getRemovePage ($this->objCMS->getRecordId ());
		}
	}
	
	private function getRemovePage ($id)
	{
		$db = Core_Database::__getInstance ();
		
		if (self::doRemovePage ($id))
		{
			return json_encode
			(
				array
				(
					'id' => $id,
					'status' => 'success',
					'message' => 'This page has been removed.'
				)
			);
		}
		else
		{
			return json_encode
			(
				array
				(
					'id' => $id,
					'status' => 'failed',
					'message' => 'The page that you are trying to remove, does not exist.'
				)
			);		
		}
	}
	
	private function submitNavigation ()
	{
		$db = Core_Database::__getInstance ();
		
		$sortId = 0;
		$nodes = explode (',', $_POST['nodelist']);
		
		$levels = array ();
		
		// Load all parents (for the levels)
		$id2parent = array ();
		$id2level = array ();
		
		$parents = $db->select
		(
			'navigation',
			array ('n_id', 'n_p_id')
		);
		
		foreach ($parents as $v)
		{
			$id2parent[$v['n_id']] = $v['n_p_id'];
		}
		
		// Loop trough the nodes and update the navigation
		foreach ($nodes as $v)
		{
			$stat = explode ('-', $v);
			$stat[0] = intval($stat[0]);
			
			if (isset ($id2parent[$stat[0]]) && count ($stat) == 2)
			{
				$sortId ++;
				
				if (intval ($stat[1]) > 0 && isset ($id2parent[$stat[1]]))
				{
					// Calculate the level
					if (!isset ($id2level[$id2parent[$stat[0]]]))
					{
						$id2level[$id2parent[$stat[0]]] = 0;
					}
					
					$id2level[$stat[0]] = $id2level[$id2parent[$stat[0]]] + 1;
				
					// Update parent id
					$db->update
					(
						'navigation',
						array
						(
							'n_p_id' => intval ($stat[1]),
							'n_container' => null,
							'n_level' => $id2level[$stat[0]],
							'n_sorting' => $sortId
						),
						"n_id = '".$stat[0]."'"
					);
				}
				elseif (!empty ($stat[1]))
				{
					// Update container
					$db->update
					(
						'navigation',
						array
						(
							'n_p_id' => 0,
							'n_container' => $db->escape ($stat[1]),
							'n_sorting' => $sortId,
							'n_level' => 0
						),
						"n_id = '".$stat[0]."'"
					);
				}
			}
		}
		
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your navigation order has been saved.'
			)
		);
	}
	
	/*
		Removes a page and all it's content
	*/
	public static function doRemovePage ($id)
	{
		$db = Core_Database::__getInstance ();
		
		$id = intval ($id);
		
		if ($id > 0)
		{
			$affected = $db->remove
			(
				'navigation',
				"n_id = '".$id."'"
			);
		
			$db->remove
			(
				'meta2content',
				"m_type = 'page' && c_id = '".$id."'"
			);
			
			return $affected > 0;
		}
		
		else
		{
			return false;
		}
	}
}
?>
