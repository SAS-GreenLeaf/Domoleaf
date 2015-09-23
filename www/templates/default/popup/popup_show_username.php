<?php

include('header.php');

if (empty($_GET['usernameval'])){
	$_GET['usernameval'] = '';
}

$request =  new Api();
$result  =  $request -> send_request();

echo '
	<div class="control-group margin-top">'.
		''._('You have succeeded to change your password, don\'t forget, your username is ').'<B>'.$_GET['usernameval'].'<B>'.
	'</div>'.
	'<br/><br/><div class="controls center">'.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	'</div>';


?>