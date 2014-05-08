<?php

function admin_drawNavigation ($container, $id = 'menu', $level = 0, $tabs = 0, $additional = false, $urlKey = 'sUrl')
{
	$t = "";
	for ($i = 0 - $tabs; $i < ($level*2); $i ++)
	{
		$t .= "\t";
	}
	echo $t . '<ul class="level'.($level+1).'">' . "\n";

	foreach ($container->getChildren () as $obj)
	{
		$v = $obj->getField ();
	
		echo $t . "\t" . '<li id="'.$id.'_'.$v['id'].'" class="'.(isset ($v['sClass']) ? $v['sClass'] : null ).'">' . "\n";
		echo $t . "\t\t" . '<a href="'.$v[$urlKey].'">'.$v['sName'] . "</a>\n";
		
		if ($additional)
		{
			$aReplaces = array ('{id}', '{editUrl}');
			$aValues = array ($v['id'], $v[$urlKey]);
		
			echo $t . "\t\t" . str_replace ($aReplaces, $aValues, $additional) . "\n";
		}
		
		//echo $t . "\t\t" . ' | <a href="javascript:void(0);" class="handler">Move</a>' . "\n";
		if ($obj->hasChildren ())
		{
			admin_drawNavigation ($obj, $id, $level + 1, $tabs, $additional, $urlKey);
		}

		echo $t . "\t" . '</li>' . "\n";
	}
	echo $t . '</ul>' . "\n";
}

?>
