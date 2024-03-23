<?php

class Session
{
	public static function start()
	{
		if(! session_id())
		{
			session_start();
		}
	}
	public static function set($key, $value)
	{
		self::start();
		$_SESSION["$key"] = $value;
	}

	public static function is_set($key)
	{
		self::start();
		return isset($_SESSION["$key"]);
	}

	public static function get($key)
	{
		self::start();
		return $_SESSION["$key"];
	}
	public static function unset($key)
	{
		self::start();
		unset($_SESSION["$key"]);
	}
	public static function destroy()
	{
		self::start();
		session_destroy();
	}
	public static function redirect($location)
	{
		header("Location: $location");
		exit;
	}
}