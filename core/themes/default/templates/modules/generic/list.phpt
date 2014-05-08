<?php if (count ($records) > 0) { ?>
	<table style="width: 100%;">
		<tr>
			<?php 
			foreach ($list_table_headers as $v) 
			{ 
				switch ($v[1])
				{
					case 'date':
						echo '<th style="width: 180px;">'.$v[2].'</th>';
					break;
			
					case 'id':
						if ($isAdmin)
						{
							echo '<th style="width: 50px;">'.$v[2].'</th>';
						}
					break;
			
					default:
						echo '<th style="text-align: left;">'.$v[2].'</th>';
					break;
				}
			} 
			?>

			<?php if ($isAdmin) { ?>	
				<th style="width: 30px;">&nbsp;</th>
				<th style="width: 40px;">&nbsp;</th>
			<?php } ?>
		</tr>

		<?php foreach ($records as $v) { ?>
			<tr>
				<?php foreach ($list_table_headers as $k) 
				{
					$vv = $v[$k[0]];
					
					switch ($k[1])
					{
						case 'date':
							echo '<td style="text-align: center;">'.$vv.'</td>';
						break;
						
						case 'id':
							if ($isAdmin)
							{
								echo '<td style="text-align: center;">'.$vv.'</td>';
							}
						break;
						
						default:
							if ($k[0] == 'title' && !$isAdmin)
							{
								echo '<td><a href="'.$readUrl.$v['id'].'/">'.$vv.'</a></td>';
							}
							else
							{
								echo '<td>'.$vv.'</td>';
							}
						break;
					}
				} ?>
			
				<?php if ($isAdmin) { ?>
					<td><a href="<?=$editUrl.$v['id'].'/'?>" class="edit"> </a></td>
					<td><a href="javascript:void(0);" onclick="CMS.modulemanager.removeItem('<?=$removeUrl.$v['id'].'/?output=json'?>');" class="remove"> </a></td>
				<?php } ?>
			</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	<p class="false">No records yet.</p>
<?php } ?>
