<div id="page_save_button" class="button">
	<a href="javascript:void(0);" onclick="CMS.pages.doSaveContent($('editor_content'));">Save content</a>
</div>

<div id="page_delete_button" class="button">	
	<a href="<?=$remove_url?>" 
		onclick="return CMS.pages.deletePage(this.href, 'Bent u zeker dat u deze pagina wilt verwijderen?');"
	>Delete page</a>
</div>
