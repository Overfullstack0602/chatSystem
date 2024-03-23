<?php

require_once "includes/init.php";

if(isset($_GET['login']))
{
	if(Session::is_set('id'))
	{
		Session::redirect("/?home=1");
	}
	else
	{
		$login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_NUMBER_INT);
		if($login)
		{
			include "includes/login.php";
		}
	}
}
else if(isset($_GET['logout']))
{
	$logout = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_NUMBER_INT);
	if($logout)
	{
		include "includes/logout.php";
	}
}
else if(isset($_GET['register']))
{
	if(Session::is_set('id'))
	{
		Session::redirect("/?home=1");
	}
	else
	{
		$register = filter_input(INPUT_GET, 'register', FILTER_SANITIZE_NUMBER_INT);
		if($register)
		{
			include "includes/register.php";
		}
	}
}
else if(isset($_GET['chat']))
{
	if(! Session::is_set('id'))
	{
		Session::redirect("/?login=1");
	}
	else
	{
		$chat = filter_input(INPUT_GET, 'chat', FILTER_SANITIZE_NUMBER_INT);
		if($chat)
		{
			include "includes/chat.php";
		}
	}
}
else if(isset($_GET['home']))
{
	if(! Session::is_set('id'))
	{
		Session::redirect("/?login=1");
	}
	else
	{
		$home = filter_input(INPUT_GET, 'home', FILTER_SANITIZE_NUMBER_INT);
		if($home)
		{
			include "includes/home.php";
		}
	}
}
else if(isset($_GET['profile']))
{
	if(! Session::is_set('id'))
	{
		Session::redirect("/?login=1");
	}
	else
	{
		$profile = filter_input(INPUT_GET, 'profile', FILTER_SANITIZE_NUMBER_INT);
		if($profile)
		{
			include "includes/profile.php";
		}
	}
}else if(isset($_GET['view_profile']))
{
	if(! Session::is_set('id'))
	{
		Session::redirect("/?login=1");
	}
	else
	{
		$view_profile = filter_input(INPUT_GET, 'view_profile', FILTER_SANITIZE_NUMBER_INT);
		if($view_profile)
		{
			include "includes/profile.php";
		}
	}
}

else if(isset($_GET['page404']))
{

	$page404 = filter_input(INPUT_GET, 'page404', FILTER_SANITIZE_NUMBER_INT);
	if($page404)
	{
		include "includes/404.php";
	}
}
else 
{
	if(Session::is_set('id'))
	{
		Session::redirect("/?home=1");
	}
	else
	{
		include "includes/login.php";
	}
}

if(isset($_GET['chat']))
{
	include "includes/chat_footer.php";
}
else
{
	include "includes/footer.php";
}