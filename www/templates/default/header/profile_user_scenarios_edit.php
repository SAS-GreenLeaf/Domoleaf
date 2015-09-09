<?php

include('templates/default/function/display_widget.php');

if (empty($_GET['id_smartcmd'])) {
	redirect();
}

$id_smartcmd = $_GET['id_smartcmd'];

$request = new Api();
$request -> add_request('searchSmartcmdById',array($id_smartcmd));
$request -> add_request('confUserInstallation',array(''));
$result  =  $request -> send_request();


$installation_info = $result->confUserInstallation;
$name_smartcmd = $result->searchSmartcmdById;

if(empty($installation_info) || empty($name_smartcmd)) {
	redirect();
}

?>