<?php

require_once "Database.php";

class Home 
{
	public static function see_more()
	{
		global $db;
		global $hashids;
		
		$respond = [];
		$respond['success'] = false;

		$offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT);
		
		$user_id = Session::get('id');
		$blocked_users = User::blocked_users();
		array_push($blocked_users, $user_id);
		$blocked_users_imploded = implode(", ", $blocked_users);

		$query = "SELECT * FROM users WHERE id NOT IN ($blocked_users_imploded) ORDER BY id DESC LIMIT $offset, 20";
		try
		{
			$users = $db->customQuery($query, []);
			$respond['users'] = $users;
			$respond['success'] = true;
			echo json_encode($respond);
			exit;
		}
		catch(PDOException $e)
		{
			echo json_encode($respond);
			exit;
		}
	}
}