<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listdaemon = $result->confDaemonList;

foreach ($listdaemon as $elem){
	if (!empty($elem->protocol)->{1}){
		echo '<option value="'.$elem->daemon_id.'">'.$elem->name.'</option>';
	}
}

?>