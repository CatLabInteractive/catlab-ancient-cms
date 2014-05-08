<h2>Register</h2>

<?php if (isset ($registrationError)) { ?>
	<p class="false"><?=$registrationError?></p>
<?php } ?>

<form method="post" action="<?=$register_action?>">

	<fieldset>

		<ol>
			<li>
				<label for="register_username">Username:</label>
				<input type="text" id="register_username" name="register_username" />
			</li>

			<li>
				<label for="register_email">Email address:</label>
				<input type="text" id="register_email" name="register_email" />
			</li>

			<li>
				<label for="register_password">Password:</label>
				<input type="password" id="register_password" name="register_password" />
			</li>

			<li>
				<label for="register_password-repeat">Repeat password:</label>
				<input type="password" id="register_password-repeat" name="register_password-repeat" />
			</li>

			<li>
				<button type="submit" name="register" value="register"><span>Register</span></button>
			</li>
		</ol>

	</fieldset>

</form>

<p>
	Already have an account? <a href="<?=$login_url?>">Login here</a>.
</p>