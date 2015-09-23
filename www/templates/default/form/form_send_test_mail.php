<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confSendTestMail');
$result  =  $request -> send_request();

if (!empty($result) && !empty($result->confSendTestMail && $result->confSendTestMail == 'Error')){
	echo ' 
		<div class="alert alert-danger alert-dismissible center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
		'._('An error occured for send mail. Please, check your configuration mail.').'</div>'; 
}
else{
	echo '
		<div class="alert alert-success alert-dismissible center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="'._('Close').'"><span aria-hidden="true">&times;</span></button>
		'._('The mail have been send.').'</div>';
}

?>