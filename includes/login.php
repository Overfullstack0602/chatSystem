<?php include "includes/chat_header.php"; ?>

<style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');

body
{
	font-family: 'Lato', sans-serif;
}

#login-container
{
	width: 40%;
	margin: auto;
	margin-top: 15%;
}

#login-container h2
{
	color: #dedede;
}

#login-container form
{
	margin-top: 50px;
}

#login-container form button { border-radius: 0; }

#login-container form input
{
	background: #fff;
	color: black;
	border-radius: 0;
	border-right: none;
	border-left: none;
	border-top: none;
}

#login-container form p 
{
	margin-top: 10px;
	font-size: 16px;
}
#login-container form p a
{
	color: #ababab;
}

</style>

<?php
	User::login();
	
	$token = bin2hex( random_bytes(32) );
	Session::set('token', $token);
?>

<div class="container">	
	<div id="login-container">
		
		<h2 class="text-center">Login</h2>


		<?php
			if( Session::is_set('login-error'))
			{
				echo "<div class='alert alert-danger message-hide'>" . Session::get('login-error') . '</div>';
				Session::set('login-error', null);
			}
		?>

		<form autocomplete="off" method="POST">
			<input name='token' type='hidden' value='<?php echo $token; ?>'>
			<div class="form-group">
				<input required type="email" name="email" class="form-control" placeholder="Email Address">
			</div>
			<div class="form-group">
				<input required type="password" name="password" class="form-control" placeholder="Password">
			</div>
			<button type="submit" class="btn btn-secondary form-control" name="login">
				Login
			</button>
			
			<p class='text-light'>if you do not have an account <a href="/?register=1">create account</a></p>

		</form>

	</div>

</div>