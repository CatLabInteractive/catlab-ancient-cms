<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="<?=CMS_FULL_URL?>core/css/upload.css" rel="stylesheet" type="text/css" />
	<link href="<?=CMS_FULL_URL?>core/css/common.css" rel="stylesheet" type="text/css" />
	
	<?php echo $header; ?>
	
	<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/upload.js"></script>
	
</head>
<body>

	<div id="tabs">
		<ul>
		
			<?php foreach (array ('gallery', 'upload') as $v) { ?>
			
				<li>
					<a id="upload_tab_<?=$v?>" class="upload_tabs <?php if ($v == $action) { ?>active<?php } ?>" href="javascript:void(0);">
						<?=$$v?>
					</a>
				</li>
			
			<?php } ?>
		</ul>
	</div>
	
	<div id="panel_wrapper" >

		<?php if (isset ($success)) { ?>
			<p class="true">
				<?=$success?>
			</p>
		<?php } ?>
		
		<?php if (isset ($error)) { ?>
			<p class="false">
				<?=$error?>
			</p>
		<?php } ?>

		<div id="upload_content_gallery" class="panel" style="<?php if ($action == 'gallery') { ?>display: block;<?php } else { ?>display: none; <?php } ?>">
		
		
			<div id="gallery_content">
				<?php foreach ($list_images as $v) { ?>
					<div class="image">
						<a href="javascript:void(0);" onclick="parent.CMS.upload.insertImageIntoEditor('<?=$v['thumbnail_url']?>', '<?=$v['file_url']?>');" style="background-image:url('<?=$v['thumbnail_url']?>');">
							<img src="<?=$v['thumbnail_url']?>" />
						</a>
					</div>
				<?php } ?>
				
				<br style="clear: both;">
			</div>
		</div>

		<div id="upload_content_upload" class="panel" style="<?php if ($action == 'upload') { ?>display: block;<?php } else { ?>display: none; <?php } ?>">
		
			<form method="post" enctype="multipart/form-data" action="<?=$imgupload_url?>">
				<fieldset>
					<legend>Choose an image to upload</legend>
					
					<div>
						<label for="imageFile">Image file:</label>
						<input type="file" id="imageFile" name="imageFile" />
					</div>
					
					<div>
						<label for="doResize" class="checkbox">Make this image web-safe (resize).</label>
						<input type="checkbox" id="doResize" name="doResize" class="checkbox" checked="checked" value="yes" />
					</div>
					
					<div>
						<label for="forceThumbSize" class="checkbox">Force thumbsize and allow whitespace.</label>
						<input type="checkbox" id="forceThumbSize" name="forceThumbSize" class="checkbox" value="yes" />
					</div>
					
					<button type="submit">Upload image</button>
					
				</fieldset>
			</form>
		</div>
	</div>
	
	<div class="mceActionPanel">
		<div style="float: left; display: none;">
			<input type="button" id="insert" name="insert" value="Insert" onclick="ExampleDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="Cancel" onclick="top.CMS.upload.closeManager();" />
		</div>
	</div>

</body>
</html>
