<h2>Registration</h2>

<p>We'll just need a tiny bit more of information from you.</p>


<form method="post" action="<?=$play_url?>">
	<fieldset>

		<ol>
			<?php foreach ($requirements as $v) { ?>

				<li>

					<?php if ($v['type'] === 'text') { ?>
						<label for="requirements_<?=$v['name']?>"><?=$v['text']?></label>
						<input type="text" id="requirements_<?=$v['name']?>" name="requirements_<?=$v['name']?>" class="<?=$v['validation']?>" <?php if (isset ($v['value'])) { ?>value="<?=$v['value']?>"<?php } ?> />

						<?php if (isset ($v['response']) && $v['response']['status'] == 'error') { ?>
							<p class="false">
								<?=$v['response']['error']?>
							</p>
						<?php } else { ?>
							<p class="description">
								<?=$v['description']?>
							</p>
						<?php } ?>
					
					<?php } else if ($v['type'] === 'checkbox') { ?>

						<input type="checkbox" id="requirements_<?=$v['name']?>" name="requirements_<?=$v['name']?>" class="checkbox <?=$v['validation']?>" value="1" <?php if (strpos ($v['validation'], 'checked') !== false) { ?>checked="checked"<?php } ?> />
						<label for="requirements_<?=$v['name']?>" class="checkbox"><?=$v['description']?></label>

					<?php } ?>
					
				</li>

			<?php } ?>

			<li>
				<button type="submit" name="register" value="register"><span>Register</span></button>
			</li>
		</ol>
	</fieldset>
</form>