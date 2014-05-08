<?php
if (!function_exists ('getFieldHtml')) {
	function getFieldHtml ($v, $lang = '')
	{
		if (!empty ($lang))
		{
			$value = $v['sValue'][$lang];
			$lang = '_'.$lang;
		}
		else
		{
			$value = $v['sValue'];
		}
	
		echo '<label for="'.$v['sName'].'">'.$v['sLabel'].': </label>';
		switch ($v['sType'])
		{
			case 'text':
			case 'date':
				echo '<input type="text" name="'.$v['sName'].$lang.'" value="'.$value.'" />';
			break;
			
			case 'html':
				echo '<textarea name="'.$v['sName'].$lang.'">'.$value.'</textarea>';
			break;
		}
	}
}
?>

<form id="form_<?=$id?>" action="<?=$action?>">

	<div class="nolanguage_container">
		<?php if (isset ($list_general)) { ?>
			<?php
				foreach ($list_general as $v)
				{
					getFieldHtml ($v);
				}
			?>
		<?php } ?>
	</div>
	
	<div class="language_container">
		<div class="language_toggles">
			<ol>
				<?php foreach ($list_languages as $v) { ?>
					<li>
						<a 
							href="javascript:void(0);" 
							class="toggle_<?=$v['id']?> <?php if ($v['id'] == LANGUAGE_TAG) { ?>active<?php } ?>" 
							title="<?=$v['name']?>"
						><?=strtoupper($v['id'])?></a>
					</li>
				<?php } ?>
			</ol>
		</div>
	
		<div class="language_content">
			<?php if (isset ($list_translated)) { ?>
				<?php foreach ($list_languages as $l) { ?>
					<div class="content_<?=$l['id']?>" <?php if ($l['id'] != LANGUAGE_TAG) { ?>style="display: none;"<?php } ?>>
						<?php
							foreach ($list_translated as $v)
							{
								getFieldHtml ($v, $l['id']);
							}
						?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</form>
