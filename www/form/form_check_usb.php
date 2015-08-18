<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbCheckUsb');
$result  =  $request -> send_request();

if (!empty($result) && !empty($result->confDbCheckUsb)){
	echo $result->confDbCheckUsb;
}
else{
	echo 0;
}

?>