<?php include "includes/chat_header.php"; ?>

<style type="text/css">


#register-container{
	width: 40%;
	margin: auto;
	margin-top: 15%;
}

#register-container h2
{
	color: #dedede;
}
#register-container form
{
	margin-top: 50px;
}

#register-container form
{
	margin-top: 50px;
}
#register-container form button { border-radius: 0; }
#register-container form input
{
	background: #fff;
	color: black;
	border-radius: 0;
	border-right: none;
	border-left: none;
	border-top: none;
}
#register-container form p 
{
	margin-top: 10px;
	font-size: 16px;
}
#register-container form p a
{
	color: #ababab;
}
</style>

<?php
	User::register();

	$token = bin2hex( random_bytes(32) );
	Session::set('token', $token);
	
?>

<div class="container">	
	<div id="register-container">
		<h2 class="text-center">Register</h2>

		<?php
			if( Session::is_set('register-error'))
			{
				echo "<div class='alert alert-danger message-hide'>" . Session::get('register-error') . '</div>';
				Session::set('register-error', null);
			}
		?>

		<form autocomplete="off" method="POST">
			<input name='token' type='hidden' value='<?php echo $token; ?>'>
			<div class="form-group">
				<input required type="text" name="username" class="form-control" placeholder="Username">
			</div>
			<div class="form-group">
				<input required type="email" name="email" class="form-control" placeholder="Email Address">
			</div>
			<div class="form-group">
				<input required type="password" name="password" class="form-control" placeholder="Password">
			</div>

			<div class="form-group">
				<input required type="password" name="password_confirmation" class="form-control" placeholder="Password Confirmation">
			</div>

			<button type="submit" class="btn btn-secondary form-control" name="register">Register</button>
			<p class='text-light'>Have an account <a href="/?login=1">sign in</a></p>
		</form>

	</div>

</div>