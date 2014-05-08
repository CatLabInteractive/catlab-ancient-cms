<h2>Please complete your registration</h2>
<p>We need a bit more data in order to finalise your registration.</p>

<form method="post" action="<?=$play_url?>">

	<fieldset>

		<ol>

			<li>
				<label for="register_username">Username:</label>
				<input type="text" id="register_username" name="register_username" />

				<?php if (isset ($error)) { ?>
					<p class="error false"><?=$error?></p>
				<?php } ?>
			</li>

			<li>
				<button type="submit" name="register" value="register"><span>Register</span></button>
			</li>

		</ol>

	</fieldset>

</form>