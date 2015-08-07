<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listdaemon = $result->confDaemonList;

foreach ($listdaemon as $elem){
	if (in_array(1, $elem->protocol)){
		echo '<option value="'.$elem->daemon_id.'">'.$elem->name.'</option>';
	}
}

?>