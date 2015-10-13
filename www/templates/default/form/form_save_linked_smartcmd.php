<?php

include('header.php');

if (!empty($_GET['trigger_id']) && !empty($_GET['smartcmd_id'])) {

	$request = new Api();
	$request -> add_request('triggerSaveLinkedSmartcmd', array($_GET['trigger_id'], $_GET['smartcmd_id']));
	$result  =  $request -> send_request();
	
	echo 0;
}
else {
	echo 1;
}

?>