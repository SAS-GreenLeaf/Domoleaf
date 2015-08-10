<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbCreateUsb');
$result  =  $request -> send_request();

?>