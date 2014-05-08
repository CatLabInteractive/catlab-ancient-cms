<?php require_once ($template_path.'functions.php'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
    xmlns:foaf="http://xmlns.com/foaf/0.1/"
    xmlns:dc="http://purl.org/dc/elements/1.1/" xml:lang="en">

	<head>
	
		<?=$header?>
		
		<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/upload.js"></script>
		
		<!-- Module specific javascript -->
		<?php if (isset ($module_jsfile)) { ?>
			<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/<?=$module_jsfile?>.js"></script>
		<?php } ?>
		
		<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/common.js"></script>
		
		
		<!-- Javascript libraries -->
		<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/context-menu.js"></script>
		<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/dragtree.js"></script>
		
		<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/tiny_mce/tiny_mce.js"></script>
		
		<!-- CSS -->
		<link href="<?=CMS_FULL_URL?>core/css/admin.css" rel="stylesheet" type="text/css" />
		
		<!-- PLUGIN: Window -->
		<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/jswindow/window.js"></script>
		<link href="<?=CMS_FULL_URL?>core/javascript/jswindow/themes/alphacube.css" rel="stylesheet" type="text/css" />
		<link href="<?=CMS_FULL_URL?>core/javascript/jswindow/themes/default.css" rel="stylesheet" type="text/css" />
		
		<!-- Module specific css -->
		<?php if (isset ($module_cssfile)) { ?>
			<link href="<?=CMS_FULL_URL?>core/css/<?=$module_cssfile?>.css" rel="stylesheet" type="text/css" />
		<?php } ?>
		
	</head>
	
	<body>
		<!-- Container -->
		<div id="container" style="z-index: 0;">
			<h1>Admin panel</h1>
		
			<!-- Top navigation -->
			<!--<h2>Navigation</h2>-->
			
			<div id="topnavigation">
			<?php admin_drawNavigation ($navigation['main']); ?>
			</div>
			<!-- /Top navigation -->
		
			<!-- Content -->
			<div id="content">
				<?=$content?>
			</div>
			<!-- /Content -->
			
			<p style="clear: both; text-align: center; font-size: 10px; padding-top: 10px; font-family: Verdana;">
				Neuron CMS &copy; <a href="http://www.neuroninteractive.eu/">Neuron Interactive</a>
			</p>
			
		</div>
				
		<!-- /Container -->
	</body>
	
</html>
