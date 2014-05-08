<h2>Pages</h2>

<div id="page_container">

	<div id="page_module">
		<form method="post" action="<?=$action_module?>">
			<label>This page contains:</label>
			<select name="pg_module" onchange="this.form.submit();">
				<?php foreach ($list_modules as $k => $v) { ?>
					<option value="<?=$v['id']?>" <?php echo $v['selected'] ? 'selected="selected"' : null; ?>>
						<?=$v['title']?>
					</option>
				<?php } ?>
			</select>
		</form>
	</div>

	<div id="tabs_content">
	
		<div class="language_actions">	
		
			<?=$editor_actions?>	

		</div>
		
		<!--
		<div class="nolanguage_container">
			Test
		</div>
		-->
	
		<div class="language_container">
		
			<?=$editor_content?>
		</div>
	</div>
	
</div>
