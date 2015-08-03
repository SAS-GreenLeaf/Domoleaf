<?php

include('header.php');

$request =  new Api();
$request -> add_request('profileList');

if (!empty($_GET['iduser'])){
	$request -> add_request('profileRemove', array($_GET['iduser']));
}

$result  =  $request -> send_request();

?>
