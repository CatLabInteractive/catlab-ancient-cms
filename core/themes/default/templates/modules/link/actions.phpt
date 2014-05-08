<div id="page_actions">
	<ul>
		<li id="page_action_save"><a href="javascript:void(0);" onclick="CMS.pages.saveContent();">Save link</a></li>
		
		<li id="page_action_delete">
			<a href="<?=$remove_url?>" 
				onclick="return CMS.pages.deletePage(this.href, 'Bent u zeker dat u deze pagina wilt verwijderen?');"
			>Delete page</a>
		</li>
	</ul>
</div>
