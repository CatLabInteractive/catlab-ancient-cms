<?php echo '<?xml version="1.0" ?>'; ?>
<rss version="2.0">
  <channel>
    <title><?=$name?></title> 
    <link><?=CMS_FULL_URL?></link> 
	<?php if (isset ($list_records)) { ?>
		<?php foreach ($list_records as $v) { ?>
		    <item>
		      <title><![CDATA[<?=$v['title']?>]]></title> 
		      <link><?=$v['link']?></link> 
		      <description><![CDATA[<?=$v['description']?>]]></description> 
		      <pubDate><![CDATA[<?=$v['pubDate']?>]]></pubDate> 
		    </item>
		<?php } ?>
	<?php } ?>
  </channel>
</rss>
