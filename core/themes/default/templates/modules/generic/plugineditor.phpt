<form id="editor_content" action="<?=$content_action?>">
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
		<label>Overview Type:</label>
		
		<select style="margin-bottom: 10px;" name="overview_type">
			<?php if ($overview == 'blog') { ?>
				<option value="blog" selected="selected">Blog</option>
				<option value="list">List</option>
			<?php } else { ?>
				<option value="blog">Blog</option>
				<option value="list" selected="selected">List</option>		
			<?php } ?>
		</select>
	
		<?php foreach ($list_languages as $v) { ?>
			<div class="content_<?=$v['id']?>" <?php if ($v['id'] != LANGUAGE_TAG) { ?>style="display: none;"<?php } ?>>
				<label for="pt_<?=$v['id']?>">Page Title <?=($v['name'])?>:</label>
				<input id="pt_<?=$v['id']?>" class="page_title" type="text" style="margin-bottom: 10px;" name="title_<?=$v['id']?>" value="<?=$title[$v['id']]?>" maxlength="255" />

			</div>
		<?php } ?>
	</div>
</form>
