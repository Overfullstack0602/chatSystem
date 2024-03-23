<?php 

require_once "Database.php";
require_once "Session.php";

class User
{
	// Global User Fields
	private static $fields = ['username', 'email', 'password', 'image'];
	private static $table_name = "users";
	
	private $id;
	private $username;
	private $email;
	public function __construct($id, $username, $email)
	{
		$this->id = $id;
		$this->username = $username;
		$this->email = $email;
	}
	public function get_username()
	{
		return $this->username;
	}
	public function get_email()
	{
		return $this->email;
	}
	public function get_id()
	{
		return $this->id;
	}

	public function update_profile()
	{
		if(isset($_POST['update_profile']))
		{
			global $db;

			$respond = [];
			$respond['success'] = false;
			$user_id = $this->id;

			$col = filter_input(INPUT_POST, 'col', FILTER_SANITIZE_STRING);
			$value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);

			$query = "UPDATE users SET $col = ? WHERE id = ?";

			try
			{
				$db->customQuery($query, [$value, $user_id], 'update');
				$respond['message'] = "Profile has been updated";
				$respond['success'] = true;
				$respond['value'] = $value;
			}
			catch(PDOException $e)
			{
				$respond['message'] = "Error, Try again.";
			}
			echo json_encode($respond);
			exit;
		}
	}	
	public function updateImage()
	{
		global $db;
		$user_id = Session::get('id');
		$folder_name = $_SERVER['DOCUMENT_ROOT'] . '/user_images/';
		$filename = time() . '_' . $_FILES['file']['name'];

		if(move_uploaded_file($_FILES['file']['tmp_name'], $folder_name . $filename))
		{
			if($db->update('users', ['image'], [$filename], 'id', $user_id))
			{
				echo $filename;
			}
		}
		exit;
	}
	
	public function update_hidden_data()
	{
		if(isset($_POST['update_hidden_data']))
		{
			global $db;

			$respond = [];
			$respond['success'] = false;
			$user_id = $this->id;
			$class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
			$query = "INSERT INTO user_blocked_data (user_id, data) VALUES (?, ?)";
			try
			{
				$db->customQuery($query, [$user_id, $class], 'insert');
				$respond['message'] = "Profile has been updated";
				$respond['success'] = true;
				$respond['class'] = $class;
			}
			catch(PDOException $e)
			{
				$respond['message'] = "Error, Try again.";
			}
			echo json_encode($respond);
			exit;
		}
	}

	// User Static Methods
	public static function create($values)
	{
		global $db;
		return $db->insert(self::$table_name, self::$fields, $values);
	}

	public static function update($columns, $values, $id)
	{
		global $db;
		return $db->update(self::$table_name, $columns, $values, "id", $id);
	}

	public static function delete($id)
	{
		global $db;
		return $db->delete(self::$table_name, "id", $id);
	}

	public function get()
	{
		global $db;
		$user_id = $this->id;
		$query = "SELECT * FROM users u LEFT JOIN user_blocked_data ubd ON u.id = ubd.user_id WHERE u.id = ?";
		return $db->customQuery($query, [$user_id]);
	}

	public function get_user_profile()
	{
		global $db;
		global $hashids;

		$user_id = Session::get('id');
		$view_profile = filter_input(INPUT_GET, 'view_profile', FILTER_SANITIZE_STRING);
		$view_profile = $hashids->decode($view_profile)[0];

		if($user_id === $view_profile)
		{
			header("Location: /?profile=1");
			exit;
		}

		if(in_array($view_profile, self::blocked_users())) {
			header("Location: /?page404=1");
			exit;
		}

		$user = $db->customQuery("SELECT * FROM users u LEFT JOIN user_blocked_data ubd ON u.id = ubd.user_id WHERE u.id = ?", [$view_profile]);

		return $user;
	}
	
	public static function register()
	{
		if( isset($_POST['register']) )
		{

			if(Session::get('token') != $_POST['token'])
			{
				Session::set('register-error', "Invalid token");
				Session::redirect("/?register=1");
			}
			
			$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$password = filter_input(INPUT_POST, 'password');
			$password_confirmation = filter_input(INPUT_POST, 'password_confirmation');

			$image = "user.jpg";

			if( strlen($username) < 5 || strlen($username) > 20 )
			{
				Session::set('register-error', "Username must be between 5 and 20 characters.");
			}
			else if( strlen($password) < 8 || strlen($password) > 20 )
			{
				Session::set('register-error', "Pasword must be between 8 and 20 characters.");
			}
			else if( $password !== $password_confirmation )
			{
				Session::set('register-error', "Pasword must be match confirmation.");
			}
			else
			{
				$hashed_password = password_hash($password, PASSWORD_DEFAULT);
				$values = array($username, $email, $hashed_password, $image);
				$user_id = self::create($values);
				if( $user_id )
				{
					Session::set('id', $user_id);
					Session::set('username', $username);
					Session::set('email', $email);

					$db->update(self::$table_name, ['login'], [1], 'id', $user_id);

					Session::set('user', new User($user_id, $username, $email));
					Session::redirect("/?home=1");
				}
				else 
				{
					Session::redirect("/?register=1");
				}
			}
			Session::redirect("/?register=1");
		}
	}

	public static function logout()
	{
		global $db;
		$user_id = Session::get('id');
		$db->update(self::$table_name, ['login'], [0], 'id', $user_id);

		Session::unset('id');
		Session::unset('username');
		Session::unset('email');
		Session::unset('user');
		
		session_destroy();
		Session::redirect("/?login=1");
	}

	public static function login()
	{
		if( isset($_POST['login']) )
		{
			if(Session::get('token') != $_POST['token'])
			{
				Session::set('login-error', "Invalid token");
				Session::redirect("/?login=1");
			}

			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$password = filter_input(INPUT_POST, 'password');

			global $db;

			$user = $db->get(self::$table_name, 'email', $email)[0];

			if( $user )
			{
				$hashed_password = $user->password;

				if( password_verify($password, $hashed_password) )
				{
					Session::set('id', $user->id);
					Session::set('username', $user->username);
					Session::set('email', $user->email);

					// updare user login status
					$db->update(self::$table_name, ['login'], [1], 'id', $user->id);
					Session::set('user', new User($user->id, $user->username, $user->email));
					Session::redirect("/?home=1");
				}
				else 
				{
					Session::set('login-error', "Your password is not correct!");
					Session::redirect("/?login=1");
				}
			}
			else
			{
				Session::set('login-error', "Your email is not correct!");
				Session::redirect("/?login=1");
			}
		}
	}

	public static function get_prev_chats()
	{
		global $db;
		$user_id = Session::get('id');
		$chats = $db->customQuery("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY id DESC",[$user_id, $user_id]);

		$ids = "";
		$last_chat_id = 0;

		foreach ($chats as $key => $chat) {
			if($key == 0) { 
				$last_chat_id = $chat->sender_id === $user_id ? $chat->receiver_id : $chat->sender_id;
			}

			$receiver_id = $chat->receiver_id;
			$sender_id = $chat->sender_id;

			$ids .= $receiver_id . ", " . $sender_id;
			if($key !== count($chats) - 1) { $ids .= ", "; }
		}
		$users = $db->customQuery("SELECT * FROM users WHERE id IN ($ids) AND id != $user_id ORDER BY id = $last_chat_id DESC", []);
		return $users;
	}

	public static function get_last_chat()
	{
		global $db;
		$user_id = Session::get('id');
		$values = [$user_id, $user_id];
		$result = $db->customQuery("SELECT * FROM messages WHERE sender_id = ? || receiver_id = ? ORDER BY id DESC LIMIT 1", $values)[0];

		$sender_id = $result->sender_id;
		$receiver_id = $result->receiver_id;

		if($sender_id === $user_id) {
			return [$db->customQuery("SELECT * FROM users WHERE id = ?", [$receiver_id])[0],
			$db->customQuery("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY id", [$receiver_id, $sender_id, $sender_id, $receiver_id])];
		}else 
		{
			return [$db->customQuery("SELECT * FROM users WHERE id = ? ", [$sender_id])[0],
			$db->customQuery("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY id", [$sender_id, $receiver_id, $receiver_id, $sender_id])];
		}
	}

	public static function get_chat($cid)
	{
		global $db;
		$user_id  = Session::get('id');

		return [$db->customQuery("SELECT * FROM users WHERE id = ?", [$cid])[0],
		$db->customQuery("SELECT * FROM messages WHERE ( sender_id = ? AND receiver_id = ? ) OR ( sender_id = ? AND receiver_id = ? ) ORDER BY id", [$cid, $user_id, $user_id, $cid])];
	}


	public static function blocked_users()
	{
		global $db;
		$result = [];

		$user_id = Session::get('id');
		$query = "SELECT * FROM blocked_users WHERE user_id = ?";
		$blocked_users_ids = $db->customQuery($query, [$user_id]);
		foreach($blocked_users_ids as $blocked_users_id)
		{
			$blocked_user_id = $blocked_users_id->blocked_user_id;
			$result[] = $blocked_user_id;
		}
		return $result;
	}

	public function get_blocked_user()
	{
		global $db;
		$ids = implode(",", self::blocked_users());
		if($ids == '') return [];

		$query = "SELECT * FROM users WHERE id IN ($ids) ORDER BY id DESC";
		
		return $db->customQuery($query, []);
	}

	public static function getAll()
	{
		global $db;
		$user_id = Session::get('id');

		$blocked_users = self::blocked_users();
		
		array_push($blocked_users, $user_id);

		$blocked_users_imploded = implode(", ", $blocked_users);

		$query = "SELECT * FROM users WHERE id NOT IN ($blocked_users_imploded) ORDER BY id DESC LIMIT 20";
		return $db->customQuery($query, []);
	}

	public function block_user()
	{
		global $db;
		global $hashids;
		$user_id = $this->id;

		if(isset($_POST['block_user']))
		{
			$respond = [];
			$respond['success'] = false;

			$encoded_blocked_user_id = filter_input(INPUT_POST, 'blocked_user_id', FILTER_SANITIZE_STRING);
			$blocked_user_id = $hashids->decode($encoded_blocked_user_id)[0];

			if($db->insert('blocked_users', ['user_id', 'blocked_user_id'], [$user_id, $blocked_user_id]))
			{
				$respond['success'] = true;
				$respond['blocked_user'] = $encoded_blocked_user_id;
			}
			echo json_encode($respond);
			exit;
		}
	}

	public function unblock_user()
	{
		global $db;
		global $hashids;
		$user_id = $this->id;

		if(isset($_POST['unblock_user']))
		{
			$respond = [];
			$respond['success'] = false;

			$hashed_unblocked_user_id = filter_input(INPUT_POST, 'unblocked_user_id', FILTER_SANITIZE_STRING);

			$unblocked_user_id = $hashids->decode($hashed_unblocked_user_id)[0];
			if($db->customQuery("DELETE FROM blocked_users WHERE user_id = ? && blocked_user_id = ?", [$user_id, $unblocked_user_id], 'delete'))
			{
				$respond['success'] = true;
				$respond['unblocked_user'] = $hashed_unblocked_user_id;
			}
			echo json_encode($respond);
			exit;
		}
	}

	public function send_message()
	{
		global $db;
		global $hashids;
		$user_id = $this->id;

		if(isset($_POST['send_message']))
		{
			$respond = [];
			$respond['success'] = false;

			$receiver_id = filter_input(INPUT_POST, 'reciever_id', FILTER_SANITIZE_STRING);
			$reciever_id = $hashids->decode($receiver_id)[0];
			
			$query = "SELECT count(*) as c FROM blocked_users WHERE ( user_id = ? AND blocked_user_id = ? ) || ( user_id = ? AND blocked_user_id = ? )";

			$count = $db->customQuery($query, [$user_id, $receiver_id, $receiver_id, $user_id])[0]->c;
			
			if($count == 0)
			{
				$respond['success'] = true;
				$respond['reciever_id'] = $receiver_id;
			}else { $respond['error'] = "This user is not allowed to you."; }
			echo json_encode($respond);
			exit;
		}
	}

	public static function count_not_seen_messages()
	{
		global $db;
		$user_id = Session::get('id');
		$query = "SELECT count(*) as c FROM messages WHERE seen = ? AND receiver_id = ?";
		$count = $db->customQuery($query, [0, $user_id])[0]->c;
		return $count;
	}
}