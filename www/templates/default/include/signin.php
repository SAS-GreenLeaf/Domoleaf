<?php 
echo '<div id="signin-logo">
	<img alt="logo" src="/templates/'.TEMPLATE.'/img/logo/logo_gl.png" />
</div>
<div class="container col-lg-4 col-md-6 col-sm-10 col-xs-12 col-lg-offset-4 col-md-offset-3 col-sm-offset-1 center">
	<form class="form-singin" role="form" method="post">
		<h2 class="form-signin-heading" id="center">'._('Sign in').'</h2>
		
		<div class="form-group">
			<div class="alert  alert-success" role="alert">
				<span class="glyphicon glyphicon-info-sign"></span>
				<span class="sr-only">'._('Info:').'</span>
				'._('Please enter the username and password to acces to the remote control').'
			</div>
			<div class="input-group">
				<label for="username" class="input-group-addon">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</label>
				<input name="username" type="text" class="form-control" id="username" placeholder="'._('Username').'">
			</div>
		</div>
		<div class="form-group">
			<div class="input-group">
				<label for="password" class="input-group-addon">
					<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
				</label>
				<input name="password" type="password" class="form-control" id="password" placeholder="'._('Password').'">
			</div>
			<p><a href="#" onclick="PopupPasswordLost()" >'._('Password lost').'</a></p>
		</div>
		<div class="form-group">
			<div>
				<button type="submit" class="btn btn-greenleaf">'._('Sign in').'</button>
			</div>
		</div>

		<div class="alert alert-danger alert-hidden alert-dismissible" role="alert" id ="signerr" >
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<span class="sr-only">'._('Error:').'</span>
				'.$login_error.'
		</div>
	</form>
</div>
<footer>
	<div class="col-xs-12">
		<p>'._('Develop by ').'<a href="http://www.greenleaf.fr" target="_blank">Greenleaf</a></p>
	</div>
</footer>';

if(!empty($login_error)){
	echo '<script type="text/javascript">
			$("#signerr").show();
			</script>';
}

echo '

<script type="text/javascript">

function PopupPasswordLost(){
	$.ajax({
		type: "GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_password_lost.php",
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Reset password').'",
				message: result
			});
		}
	});
}

function PopupPasswordReset(resetKeyval){
	popup_close();
	$.ajax({
		type: "POST",
		data: "resetKeyval="+encodeURIComponent(resetKeyval),
		url: "/templates/'.TEMPLATE.'/popup/popup_password_reset.php",
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Reset password').'",
				message: result
			});
		}
	});
}

function PopupPasswordReset(resetKeyval){
	popup_close();
	$.ajax({
		type: "POST",
		data: "resetKeyval="+encodeURIComponent(resetKeyval),
		url: "/templates/'.TEMPLATE.'/popup/popup_password_reset.php",
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Reset password').'",
				message: result
			});
		}
	});
}

function PopupShowUsername(usernameval){
	popup_close();
	$.ajax({
		type: "GET",
		data: "usernameval="+encodeURIComponent(usernameval),
		url: "/templates/'.TEMPLATE.'/popup/popup_show_username.php",
		success: function(result) {
			BootstrapDialog.show({
				title: "",
				message: result
			});
		}
	});
}

</script>';

?>