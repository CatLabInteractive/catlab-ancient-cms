<div id="page_save_button" class="button">
	<a href="javascript:void(0);" onclick="CMS.pages.saveContent();">Save content</a>
</div>

<div id="page_delete_button" class="button">	
	<a href="<?=$remove_url?>" 
		onclick="return CMS.pages.deletePage(this.href, 'Bent u zeker dat u deze pagina wilt verwijderen?');"
	>Delete page</a>
</div>

<div id="widget_list">
	<h3>Widgets</h3>
	
	<ul>
		<li>
			<label>Player count</label>
			<select>
				<optgroup label="Page specific">
					<option>Show on this page</option>
					<option>Hide on this page</option>
				</optgroup>
				
				<optgroup label="General">
					<option>Show on all pages</option>
					<option>Hide on all pages</option>
				</optgroup>
			</select>
		</li>
	</ul>
</div>
