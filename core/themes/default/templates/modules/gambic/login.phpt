<?php $this->setTextSection ('login', 'gambic'); ?>

<h2>Login</h2>

<?php if (isset ($error) && !empty ($error)) { ?>
	<p class="false"><?=$error?></p>
<?php } ?>

<form method="post" action="<?=$login_action?>">

	<fieldset>

		<ol>
			<li>
				<label for="login">Username or email address:</label>
				<input type="text" id="login" name="email" />
			</li>

			<li>
				<label for="password">Password:</label>
				<input type="password" id="password" name="password" />
			</li>

			<li>
				<button type="submit"><span>Login</span></button>
			</li>
		</ol>

	</fieldset>

</form>

<p>
	Don't have an account yet? <a href="<?=$register_url?>">Register here</a>.
</p>