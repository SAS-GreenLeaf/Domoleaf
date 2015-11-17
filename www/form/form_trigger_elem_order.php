<?php

include('header.php');

if (!empty($_GET['id_trigger']) && !empty($_GET['old_id_condition']) && !empty($_GET['new_id_condition'])) {

	$request = new Api();
	$request -> add_request('triggerChangeElemsOrder', array($_GET['id_trigger'],
												$_GET['old_id_condition'], $_GET['new_id_condition']));
	$result  =  $request -> send_request();
	
}
?>