<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/init.php";

$user = $_SESSION['user'];
$users = User::getAll();

$user->block_user();
$user->send_message();

if(isset($_POST['see_more']))
{
	Home::see_more();
}

?>
<!doctype html>
<html lang="en">
	<?php include "head.php"; ?>
  <body>

  	<?php include "navbar.php"; ?>

	<div class="container">
	  	<div class="page-wrapper home-page">

	  		<div class='online-users'>

		  		<div class='row'>
		  			<?php
		  			foreach($users as $key => $user):
		  				$id = $user->id;

		  				$hashed_id = $hashids->encode($id);
						
		  				$image = $user->image;
		  				$work_as = strlen($user->work_as) > 0 ? $user->work_as : 'job is not defined';
		  				$login = $user->login;
		  				$class = $login == 1 ? 'active' : '';
		  				$username = strlen($user->username) > 15 ? substr($user->username,0, 12) . ' ...' : $user->username;

	  				?>
		  			<div class="col-sm-4 " id='<?php echo $key; ?>'>

				  		<div class="card mb-3 user" id='<?php echo $hashed_id; ?>' style="max-width: 540px">
						  <div class="row g-0">
						    <div class="col-md-4 image-container">
						      <img src="<?php echo $image == '' || $image == NULL ? '/images/user.jpg' : '/user_images/' . $image; ?>" class="img-fluid"
						      />
						    </div>
						    <div class="col-md-8">
						      <div class="card-body">
						        <h6 class="card-title">
						        	<a class='text-info text-decoration-none' href='/?view_profile=<?php echo $hashed_id; ?>' target='_blank'>
						        		<?php echo $username; ?>
						        	</a>
						    	</h6>
						        <p class="card-text <?php echo !strlen($user->work_as) > 0 ? 'text-muted' : '' ?>">
						        	<?php echo $work_as; ?>
						        </p>
						        <p class="card-text">
						          <small class="text-muted"><i class='fa fa-circle <?php echo $class; ?>'></i> Online</small>
						        </p>
						        <a class='btn btn-info btn-sm send-message' href='#'><i class='fa fa-comment'></i></a>
						        <a class='btn btn-danger btn-sm block-user' href='#'><i class='fa fa-eye-slash'></i></a>

						      </div>
						    </div>
						  </div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

	  	</div>
	</div>


    <!-- Footer -->
    
    <!-- scripts -->
    <?php include "js.php"; ?>

  </body>
</html>