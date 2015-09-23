<?php 

include('header.php');

if (empty($_POST['resetKeyval'])){
	$_POST['resetKeyval'] = '';
}

if (empty($_POST['newPasswordval'])){
	$_POST['newPasswordval'] = '';
}

if (empty($_POST['newPasswordBisval'])){
	$_POST['newPasswordBisval'] = '';
}

if ($_POST['resetKeyval'] == '' || $_POST['newPasswordval'] == '' || $_POST['newPasswordBisval'] == ''){
	echo '
		<div class="alert alert-danger alert-dismissible center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
		'._('All fields must be fill').'</div>';
}
elseif ($_POST['newPasswordval'] != $_POST['newPasswordBisval']){
	echo '
		<div class="alert alert-danger alert-dismissible center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
		'._('The passwords are not the same').'</div>';
}
elseif (strlen(utf8_decode($_POST['newPasswordval'])) < 6){
	echo '
		<div class="alert alert-danger alert-dismissible center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
		'._('Your new password is too short ! (6 characters min)').'</div>';
}
else{
	$request =  new Api();
	$request -> add_request('confResetPassword', array($_POST['resetKeyval'], $_POST['newPasswordval']));
	$result  =  $request -> send_request();
	
	if ($result->confResetPassword){
		echo $result->confResetPassword;
	}
	else{
		echo '
			<div class="alert alert-danger alert-dismissible center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
			'._('Your reset key is not good').'</div>';
	}
	
}

?>