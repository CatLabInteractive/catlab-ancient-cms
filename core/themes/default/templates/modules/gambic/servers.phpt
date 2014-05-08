<?php $this->setTextSection ('selectServer', 'gambic'); ?>

<div class="select-server">
	<h2><?=$this->getText ('selectServer'); ?></h2>

	<p class="description"><?=$this->getText ('about'); ?></p>

	<ul class="serverlist">
		<?php foreach ($servers as $v) { ?>

			<li <?php if ($v['prefered']) { ?> class="prefered"<?php } ?>>
				<div class="name">
					<a href="<?=$v['play_url']?>"><?=$v['name']?></a> 

					<?php if ($v['prefered']) { ?>
						<span class="recommended"><?=$this->getText ('recommended'); ?></span>
					<?php } ?>
				</div>

				<div class="details">
					<?= Core_Tools::putIntoText ($this->getText ('players'), array ('players' => $v['players'])); ?>
				</div>

				<?php if (!empty ($v['description'])) { ?>
					<div><?=$v['description']?></div>
				<?php } ?>

			</li>	

		<?php } ?>
	</ul>

	<div class="clearer clear"></div>
</div>