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

<div class="language_content" id="language_content">
	<form method="post" action="<?=$content_action?>" onsubmit="return CMS.pages.doSaveContent(this);" id="content_form">
		<?php foreach ($list_languages as $v) { ?>
			<div class="content_<?=$v['id']?>" <?php if ($v['id'] != LANGUAGE_TAG) { ?>style="display: none;"<?php } ?>>

				<label for="pt_<?=$v['id']?>">Hyperlink Title <?=($v['name'])?>:</label>
				<input id="pt_<?=$v['id']?>" class="page_title" type="text" style="margin-bottom: 10px;" name="title_<?=$v['id']?>" value="<?=$title[$v['id']]?>" maxlength="255" />

				<label for="pg_<?=$v['id']?>">Hyperlink URL <?=($v['name'])?>:</label>
				<input id="pg_<?=$v['id']?>" class="page_title" type="text" name="surl_<?=$v['id']?>" value="<?=$url[$v['id']]?>" />
				
				<br style="margin-bottom: <?php echo ((count ($list_languages) - 4) * 25); ?>px;" />
			</div>	
		<?php } ?>
	</form>
</div>
