<?php $this->setTextSection ('login', 'gambic'); ?>

<h2><?=$this->getText ('login'); ?></h2>

<p><?=$this->getText ('aboutPersona'); ?> <a href="http://www.catlab.be/en/news/read/12/"><?=$this->getText ('why'); ?></a></p>

<p style="margin-top: 20px; margin-bottom: 20px;">
	<a href="<?=$login_action?>" class="persona-button catlab-persona-login"><span><?=$this->getText ('signinPersona'); ?></span></a>
</p>


<h2>Ancient method</h2>

<?php if (isset ($error) && !empty ($error)) { ?>
	<p class="false"><?=$error?></p>
<?php } ?>

<p>... for people who like to remember passwords.</p>

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