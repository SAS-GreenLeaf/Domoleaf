<?php 

include('header.php');

if (empty($_GET['dayval'])){
	$_GET['dayval'] = '';
}

if (empty($_GET['monthval'])) {
	$_GET['monthval'] = '';
}

if (empty($_GET['yearval'])){
	$_GET['yearval'] = '';
}

if (empty($_GET['hourval'])) {
	$_GET['hourval'] = 0;
}
if (empty($_GET['minuteval'])){
	$_GET['minuteval'] = 0;
}

$request =  new Api();
$request -> add_request('confDateTime', array($_GET['dayval'], $_GET['monthval'], $_GET['yearval'], $_GET['hourval'],$_GET['minuteval']));
$result  =  $request -> send_request();

?>