<?php 
	foreach ($data as $v) 
	{	
		switch ($v['sType'])
		{
			case 'text':
				if ($v['sName'] == 'title')
				{
					echo '<h2>'.$v['value'].'</h2>';
				}
				else
				{
					echo '<p>'.$v['value'].'</p>';
				}
			break;
			
			case 'html':
				echo $v['value'];
			break;
			
			case 'date':
				echo '<p class="date">'.$v['value'].'</p>';
			break;
			
			default:
				echo '<p>'.$v['value'].'</p>';
			break;
		}
	} 
?>

<br style="clear: both;" />
