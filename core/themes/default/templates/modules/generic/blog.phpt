<?php if (isset ($list_records)) { ?>
	<?php foreach ($list_records as $v) { ?>
		<?=$v?>
	<?php } ?>
<?php } ?>

<?php include ($template_path.'blocks/pagelist.phpt'); ?>
