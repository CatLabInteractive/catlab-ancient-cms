<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?=$header?>
	<link rel="stylesheet" type="text/css" href="<?=CMS_FULL_URL?>core/themes/natural-essence/style.css" media="screen"/>
	<title><?=$objCMS->getSiteTitle()?></title>
</head>

<body>

<div id="wrapper">
<div id="container">

<div class="title">
	
	<h1><a href="<?=$CMS_FULL_URL?>"><?=$objCMS->getSiteTitle()?></a></h1>

</div>

<div class="header"></div>

<div class="navigation">
	
	<?=$navigation['main']->drawLevel(1);?>

	<div class="clearer"></div>

</div>

<div class="main" id="two-columns">

	<div class="col2">

		<div class="left">

			<div class="content">

				<?=$content?>

			</div>
	
		</div>

		<div class="right">
			
			<div class="content">

				<?php		
					$subnav = $navigation['main']->drawLevel(2, 'subnav');
					if (!empty ($subnav))
					{
						echo '<ul><li>'.$navigation['main']->getCurrentTitle (2);
						echo $subnav;
						echo '</li></ul>';
					}
				?>

				<!--
				<h2>Something</h2>
				
				<ul class="block">
					<li><a href="index.html">pellentesque</a></li>
					<li><a href="index.html">sociis natoque</a></li>
					<li><a href="index.html">semper</a></li>
					<li><a href="index.html">convallis</a></li>
				</ul>

				<h2>Another thing</h2>

				<ul class="block">
					<li><a href="index.html">consequat molestie</a></li>
					<li><a href="index.html">sem justo</a></li>
					<li><a href="index.html">semper</a></li>
					<li><a href="index.html">sociis natoque</a></li>
				</ul>

				<h2>Third and last</h2>

				<ul class="block">
					<li><a href="index.html">sociis natoque</a></li>
					<li><a href="index.html">magna sed purus</a></li>
					<li><a href="index.html">tincidunt</a></li>
					<li><a href="index.html">consequat molestie</a></li>
				</ul>

				<h2>And some text</h2>

				<p>Donec ligula lorem, varius eget, semper eget, aliquet quis, lectus. Vestibulum ipsum nunc, aliquet quis, blandit ac, varius a, est.</p>
				-->
	
			</div>

		</div>

		<div class="clearer"></div>

	</div>

	<div class="bottom">

		<div class="left">
			<?=$navigation['foot']?>
		</div>

		<div class="right">

			
			
		</div>

		<div class="clearer"></div>

	</div>

	<div class="footer">
		
		<div class="left">
			&nbsp;
		</div>

		<div class="right">
			Design by <a href="http://arcsin.se/">Arcsin</a> <a href="http://templates.arcsin.se/">Web Templates</a>
		</div>

		<div class="clearer"></div>

	</div>

</div>

</div>
</div>

</body>
</html>
