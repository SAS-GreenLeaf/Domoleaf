<?php

include('header.php');

if (!empty($_GET['product_id'])){
	$request =  new Api();
	$request -> add_request('confProductOptionList', array($_GET['product_id']));
	$result  =  $request -> send_request();

	echo json_encode($result->confProductOptionList);
}

?>
