<form method="post" action="<?=$content_action?>" onsubmit="return CMS.pages.doSaveContent(this);" id="content_form">
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
		<?php foreach ($list_languages as $v) { ?>
			<div class="content_<?=$v['id']?>" <?php if ($v['id'] != LANGUAGE_TAG) { ?>style="display: none;"<?php } ?>>
				<label for="pt_<?=$v['id']?>">Page Title <?=($v['name'])?>:</label>
				<input id="pt_<?=$v['id']?>" class="page_title" type="text" style="margin-bottom: 10px;" name="title_<?=$v['id']?>" value="<?=$title[$v['id']]?>" maxlength="255" />

				<label for="pg_<?=$v['id']?>">Page Content <?=($v['name'])?>:</label>
				<textarea id="pg_<?=$v['id']?>" rows="10" cols="50" name="content_<?=$v['id']?>"><?=$content[$v['id']]?></textarea>
			</div>
		<?php } ?>
	</div>
</form>
