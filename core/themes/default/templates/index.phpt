<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

	<head>
		<?=$header?>
		<link href="<?=CMS_FULL_URL?>core/css/client.css" rel="stylesheet" type="text/css" />
	</head>
	
	<body>
		<div id="container">
		
			<div id="contentContainer">
			
				<h1><?=$objCMS->getSiteTitle()?></h1>
			
				<div id="menu">
					<?=$navigation['main']?>
				</div>
		
				<div id="content">
					<?=$content?>
				</div>
				
				<br style="clear: both;" />
			</div>
			
			<div id="footer">
				<?=$navigation['foot']?>
			</div>
		</div>
	</body>
	
</html>
