<?php 

include('header.php');

$request =  new Api();
$request -> add_request('profileList');
$result  =  $request -> send_request();

$listuser = $result->profileList;

echo '
	<div class="center">';
		printf(_('Do you want to delete %s?'), '<strong>'.$listuser->{$_GET['iduser']}->lastname.' '.$listuser->{$_GET['iduser']}->firstname.'</strong>');
	echo '</div>
	<div class="controls center">
		<button id="eventSave" onclick="DeleteUser('.$_GET['iduser'].')" class="btn btn-success">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>
</div>';


?>
