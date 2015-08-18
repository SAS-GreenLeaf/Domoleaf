<?php 

include('header.php');

if (empty($_POST['destinatorMailval'])){
	$_POST['destinatorMailval'] = '';
}

if (empty($_POST['objectMailval'])){
	$_POST['objectMailval'] = '';
}

if (empty($_POST['messageMailval'])){
	$_POST['messageMailval'] = '';
}

$request =  new Api();
$request -> add_request('confSendMail', array($_POST['destinatorMailval'], $_POST['objectMailval'], $_POST['messageMailval']));
$result  =  $request -> send_request();

?>