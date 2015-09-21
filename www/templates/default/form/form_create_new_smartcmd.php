<?php

include('header.php');

if (!empty($_GET['smartcmd_name'])) {
	$request =  new Api();
	$request -> add_request('createNewSmartcmd', array($_GET['smartcmd_name']));
	$result  =  $request -> send_request();

	if (empty($result->createNewSmartcmd) || $result->createNewSmartcmd == -1) {
		echo '-1';
	}
	else {
		echo $result->createNewSmartcmd;
	}
}
else {
	echo '-2';
}

?>