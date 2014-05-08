<?php if (isset ($pagelist_loc)) { ?>
	<div class="pagelist <?=$pagelist_loc?>">
<?php } else { ?>
	<div class="pagelist top">
<?php } ?>

	<?php if ($pagelist_curpage > 1) { ?>
		<span class="previous">
			<a href="<?=$pagelist_action?><?php echo ($pagelist_curpage - 1); ?>"><?=$this->getText ('previous', 'pagelist', 'main')?></a>
		</span>
		
		<?php if ($pagelist_curpage < $pagelist_end) { ?> | <?php } ?>
	<?php } ?>

	<!--
	<?php for ($i = $pagelist_start; $i <= $pagelist_end; $i ++) { ?>
		<span <?php if ($pagelist_curpage == $i) { echo 'class="active"'; } ?>>
			<a href="<?=$pagelist_action?><?=$i?>"><?=$i?></a>
		</span>
	<?php } ?>
	-->

	<?php if ($pagelist_curpage < $pagelist_end) { ?>
		<span class="next">
			<a href="<?=$pagelist_action?><?php echo ($pagelist_curpage + 1); ?>"><?=$this->getText ('next', 'pagelist', 'main')?></a>
		</span>
	<?php } ?>

</div>
