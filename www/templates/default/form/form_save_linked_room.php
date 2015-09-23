<?php

include('header.php');

if (!empty($_GET['smartcmd_id'])) {

	$request = new Api();
	$request -> add_request('smartcmdSaveLinkedRoom', array($_GET['smartcmd_id'], $_GET['room_id']));
	$result  =  $request -> send_request();
	
	echo 0;
}
else {
	echo 1;
}

?>