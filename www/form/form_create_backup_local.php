<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbCreateLocal');
$result  =  $request -> send_request();

?>