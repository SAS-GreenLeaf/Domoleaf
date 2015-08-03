<?php

include('header.php');

$request =  new Api();
$request -> add_request('confFloorRemove', array($_GET['floor']));
$result  =  $request -> send_request();

?>