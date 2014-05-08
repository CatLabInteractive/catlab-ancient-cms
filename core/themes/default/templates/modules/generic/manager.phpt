<div id="modulemanager">
	<h2><?=$module_name?></h2>
	<ul id="mm_tablist">
		<li><a href="javascript:void(0);" id="mm_toggle_list">List</a></li>
		<?php if (isset ($editform)) { ?>
			<li><a class="active" href="javascript:void(0);" id="mm_toggle_edit">Edit</a></li>
			<li><a href="javascript:void(0);" id="mm_toggle_add">Add</a></li>
		<?php } else { ?>
			<li><a class="active" href="javascript:void(0);" id="mm_toggle_add">Add</a></li>
		<?php } ?>
	</ul>

	<div id="tabs_content">

		<div id="mm_content_list" style="display: none;">
			<?=$reclist?>
		</div>

		<?php if (isset ($editform)) { ?>
			<div id="mm_content_edit">
		
				<div class="language_actions">
					<div class="button save">
						<a onclick="CMS.modulemanager.submitContent($('form_edit'));" href="javascript:void(0);">Save Item</a>
					</div>
				</div>		
			
				<?=$editform?>
			</div>
			
			<div id="mm_content_add" style="display: none;">
		<?php } else {?>
			<div id="mm_content_add">
		<?php } ?>
		
		
			<div class="language_actions">
				<div class="button save">
					<a onclick="CMS.modulemanager.submitContent($('form_add'));" href="javascript:void(0);">Add Item</a>
				</div>
			</div>		
			
			<?=$addform?>
		</div>
	</div>
</div>
