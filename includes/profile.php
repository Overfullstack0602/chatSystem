<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/init.php";

$user = Session::get('user');

$hidden_info = [];
$hidden_data = [];

if(isset($_GET['view_profile']) )
{
	$visitor = true;
	
	$users_collection = $user->get_user_profile();
	$hidden_info = [];
	foreach($users_collection as $user_data){
		$hidden_info[] = $user_data->data;
	}

	$user = $users_collection[0];

	$username = $user->username;
	$email = $user->email;
	$image = $user->image;
	$work_as = $user->work_as == "" ? "not found" : $user->work_as;
	$address = $user->address == "" ? "not found" : $user->address;
	$mobile = $user->mobile == "" ? "not found" : $user->mobile;
	$created_at = $user->created_at;
	$work_as = $user->work_as == "" ? "not found" : $user->work_as;
}
else
{
	if(isset($_POST['change_user_image']))
	{
		$user->updateImage();
	}

	$visitor = false;
	$user->update_profile();
	$user->update_hidden_data();
	$users_I_blocked = $user->get_blocked_user();
	$user->unblock_user();



	$user = $user->get();
	$hidden_data = [];
	foreach($user as $user_data){
		$hidden_data[] = $user_data->data;
	}

	$username = $user[0]->username;
	$image = $user[0]->image;
	$email = $user[0]->email;
	$work_as = $user[0]->work_as == "" ? "not found" : $user[0]->work_as;
	$address = $user[0]->address == "" ? "not found" : $user[0]->address;
	$mobile = $user[0]->mobile == "" ? "not found" : $user[0]->mobile;
	$created_at = $user[0]->created_at;
	$work_as = $user[0]->work_as == "" ? "not found" : $user[0]->work_as;

	
}
?>

