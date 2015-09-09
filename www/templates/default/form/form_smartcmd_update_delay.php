<?php

include('header.php');

if (!empty($_GET['smartcmd_id']) && !empty($_GET['exec_id']) && !empty($_GET['delay'])) {
	
	$request =  new Api();
	$request -> add_request('smartcmdUpdateDelay',
							array($_GET['smartcmd_id'], $_GET['exec_id'], $_GET['delay']));
	$result  =  $request -> send_request();
}

?>