<?php 

$request =  new Api();
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

$menuProtocol = $result->confMenuProtocol;

?>