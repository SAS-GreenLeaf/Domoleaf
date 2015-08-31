<?php 

include('header.php');

if (empty($_POST['resetKeyval'])){
	$_POST['resetKeyval'] = '';
}

$request =  new Api();
$request -> add_request('confCheckResetKey', array($_POST['resetKeyval']));
$result  =  $request -> send_request();

echo $result->confCheckResetKey;

?>