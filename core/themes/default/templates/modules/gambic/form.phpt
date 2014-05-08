<form id="content_form" action="<?=$content_action?>">
	<div class="language_content" id="language_content">
		<label>Page:</label>
		
		<select style="margin-bottom: 10px;" name="overview_type">
			<?php if ($overview == 'login') { ?>
				<option value="login" selected="selected">Login</option>
				<option value="register">Register</option>
				<option value="play">Play</option>
			<?php } else if ($overview == 'register') { ?>
				<option value="login">Login</option>
				<option value="register" selected="selected">Register</option>
				<option value="play">Play</option>
			<?php } else { ?>
				<option value="login">Login</option>
				<option value="register">Register</option>
				<option value="play" selected="selected">Play</option>
			<?php } ?>
		</select>
	</div>
</form>
