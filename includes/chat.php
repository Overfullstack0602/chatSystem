<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/classes/User.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/classes/Message.php";

	Message::sendMessage();

	$prev_chats = User::get_prev_chats();
	$user_id = $_SESSION['id'];

	if(isset($_GET['cid']) && $_GET['cid'] !== '')
	{
		$cid = filter_input(INPUT_GET, 'cid', FILTER_SANITIZE_STRING);
		$cid = $hashids->decode($cid)[0];
		if($user_id !== $cid)
		{
			$messages = User::get_chat($cid)[1];
			$last_chat = User::get_chat($cid)[0];
			Session::set('user_chat_id', $last_chat->id);
			
			Message::mark_as_seen($last_chat->id, $user_id);
		}
		else { Session::redirect("/?chat=1"); }
	}
	else
	{
		$last_chat = User::get_last_chat();
		$messages = $last_chat[1];
		$last_chat = $last_chat[0];

		Message::mark_as_seen($last_chat->id, $user_id);

		Session::set('user_chat_id', $last_chat->id);
	}
	$last_chat_id = $last_chat->id;
	$query = "SELECT count(*) as c FROM messages WHERE ( sender_id = ? AND receiver_id = ? ) OR ( sender_id = ? AND receiver_id = ? ) ";

	$values = [$last_chat_id, $user_id, $user_id, $last_chat_id];
	$messages_count = $db->customQuery($query, $values)[0]->c;
?>

<?php include "includes/chat_header.php"; ?>

<div class="container-fluid h-100">
	<div class="row justify-content-center h-100">
		<div class="col-md-4 col-xl-3 chat"><div class="card mb-sm-3 mb-md-0 contacts_card">
			<div class="card-header">
				<div class="input-group">
					<input type="text" placeholder="Search..." name="" class="form-control search">
					<div class="input-group-prepend">
						<span class="input-group-text search_btn"><i class="fas fa-search"></i></span>
					</div>
				</div>
			</div>
			<div class="card-body contacts_body">
				<ul id='contact-list' class="contacts">
					
					<?php foreach($prev_chats as $prev_chat):
						$hash = $hashids->encode($prev_chat->id);
					?>

					<li id='chat_<?php echo $hash; ?>' class="<?php echo $prev_chat->id == $last_chat->id ? 'active' : ''; ?>">
						<div class="d-flex bd-highlight">
							<div class="img_cont">
								<img src="/user_images/<?php echo $prev_chat->image; ?>" class="rounded-circle user_img">
								<span class="online_icon offline"></span>
							</div>
							<div class="user_info">
								<span><?php echo $prev_chat->username; ?></span>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			<div class="card-footer"></div>
		</div></div>
		<div class="col-md-8 col-xl-6 chat">
			<div class="card">
				<div class="card-header msg_head">
					<div class="d-flex bd-highlight">
						<div class="img_cont">
							<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
							<span class="online_icon"></span>
						</div>
						<div class="user_info">
							<span>Chat with <?php echo $last_chat->username; ?></span>
							<p><?php echo $messages_count; ?> Messages</p>
						</div>
					</div>
					<span id="action_menu_btn"><i class="fas fa-ellipsis-v"></i></span>
					<div class="action_menu">
						<ul>
							<li><i class="fas fa-user-circle"></i> View profile</li>
							<li><i class="fas fa-users"></i> Add to close friends</li>
							<li><i class="fas fa-ban"></i> Block</li>
						</ul>
					</div>
				</div>

				<div id='<?php echo $user_id; ?>' class="card-body msg_card_body">
					<div>

						<?php foreach($messages as $key => $message): ?>

						<div class="d-flex <?php echo $message->sender_id !== $user_id ? 'justify-content-start' : 'justify-content-end'; ?> mb-4">

							<?php 
							if($message->sender_id !== $user_id)
							{ ?>

								<div class="img_cont_msg">
									<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg">
								</div>

								<div class="msg_cotainer">
									<?php echo $message->message; ?>
									<span class="msg_time">
										<?php
										$time = $message->datetime;
										echo date("m-d H:i");
										?>
									</span>
								</div>
							<?php 
							}
							else
							{ ?>

								<div class="msg_cotainer_send">
									<?php echo $message->message; ?>
									<span class="msg_time">
										<?php
										$time = $message->datetime;
										echo date("m-d H:i");

										?>
									</span>
								</div>

								<div class="img_cont_msg">
									<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg">
								</div>
								<?php
							}
							?>
							
						</div>

					<?php endforeach; ?>

					</div>
				</div>
				<div class="card-footer send_area">
					<div class="input-group">
						<div class="input-group-append">
							<span class="input-group-text attach_btn"><i class="fas fa-paperclip"></i></span>
						</div>
						<textarea id='send_input' name="" class="form-control type_msg" placeholder="Type your message..."></textarea>
						<div class="input-group-append">
							<span class="input-group-text send_btn"><i class="fas fa-location-arrow"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>