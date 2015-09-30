<?php 

include('header.php');

if (!empty($_GET['idVersion'])) {
	$request =  new Api();
	$request -> add_request('conf_load');
	$result  =  $request -> send_request();
	
	$conf_infos = $result->conf_load;
	$newVersion = $conf_infos->{$_GET['idVersion']}->configuration_value;

	echo $newVersion;
}

?>