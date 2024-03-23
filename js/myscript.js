
$(function(){
	const hashids = new Hashids('', 15) 

	$(document).on("click", "a.unblock-user", function(e){
		e.preventDefault();
		let $this = $(this);

		const demo = $(".demo");
		new Attention.Confirm({
			title: 'Unblock User Confirmation',
			content: 'You are unblocking this user, proceed ?',
			onConfirm: function(){
				
				let user_id = $this.parents("li").attr("id");
				let formData = new FormData();
				formData.append("unblocked_user_id", user_id);
				formData.append("unblock_user", 1);

				$.ajax({
					url: 'includes/profile.php',
					type: 'POST',
					dataType: "JSON",
					processData: false,
					contentType: false,
					data: formData,
					success: function(data)
					{
						if(data.success)
						{
							let unblocked_user = data.unblocked_user;
							$("#"+unblocked_user).remove();
						}
					}
				})
				
			}
		})

	});

	$(document).on("click", "a.block-user, .block-user-profile", function(e){
		e.preventDefault();
		let $this = $(this);

		const demo = $(".demo");
		new Attention.Confirm({
			title: 'Deletion Confirmation',
			content: 'You are not going to see this user again, proceed ?',
			onConfirm: function(){
				
				let user_id;
				if($this.attr("class").includes("block-user-profile"))
					user_id = window.location.href.split("view_profile=")[1];
				else
					user_id = $this.parents(".user").attr("id");

				let formData = new FormData();
				formData.append("blocked_user_id", user_id);
				formData.append("block_user", 1);

				$.ajax({
					url: 'includes/home.php',
					type: 'POST',
					dataType: "JSON",
					processData: false,
					contentType: false,
					data: formData,
					success: function(data)
					{
						if(data.success)
						{
							if($this.attr("class").includes("block-user-profile"))
							{
								window.location.href = "/?home=1";
							}else
							{
								let blocked_user = data.blocked_user;
								$("#"+blocked_user+".user").parent().remove();
							}
						}
					}
				})
				
			}
		})

	})
	$(document).on("click", "a.send-message, .send-message-profile", function(e){
		e.preventDefault();
		let $this = $(this);
		let user_id;
		
		if($this.attr("class").includes("send-message-profile"))
			user_id = window.location.href.split("view_profile=")[1];			
		else
			user_id = $this.parents(".user").attr("id");

		console.log(user_id);

		let formData = new FormData();
		formData.append("reciever_id", user_id);
		formData.append("send_message", 1);

		$.ajax({
			url: 'includes/home.php',
			type: 'POST',
			dataType: "JSON",
			processData: false,
			contentType: false,
			data: formData,
			success: function(data)
			{
				if(data.success)
				{
					let reciever_id = data.reciever_id;
					window.location.href = "/?chat=1&cid=" + reciever_id;
				}
			}
		})
	})

	let offset = 20;
	let end_of_users = false;

	$(window).on("scroll", function(){

		let percentage = 80;
		let percentage_scroll = ( ( percentage * $(document).height()) / 100 )

		if( $(window).data('ajax_in_progress') === true )
			return;

		if( $(window).scrollTop() + $(window).height() >= percentage_scroll)
		{
			$(window).data('ajax_in_progress', true);

			if(!end_of_users){
				let formData = new FormData();
				formData.append('offset', offset);
				formData.append('see_more', 1);

				$.ajax({
					url: 'includes/home.php',
					type: 'POST',
					dataType: "JSON",
					processData: false,
					contentType: false,
					data: formData,
					success: function(data)
					{
						if(data.success)
						{
							let users = data.users;
							offset += 20;

							if(users.length < 20)
								end_of_users = true;
							
							let html = "";
							for(let user of users)
							{
								let id = user.id;
								hashed_id = hashids.encode(id)

								let login = user.login;
								let login_class = login === 1 ? 'active' : '';
								let username = user.username;
								let email = user.email;
								let work_as = user.work_as;

								let work_as_class =  work_as === null ? 'text-muted' : '';

								let image = user.image;
								work_as = work_as === null ? 'job is not defined' : work_as;
								username = username.length > 15 ? username.substr(0, 12) + ' ...' : username;

								html += '<div class="col-sm-4 ">';
								html += '<div class="card mb-3 user" id="' + hashed_id + '" style="max-width: 540px">';
								html += '<div class="row g-0">';
								html += '<div class="col-md-4 image-container">';
								html += '<img src="/user_images/'+ image +'" alt="..." class="img-fluid" />';
								html += '</div>';
								html += '<div class="col-md-8">';
								html += '<div class="card-body">';
								html += '<h6 class="card-title">';
								html += '<a class="text-info text-decoration-none" href="/?view_profile='+hashed_id+'" target="_blank">';

								html += username;
								html += '</a>';

								html+= "</h6>";
								html += '<p class="card-text '+ work_as_class +'">';
								html += work_as;
								html += '</p>';
								html += '<p class="card-text">';
								html += '<small class="text-muted"><i class="fa fa-circle '+ login_class +'"></i> Online</small>';
								html += '</p>';
								html += '<a class="btn btn-info btn-sm send-message" href="#""><i class="fa fa-comment"></i></a> ';
								html += '<a class="btn btn-danger btn-sm block-user" href="#""><i class="fa fa-eye-slash"></i></a>';
								html += "</div>";
								html += "</div>";
								html += "</div>";
								html += "</div>";
								html += "</div>";
							}
							if(html.length > 0)
								$(".online-users > .row").append(html);
						}
						$(window).data('ajax_in_progress', false);
					}
				});
			}
		}
	})
	
	$(document).on("click", ".user_image", function(){
		$(this).next("form").find("input").trigger("click");
	})
	
	$(document).on("change", ".user_image_form input", function(e){
		let file = $(e.target).prop("files")[0];
		let formData = new FormData();
		formData.append('change_user_image', 1);
		formData.append('file', file);

		$.ajax({
			url: 'includes/profile.php',
			type: 'POST',
			dataType: "text",
			processData: false,
			contentType: false,
			data: formData,
			success: function(filename)
			{
				$(".user_image img").attr("src", '/user_images/' + filename);	
			}
		})
	})
})