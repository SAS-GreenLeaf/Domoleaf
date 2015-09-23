<?php

include('header.php');

if (!empty($_GET['id_smartcmd']) && !empty($_GET['old_id_exec']) && !empty($_GET['new_id_exec'])) {

	$request = new Api();
	$request -> add_request('smartcmdChangeElemsOrder', array($_GET['id_smartcmd'],
												$_GET['old_id_exec'], $_GET['new_id_exec']));
	$result  =  $request -> send_request();
	
}
?>