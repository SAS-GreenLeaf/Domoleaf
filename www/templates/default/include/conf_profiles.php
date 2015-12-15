<?php  

include('configuration-menu.php');

echo '
<div class="col-lg-12 col-lg-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
	echo '
	<div class="col-lg-5 col-sm-12 col-xs-12">
		<fieldset class="center">
			<h2>'._('Personal informations').'</h2>
			<div class="control-group control-group-profile">
				<label class="control-label" for="username">'._('Username').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
					</label>
					<input name="username" type="text" class="form-control" id="lastname" placeholder="'._('Enter your username name').'" value="'.$profilInfo->username.'">
				</div>
							
				<label class="control-label" for="lastname">'._('Last Name').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
					</label>
					<input name="lastname" type="text" class="form-control" id="lastname" placeholder="'._('Enter your last name').'" value="'.$profilInfo->lastname.'">
				</div>
			
				<label class="control-label" for="firstname">'._('First Name').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
					</label>
					<input name="firstname" type="text" class="form-control" id="firstname" placeholder="'._('Enter your first name').'" value="'.$profilInfo->firstname.'">
				</div>
							
				<label class="control-label" for="sexe">'._('Gender').'</label>
				<div class="controls">';
					if ($profilInfo->gender == 0){
						echo '
						<input name="sexe" id="sexe-0" value="0" checked="checked" type="radio">
						'._('Male').'&nbsp;&nbsp;
						 <input name="sexe" id="sexe-1" value="1" type="radio">
						'._('Female');
					}
					else{
						echo '
						<input name="sexe" id="sexe-0" value="0" type="radio">
						'._('Male').'&nbsp;&nbsp;
						<input name="sexe" id="sexe-1" value="1" type="radio" checked="checked" >
						'._('Female');
					}
				echo '
				</div>
				
				<label class="control-label" for="email">'._('Email').'</label>
				<div class="input-group">
					<label for="email" class="input-group-addon">@</label>
					<input name="email" type="email" class="form-control" id="email" placeholder="'._('Enter your email').'" value="'.$profilInfo->mcuser_mail.'">
				</div>

				<label class="control-label" for="phone">'._('Phone').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-earphone" aria-hidden="true"></span>
					</label>
					<input name="phone" type="text" class="form-control" id="phone" placeholder="'._('Enter your phone number (mobile)').'" value="'.$profilInfo->phone.'">
				</div>
							
				<label class="control-label" for="language">'._('Language').'</label>
				<div class="input-group">
					<label class="input-group-addon" for="username">
						<span aria-hidden="true" class="fa fa-language"></span>
					</label>
					<select id="selectLanguage" name="selectbasic" class="input-xlarge center form-control selectpicker medium-input">';
						foreach ($language as $k => $lang){
							if ($k == $profilInfo->language)
								echo '<option value="'.$k.'" selected="selected">'.$lang.'</option>';
							else
								echo '<option value="'.$k.'">'.$lang.'</option>';
						
						}
					echo '
					</select>
				</div>
				
				<label class="control-label" for="timeZone">'._('Time zone').'</label>
				<div class="input-group">
		  			<label class="input-group-addon" for="timeZone">
							<span aria-hidden="true" class=" glyphicon glyphicon-time"></span>
					</label>
					<select id="selectTimeZone" name="selectbasic" class="input-xlarge center form-control selectpicker medium-input">';
					foreach ($allTimeZone as $k => $timeZone){
						if ($k == $profilInfo->timezone){
							echo '<option value="'.$k.'" selected="selected">'.$timeZone.'</option>';
						}
						else
							echo '<option value="'.$k.'">'.$timeZone.'</option>';
					}
					echo
					'</select>
				</div>';

				if ($currentuser != $userid){
					echo '<label class="control-label" for="lvl">'._('Level').'</label>
					<div class="input-group">
						<label class="input-group-addon" for="lvl">
							<span aria-hidden="true" class="fa fa-graduation-cap"></span>
						</label>
						<select id="selectLvl" name="selectlvl" class="input-xlarge center form-control selectpicker medium-input">';
						if ($profilInfo->mcuser_level == 1){
							echo '<option value="1" selected="selected">'._('User').'</option>
								  <option value="3">'._('Admin').'</option>';
						}
						else{
							echo '<option value="1">'._('User').'</option>
								  <option value="3" selected="selected">'._('Admin').'</option>';
						}
						echo '</select>
					</div>';
				}
		echo '</div>
		</fieldset>
		<div class="control-group control-group-profile btn-saveProfile">
			<div class="controls save-button">
				<button onclick="saveProfile()" id="saveProfile" name="saveProfile" class="btn btn-greenleaf center">'._('Save').'</button>
			</div>
		</div>
	</div>
	<div class="col-lg-5 col-sm-12 col-xs-12 password">
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
							
				<div id="alert2" class="alert alert-danger center" role="alert">
						<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
						'._('Warning : Your new password is too short ! (6 characters min)').'
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
								
				<label class="control-label" for="newPWD">'._('New password').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
					</label>
					<input name="newPWD" type="password" class="form-control" id="newPWD" placeholder="'._('Type new password').'">
				</div>
							
				<div id="alert3" class="alert alert-danger center" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					'._('Warning : Passwords are not matching !').'
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
							
				<label class="control-label" for="confirmPWD">'._('Retype new password').'</label>
				<div class="input-group">
					<label for="username" class="input-group-addon">
						<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
					</label>
					<input name="confirmPWD" type="password" class="form-control" id="confirmPWD" placeholder="'._('Retype new password').'">
				</div>
				
				<div class="controls save-button">
					<button onclick="savePassword()" id="savePassword" name="savePassword" class="btn btn-greenleaf center">'._('Save').'</button>
				</div>
			</div>
		</fieldset>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function(){
	activateMenuElem(\'users\');
});

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
   	var timeZone = $("#selectTimeZone").val();
   	var level = $("#selectLvl").val();

	if ($("#sexe-1").is(\':checked\'))
	{
		var gender = 1;
	}
	else
	{
		var gender = 0;
	}
   	var email = $("#email").val();
	var phone = $("#phone").val();
	$.ajax({
		type:"GET",
		url: "/form/form_profile_save.php",
		data: "lastname="+lastname+"&firstname="+firstname+"&gender="+gender+"&phone="+phone+"&email="+encodeURIComponent(email)+"&language="+language+"&timeZone="+timeZone+"&level="+level+"&id="+'.$userid.',
		beforeSend: function(){
			loadingForm();
		},
		success: function(msg) {
			location.href="/conf_users";
		}
	});
}


</script>';
?>
