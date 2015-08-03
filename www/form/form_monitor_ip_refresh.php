<?php
 
include('header.php');

$request =  new Api();
$request -> add_request('monitorIpRefresh');
$result  =  $request -> send_request();

?>