<div id="loginform">

	<h2><?=$login?></h2>
	
	<?php if (!empty ($sError)) { ?>
		<p class="false"><?=$sError?></p>
	<?php } ?>
	
	<p><?=$about?></p>

	<form method="post" action="<?=$submit_url?>" />
	
		<fieldset>
			<legend><?=$account?></legend>
			<ol>
				<li>
					<label for="login_email"><?=$email?>:</label>
					<input type="text" id="login_email" name="login_email" />
				</li>
			
				<li>
					<label for="login_email"><?=$password?>:</label>
					<input type="password" id="login_password" name="login_password" />
				</li>
			</ol>
		</fieldset>
		<p><button type="submit"><?=$submit?></button></p>
	
	</form>
</div>
