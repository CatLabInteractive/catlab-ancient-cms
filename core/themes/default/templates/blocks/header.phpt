<title><?=$objCMS->getSiteTitle()?></title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-language" content="<?=$objText->getCurrentLanguage ();?>" /> 

<script type="text/javascript">
	var CMS = new Object ();
	CMS.settings =
	{
		'path_icons' : '<?=$path_icons?>',
		'path_static' : '<?=$path_static?>',
		'path_javascript' : '<?=$path_static?>javascript/',
		'url_upload' : '<?=$url_upload?>',
		'static_url' : '<?=CMS_FULL_URL?>',
		'language' : '<?=$objText->getCurrentLanguage ();?>'
	};
</script>

<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/prototype.js"></script>
<script src="<?=CMS_FULL_URL?>core/javascript/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/json.js"></script>
<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/formchecker.js"></script>

<?php

$googleAnalytics = $objSettings->getSetting ('google_analytics');

if (!empty ($googleAnalytics))
{
?>
	<!-- Google analytics code -->
	<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	var pageTracker = _gat._getTracker("<?=$googleAnalytics?>");
	pageTracker._initData();
	pageTracker._trackPageview();
	</script>
<?php
}
?>

<link href="<?=CMS_FULL_URL?>core/javascript/lightbox/css/lightbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=CMS_FULL_URL?>core/javascript/lightbox/js/lightbox.js"></script>

<?php if (isset ($rss_link)) { ?>
	<link href="<?=$rss_link?>" type="application/rss+xml" rel="alternate" title="<?=$objCMS->getSiteTitle()?>" />
<?php } ?>