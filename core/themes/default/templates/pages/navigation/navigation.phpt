<?php require_once ($template_path.'functions.php'); ?>

<script type="text/javascript">
	CMS.navigation.action = '<?=$submitAction?>';
	CMS.navigation.removeurl = '<?=$removeUrl?>';
</script>

<h2>Your navigation</h2>
<?php if (isset ($all_pages) && count ($all_pages) > 0) { ?>

<div>

<p>Drag & drop your items to change the navigation.</p>

<!-- Begin navigation -->
<ul id="navigation" class="dhtmlgoodies_tree">
<?php foreach ($all_pages as $page) { ?>	<li id="nav_<?=$page->getSlug ();?>" noSiblings="true" noDrag="true" <?php if ($page->getSlug () == 'draft') { ?>class="folder_wrench"<?php } ?>>
		<a href="#"><?=$page->getTitle ()?></a><?php 
			echo "\n"; 
			admin_drawNavigation 
			(
				$page, 
				'nv_', 
				0, 
				2, 
				'<a href="javascript:void(0);" onclick="CMS.navigation.doRemovePage(\'{id}\');" class="remove">&nbsp;</a>'.
				'<a href="{editUrl}" class="edit">&nbsp;</a>',
				'sEditUrl'
			); ?>
	</li>
<?php } ?>
</ul>
<!-- End navigation -->
</div>

<div id="navActions">

	<div id="nav_save_button" class="button">
		<a href="javascript:void(0);" onclick="CMS.navigation.doSaveTree();">Save navigation</a>
	</div>
	
	<div id="nav_add_button" class="button">
		<a href="<?=$addPageUrl?>">Add a page</a>
	</div>
	
	<div id="nav_addlink_button" class="button">
		<a href="<?=$addLinkUrl?>">Add a link</a>
	</div>
	
</div>

<br style="clear: both;" />
	
<?php } else { ?>
	<p>You don't have any pages yet.</p>
<?php } ?>
