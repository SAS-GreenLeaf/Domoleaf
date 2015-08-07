<?php  

include('profile-menu.php');

echo '<div class="col-xs-10 col-xs-offset-2">';
echo ' <title>'._('Profile').'</title>
		
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<fieldset class="center">
			<h2>'._('Personal informations').'</h2>
			<div class="control-group control-group-profile">
				<label class="control-label" for="lastname">'._('Last Name').'</label>
			<div class="input-group">
				<label for="username" class="input-group-addon">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</label>
				<input name="lastname" type="text" class="form-control" id="lastname" placeholder="'._('Enter your last name').'" value="'.$profilInfo->lastname.'">
			</div>
							
			</div>
			<div class="control-group control-group-profile">
				<label class="control-label" for="firstname">'._('First Name').'</label>
				<div class="input-group">
				<label for="username" class="input-group-addon">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</label>
				<input name="firstname" type="text" class="form-control" id="firstname" placeholder="'._('Enter your first name').'" value="'.$profilInfo->firstname.'">
			</div>
							
			</div>
			<div class="control-group control-group-profile">
				<label class="control-label" for="sexe">'._('Gender').'</label>
				<div class="controls">';
				if ($profilInfo->gender == 0){
					echo ' <input name="sexe" id="sexe-0" value="0" checked="checked" type="radio"> 
					'._('Male').'&nbsp;&nbsp;
					 <input name="sexe" id="sexe-1" value="1" type="radio"> 
					'._('Female');
				}
				else{
					echo ' <input name="sexe" id="sexe-0" value="0" type="radio"> 
					'._('Male').'&nbsp;&nbsp;
					 <input name="sexe" id="sexe-1" value="1" type="radio" checked="checked" > 
					'._('Female');
				}
				echo '
				</div>
			</div>
			<div class="control-group control-group-profile">
				<label class="control-label" for="phone">'._('Phone').'</label>		
				<div class="input-group">
				<label for="username" class="input-group-addon">
					<span class="glyphicon glyphicon-earphone" aria-hidden="true"></span>
				</label>
				<input name="phone" type="text" class="form-control" id="phone" placeholder="'._('Enter your phone number (mobile)').'" value="'.$profilInfo->phone.'">
			</div>			
				
			</div>
			<div class="control-group control-group-profile">
				<label class="control-label" for="language">'._('Language').'</label>
				<div class="input-group">
		  			<label class="input-group-addon" for="username">
							<span aria-hidden="true" class="fa fa-language"></span>
					</label>
				<select id="selectLanguage" name="selectbasic" class="input-xlarge center form-control medium-input">';
				foreach ($language as $k => $lang){
				if ($k == $profilInfo->language)
					echo '<option value="'.$k.'" selected="selected">'.$lang.'</option>';
				else
					echo '<option value="'.$k.'">'.$lang.'</option>';
				
				}
				
		echo '</select>
			</div>
				</fieldset>
		<div class="control-group control-group-profile btn-saveProfile">
			<div class="controls save-button">
				<button onclick="saveProfile()" id="saveProfile" name="saveProfile" class="btn btn-greenleaf center">'._('Save').'</button>
			</div>
		</div>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 password">
		<fieldset class="center">
			<h2>'._('Change password').'</h2>
			<div id="alert1" class="alert alert-danger center" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				'._('Warning : Please fill the 3 password inputs !').'
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="control-group control-group-profile">
				<label class="control-label" for="oldPWD">'._('Old password').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
					</label>
					<input name="oldPWD" type="password" class="form-control" id="oldPWD" placeholder="'._('Type old password').'">
				</div>
			</div>
			<div class="control-group control-group-profile">
				<div id="alert2" class="alert alert-danger center" role="alert">
						<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
						'._('Warning : Your new password is too short ! (6 characters min)').'
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<label class="control-label" for="newPWD">'._('New password').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="fa fa-unlock-alt" aria-hidden="true"></span>
					</label>
					<input name="newPWD" type="password" class="form-control" id="newPWD" placeholder="'._('Type new password').'">
				</div>		
			</div>
			<div id="alert3" class="alert alert-danger center" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				'._('Warning : Passwords are not matching !').'
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="control-group control-group-profile">
				<label class="control-label" for="confirmPWD">'._('Retype new password').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="fa fa-unlock-alt" aria-hidden="true"></span>
					</label>
					<input name="confirmPWD" type="password" class="form-control" id="confirmPWD" placeholder="'._('Retype new password').'">
				</div>		
							
			</div>
			<div class="control-group control-group-profile btn-savePassword">
				<div class="controls save-button">
					<button onclick="savePassword()" id="savePassword" name="savePassword" class="btn btn-greenleaf center">'._('Save').'</button>
				</div>
			</div>
		</fieldset>
	</div>
</div>

<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
</div>
													
<script type="text/javascript">
							
$("#alert1").hide();
$("#alert2").hide();
$("#alert3").hide();

function loadingForm() {
	BootstrapDialog.show({
		title: \''._('Loading').'\',
   		message: \''._('Load in progress').'\'
	});
}

function savePassword() {
	$("#alert1").hide();
	$("#alert2").hide();
	$("#alert3").hide();
	var old = $("#oldPWD").val();
	var newPassword = $("#newPWD").val();
	var confirm = $("#confirmPWD").val();
	if (old != "" && newPassword != "" && confirm != "")
	{
		if (newPassword.length < 6)
		{
			$("#alert2").show();
		}
		else if (newPassword != confirm)
		{
			$("#alert3").show();
		}
		else
		{
			$.ajax({
				type:"GET",
				url: "/form/form_password_save.php",
				data: "old="+old+"&password="+newPassword+"&id=0",
				beforeSend: function(){
					loadingForm();
				},
				success: function(msg) {
					location.reload();
				}
			});
		}
	}
	else
	{
		$("#alert1").show();
	}
}

function saveProfile() {
	var lastname = $("#lastname").val();
	var firstname = $("#firstname").val();
	var language = $("#selectLanguage").val();
   				
   	alert("ok");
	if ($("#sexe-1").is(\':checked\'))
	{
		var gender = 1;
	}
	else
	{
		var gender = 0;
	}
	var phone = $("#phone").val();
	$.ajax({
		type:"GET",
		url: "/form/form_profile_save.php",
		data: "lastname="+lastname+"&firstname="+firstname+"&gender="+gender+"&phone="+phone+"&language="+language+"&id=0",
		beforeSend: function(){
			loadingForm();
		},
		success: function(msg) {
			location.reload();
		},
   		error: function(msg, status){
   		}
	});
}


</script>';
?>
