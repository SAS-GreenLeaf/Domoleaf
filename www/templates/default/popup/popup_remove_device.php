<?php 

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

if (!empty($_GET['iddevice'])){
	$request =  new Api();
	$request -> add_request('confRoomDeviceInfo', array($_GET['iddevice']));
	$result  =  $request -> send_request();
	
	$device = $result->confRoomDeviceInfo;

	echo '<div class="center">';
		printf(_('Do you want to delete %s?'), '<strong>'.$device->name.'</strong>');
	echo '</div><div class="center">
		<button onclick="RemoveDevice('.$_GET['iddevice'].')" class="btn btn-greenleaf">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>
	</div>';
}

?>