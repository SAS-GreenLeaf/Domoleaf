<?php 

include('header.php');

if (!empty($_GET['fromMailval'])){
$request =  new Api();
$request -> add_request('confPreConfigurationMail', array($_GET['fromMailval']));
$result  =  $request -> send_request();

	if ($result->confPreConfigurationMail == '1'){
		echo '
			<br/><div class="alert alert-danger alert-dismissible center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
			'._('Your email is not listed in our database. Please, complete manually the configuration.').'</div>';
	}
	else if ($result->confPreConfigurationMail == '2'){
		echo '
			<br/><div class="alert alert-danger alert-dismissible center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
			'._('Your email is not valid. Please, enter a valid email.').'</div>';
	}
	else{
		echo $result->confPreConfigurationMail;
	}
}
else{
	echo '
		<br/><div class="alert alert-danger alert-dismissible center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
		'._('You must enter an email.').'</div>';
}

?>