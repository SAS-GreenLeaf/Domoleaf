<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbCheckUsb');
$result  =  $request -> send_request();

echo $result->confDbCheckUsb;

?>