<h2>Lost password?</h2>
<p>Enter your email address and we'll send you a link to change your password.</p>

<form method="post" action="<?=$submit_url?>">

	<fieldset>

		<ol>

			<li>
				<label for="register_email">Email:</label>
				<input type="text" id="register_email" name="email" />

				<?php if (isset ($error)) { ?>
					<p class="error false"><?=$error?></p>
				<?php } ?>
			</li>

			<li>
				<button type="submit" name="register" value="register"><span>Recover password</span></button>
			</li>

		</ol>

	</fieldset>

</form>