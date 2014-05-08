<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Name       : FronzenAge
Description: A two-column, fixed-width template suitable for business sites and blogs.
Version    : 1.0
Released   : 20071108

-->
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?=$header?>
		<link href="<?=CMS_FULL_URL?>core/themes/frozen/style.css" rel="stylesheet" type="text/css" />
		
		<!--[if IE]>
		<style type="text/css">
		#sidebar #calendar {
			background-position: 0px 20px;
		}
		</style>
		<![endif]-->
	</head>
<body>

<div id="logo">
	<h1><a href="#"><?=$objCMS->getSiteTitle()?></a></h1>
	<!--<h2>By <a href="http://www.nodethirtythree.com/">NodeThirtyThree</a> + <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></h2>-->
</div>
<div id="menu">

	<?=$navigation['main']->drawLevel(1);?>
	
	<div id="search">
		<form method="get" action="">
			<fieldset>
			<input id="s" type="text" name="s" value="" />
			<input id="x" type="image" name="imageField" src="<?=CMS_FULL_URL?>core/themes/frozen/images/img10.jpg" />
			</fieldset>
		</form>
	</div>
</div>
<hr />

<div id="banner"><img src="<?=CMS_FULL_URL?>core/themes/frozen/images/img04.jpg" alt="" width="960" height="147" /></div>
<!-- start page -->
<div id="page">
	<!-- start content -->
	<div id="content">
		<div class="post">
			<div class="entry">
				<?=$content?>
			</div>
		</div>
	</div>
	<!-- end content -->
	<!-- start sidebar -->
	<div id="sidebar">
	
		<?php		
			$subnav = $navigation['main']->drawLevel(2, 'subnav');
			if (!empty ($subnav))
			{
				echo '<h2>'.$navigation['main']->getCurrentTitle (2).'</h2>';
				echo $subnav;
			}
		?>
	</div>
	<!-- end sidebar -->
</div>
<!-- end page -->
<div id="footer">
	<p class="legal">Copyright (c) 2007 Website Name. All rights reserved.</p>
	<p class="credit">Designed by <a href="http://www.nodethirtythree.com/">NodeThirtyThree</a> + <a href="http://www.freecsstemplates.org/">Free CSS Templates</a></p>
</div>
</body>
</html>