<!doctype html>
<html lang="en">
	<?php include "head.php"; ?>
  <body>
  	<?php include "navbar.php"; ?>
	<div class="container">
	  	<div class="page-wrapper home-page">

	  		<div class='my-profile'>

	  			<div class="container">
				    <div class="main-body">

				          <div class="row gutters-sm">
				            <div class="col-md-4 mb-3">
				              <div class="card h-100">
				                <div class="card-body">
				                  <div class="d-flex flex-column align-items-center text-center">
				                    
				                  	<div class='<?php echo $visitor ? '' : 'user_image'; ?>'>

				                    	<img src="/user_images/<?php echo $image == '' || $image == NULL ? 'user.jpg' : $image; ?>" alt="Admin" class="rounded-circle" width="150" height='150'>

				                    	<?php if(! $visitor): ?>
				                    	<i class="fa fa-camera"></i>
					                    <?php endif; ?>

				                    </div>

				                    <?php if(! $visitor): ?>

				                    <form class='user_image_form'>
				                    	<input type='file' class='d-none' name='user_image'>
				                    </form>

				                	<?php endif; ?>

				                    <div class="mt-3">
				                      <h4><?php echo $username; ?></h4>
				                      <?php if(!in_array("work_as", $hidden_info)): ?>
				                      <p class="text-secondary mb-1 work-as"><?php echo $work_as; ?></p>
				                      <?php endif; ?>
				                      <?php if($visitor): ?>
				                      <button class="btn btn-outline-primary send-message-profile">Message</button>
				                      <button class="btn btn-outline-danger block-user-profile">Block</button>
				                   		<?php endif; ?>

				                    </div>
				                  </div>
				                </div>
				              </div>
				              
				            </div>
				            <div class="col-md-8">
				              <div class="card mb-3">
				                <div class="card-body">

			                	<?php if(!in_array("username", $hidden_info)): ?>


				                  <div class="row">
				                    <div class="col-sm-3">
				                      <h6 class="mb-0">Username 

				                      	<?php if(isset($_GET['profile'])): ?>
					                      	<?php if(in_array("username", $hidden_data)): ?>
					                      	<br><small class='text-info'>( not seen for visitors )</small>
					                      <?php endif; ?>
				                      	<?php endif; ?>

				                      </h6>
				                    </div>
				                    <div id='username' class="col-sm-7 text-secondary">
				                    <span><?php echo $username; ?></span>
				                    <input type='text' value='<?php echo $username; ?>' name='username' class='form-control d-none'>
				                    </div>
									<?php if(!$visitor): ?>
				                    <div class="col-sm-2 text-secondary username">
				                    	<a title='edit username' class='btn btn-info btn-sm edit'><i class="fa fa-edit"></i></a>
				                    	<a title='Not allawed for visitors' class='btn btn-danger btn-sm hide_info'><i class="fa fa-eye"></i></a>
				                  	</div>
				                  <?php endif; ?>
				                  </div>
				                  <hr>
				                  <?php endif; ?>


				                  <?php if(!in_array("email", $hidden_info)): ?>
				                  <div class="row">
				                    <div class="col-sm-3">
				                      <h6 class="mb-0">Email

				                      	<?php if(isset($_GET['profile'])): ?>
					                      	<?php if(in_array("email", $hidden_data)): ?>
					                      	<br><small class='text-info'>( not seen for visitors )</small>
					                      <?php endif; ?>
										<?php endif; ?>
				                      </h6>
				                    </div>
				                    <div id='email' class="col-sm-7 text-secondary">
				                    <span><?php echo $email; ?></span>
				                    <input type='text' value='<?php echo $email; ?>' name='email' class='form-control d-none'>
				                    </div>
				                    <?php if(!$visitor): ?>
				                    <div class="col-sm-2 text-secondary email">
				                    	<a title='edit email' class='btn btn-info btn-sm edit'><i class="fa fa-edit"></i></a>
				                    	<a title='Not allawed for visitors' class='btn btn-danger btn-sm hide_info'><i class="fa fa-eye"></i></a>
				                  	</div>
				                  	<?php endif; ?>
				                  </div>
				                  <hr>
				              <?php endif; ?>

				              <?php if(!in_array("mobile", $hidden_info)): ?>
				                  <div class="row">
				                    <div class="col-sm-3">
				                      <h6 class="mb-0">Mobile

									<?php if(isset($_GET['profile'])): ?>
				                      	<?php if(in_array("mobile", $hidden_data)): ?>
				                      	<br><small class='text-info'>( not seen for visitors )</small>
				                      <?php endif; ?>
									<?php endif; ?>

				                      </h6>
				                    </div>
				                    <div id='mobile' class="col-sm-7 text-secondary">
				                    <span><?php echo $mobile; ?></span>
				                    <input type='text' value='<?php echo $mobile; ?>' name='mobile' class='form-control d-none'>
				                    </div>
				                    <?php if(!$visitor): ?>
				                    <div class="col-sm-2 text-secondary mobile">
				                    	<a title='edit mobile' class='btn btn-info btn-sm edit'><i class="fa fa-edit"></i></a>
				                    	<a title='Not allawed for visitors' class='btn btn-danger btn-sm hide_info'><i class="fa fa-eye"></i></a>
				                  	</div>
				                  	<?php endif; ?>
				                  </div>
				                  <hr>

				                  <?php endif; ?>

				                  <?php if(!in_array("address", $hidden_info)): ?>
				                  <div class="row">
				                    <div class="col-sm-3">
				                      <h6 class="mb-0">Address
				                      	<?php if(isset($_GET['profile'])): ?>

					                      <?php if(in_array("address", $hidden_data)): ?>
					                      	<br><small class='text-info'>( not seen for visitors )</small>
					                      <?php endif; ?>

				                      <?php endif; ?>

				                  	</h6>
				                    </div>
				                    <div id='address' class="col-sm-7 text-secondary">
				                    <span><?php echo $address; ?></span>
				                    <input type='text' value='<?php echo $address; ?>' name='address' class='form-control d-none'>
				                    </div>
				                    <?php if(!$visitor): ?>
				                    <div class="col-sm-2 text-secondary address">
				                    	<a title='edit adderss' class='btn btn-info btn-sm edit'><i class="fa fa-edit"></i></a>
				                    	<a title='Not allawed for visitors' class='btn btn-danger btn-sm hide_info'><i class="fa fa-eye"></i></a>
				                  	</div>
				                  	<?php endif; ?>
				                  </div>
				                  <hr>
				                  <?php endif; ?>

				                  <?php if(!in_array("work_as", $hidden_info)): ?>
				                  <div class="row">
				                    <div class="col-sm-3">
				                      <h6 class="mb-0">Work as
				                      	<?php if(isset($_GET['profile'])): ?>
				                      	<?php if(in_array("work_as", $hidden_data)): ?>
				                      	<br><small class='text-info'>( not seen for visitors )</small>
				                      <?php endif; ?>

				                      <?php endif; ?>
				                      </h6>
				                    </div>
				                    <div id='work_as' class="col-sm-7 text-secondary">
				                    <span><?php echo $work_as; ?></span>
				                    <input type='text' value='<?php echo $work_as; ?>' name='work_as' class='form-control d-none'>
				                    </div>
				                    <?php if(!$visitor): ?>
				                    <div class="col-sm-2 text-secondary work_as">
				                    	<a title='edit work as' class='btn btn-info btn-sm edit'><i class="fa fa-edit"></i></a>
				                    	<a title='Not allawed for visitors' class='btn btn-danger btn-sm hide_info'><i class="fa fa-eye"></i></a>
				                  	</div>
									<?php endif; ?>
				                  </div>
				                  <?php endif; ?>

				                </div>
				              </div>
				              
				            </div>
				          </div>


				          <?php if(! $visitor): ?>
				          	<?php if(count($users_I_blocked) > 0): ?>

				          	<div class='row gutters-sm mt-4 mb-4'>
				          		<div class='col-sm-12'>
				          		
					          		<div class="card p-4">

					          			<h5>Users you've blocked</h5>
					          			    <ul class="list-group list-group-flush">
											<?php foreach($users_I_blocked as $user_I_blocked):
												$hash_id = $hashids->encode($user_I_blocked->id);
												?>

										        <li id='<?php echo $hash_id; ?>' class="list-group-item">
										        	<img width="40" src='/user_images/<?php echo $user_I_blocked->image; ?>' >
										        	<strong><?php echo $user_I_blocked->username; ?></strong>
										        	<a href='#' class='text-decoration-none text-info float-right unblock-user'>Unblock</a>
										        </li>

										    <?php endforeach; ?>
										    </ul>
										
										</div>
								</div>
				          	</div>

				          <?php endif; ?>
				          <?php endif; ?>

				        </div>
				    </div>

	  		</div>

	  	</div>
	</div>

    <!-- Footer -->
    
    <!-- scripts -->
    <?php include "js.php"; ?>

    <script>
    	$(document).on("click", ".edit", function(e){
    		e.preventDefault();
    		$(this).parent().prev().find("span").hide();
    		$(this).parent().prev().find("input").removeClass("d-none");
    		let val = $(this).parent().prev().find("input").val();
    		$(this).parent().prev().find("input").focus().val('').val(val);
    	});

    	$(document).on("click", "a.hide_info", function(e){
    		e.preventDefault();

    		let classes = $(this).parent().attr("class").split(" ");
    		let the_class = classes[classes.length - 1];

    		let _this = $(this);

    		let formData = new FormData();
    		formData.append('class', the_class);
    		formData.append('update_hidden_data', 1);

    		$.ajax({
    			url: '/includes/profile.php',
    			type: "POST",
    			dataType: "JSON",
    			processData: false,
    			contentType: false,
    			data: formData,
    			success: function(data)
    			{
    				console.log(data);

    				if(data.success)
    				{

    				}
    			}
    		});

    	})

    	$(document).on("focusout", "input", function(e){
    		let col = $(this).parent().attr("id");
    		let value = $(this).val();

    		let _this = $(this);

    		let formData = new FormData();
    		formData.append('col', col)
    		formData.append('value', value)
    		formData.append('update_profile', 1)

    		$.ajax({
    			url: '/includes/profile.php',
    			type: "POST",
    			dataType: "JSON",
    			processData: false,
    			contentType: false,
    			data: formData,
    			success: function(data)
    			{
    				if(data.success)
    				{
    					_this.parent().find("span").text(data.value);
    					_this.parent().find("span").show();
    					_this.addClass("d-none");

    					if(col == "work_as")
    					{
    						$(".work-as").text(value);
    					}
    				}
    				else
    				{
    					
    				}
    			}
    		})
    	});

    </script>

  </body>
</html>