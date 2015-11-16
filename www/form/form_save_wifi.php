<?php
include('header.php');

$request =  new Api();
$request -> add_request('confSaveWifi', array($_GET['daemon_id'], $_GET['ssid'], $_GET['password'], $_GET['security'], $_GET['mode']));
$result  =  $request -> send_request();

echo $result->confSaveWifi;

?>